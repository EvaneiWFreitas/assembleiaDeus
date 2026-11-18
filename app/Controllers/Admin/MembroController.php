<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Audit;
use App\Core\Auth;
use App\Core\Flash;
use App\Models\Membro;

/**
 * Cadastro de membros.
 */
final class MembroController extends BasePessoaController
{
    private const ROTA = 'membros';

    public function index(): void
    {
        $this->guard('membros.ver');
        $this->limparOld();

        $filtros = $this->filtrosDaRequisicao();

        $this->renderAdmin('admin.estrutura.index', [
            'title'              => 'Membros',
            'titulo'             => 'Membros',
            'descricao'          => 'Consulte e mantenha o cadastro de membros da igreja.',
            'itens'              => Membro::all($filtros),
            'rota'               => 'admin/' . self::ROTA,
            'permissoes'         => $this->permissoes(),
            'nota'               => 'A emissão de carteirinhas e fichas impressas será disponibilizada na etapa de documentos.',
            'colunas'            => $this->colunas(),
            'filtrosFormulario'  => $this->filtrosFormulario('admin/membros', $filtros, $this->situacoes()),
        ]);
    }

    public function criarForm(): void
    {
        $this->guard('membros.criar');

        $this->renderAdmin('admin.estrutura.form', [
            'title'     => 'Novo membro',
            'titulo'    => 'Novo membro',
            'subtitulo' => 'Preencha os dados pessoais, contato e vínculo com a congregação.',
            'acao'      => url('admin/membros'),
            'voltar'    => 'admin/membros',
            'botao'     => 'Cadastrar membro',
            'registro'  => [],
            'campos'    => $this->campos(),
        ]);
    }

    public function criar(): void
    {
        $this->guard('membros.criar');

        $dados = $this->normalizar();
        $erros = $this->validar($dados);

        if ($erros !== []) {
            $this->guardarOld($dados);
            $this->falhar($erros, 'membros/novo');
        }

        $id = Membro::create($dados);
        Audit::record('criar', 'membros', $dados, [], 'membros', $id);
        $this->limparOld();
        Flash::success('Membro cadastrado com sucesso.');
        $this->redirectAdmin('membros');
    }

    public function editarForm(string $id): void
    {
        $this->guard('membros.editar');

        $membro = Membro::find((int) $id);
        if ($membro === null) {
            Flash::error('Membro não encontrado.');
            $this->redirectAdmin('membros');
        }

        $this->renderAdmin('admin.estrutura.form', [
            'title'     => 'Editar membro',
            'titulo'    => 'Editar membro',
            'subtitulo' => 'Atualize os dados cadastrais do membro.',
            'acao'      => url('admin/membros/' . (int) $id . '/editar'),
            'voltar'    => 'admin/membros',
            'botao'     => 'Salvar alterações',
            'registro'  => $membro,
            'campos'    => $this->campos(),
        ]);
    }

    public function editar(string $id): void
    {
        $this->guard('membros.editar');

        $membro = Membro::find((int) $id);
        if ($membro === null) {
            Flash::error('Membro não encontrado.');
            $this->redirectAdmin('membros');
        }

        $dados = $this->normalizar();
        $erros = $this->validar($dados, (int) $id);

        if ($erros !== []) {
            $this->guardarOld($dados);
            $this->falhar($erros, 'membros/' . (int) $id . '/editar');
        }

        Membro::update((int) $id, $dados);
        Audit::record('editar', 'membros', $dados, $membro, 'membros', (string) (int) $id);
        $this->limparOld();
        Flash::success('Membro atualizado com sucesso.');
        $this->redirectAdmin('membros');
    }

    public function excluir(string $id): void
    {
        $this->guard('membros.excluir');

        $membro = Membro::find((int) $id);
        if ($membro === null) {
            Flash::error('Membro não encontrado.');
            $this->redirectAdmin('membros');
        }

        Membro::delete((int) $id);
        Audit::record('excluir', 'membros', [], $membro, 'membros', (string) (int) $id);
        Flash::success('Membro excluído com sucesso.');
        $this->redirectAdmin('membros');
    }

    private function validar(array $dados, ?int $ignorarId = null): array
    {
        $erros = $this->validarPessoaComum($dados, [
            'codigo'         => 'o código',
            'nome_completo'  => 'o nome completo',
            'congregacao_id' => 'a congregação',
        ], static fn (int $id): bool => Membro::congregacaoPermitida($id));

        if (!in_array($dados['situacao'], array_keys($this->situacoes()), true)) {
            $erros[] = 'Selecione uma situação válida.';
        }
        if (!in_array($dados['sexo'], array_keys($this->sexos()), true)) {
            $erros[] = 'Selecione um sexo válido.';
        }
        if (!in_array($dados['estado_civil'], array_keys($this->estadosCivis()), true)) {
            $erros[] = 'Selecione um estado civil válido.';
        }
        if ($dados['codigo'] !== null && Membro::codigoExiste($dados['codigo'], $ignorarId)) {
            $erros[] = 'Este código já está em uso.';
        }
        if ($dados['cpf'] !== null && Membro::cpfExiste($dados['cpf'], $ignorarId)) {
            $erros[] = 'Este CPF já está cadastrado.';
        }

        return $erros;
    }

    private function normalizar(): array
    {
        return [
            'codigo'            => $this->texto($this->request->post('codigo'), 20),
            'nome_completo'     => $this->texto($this->request->post('nome_completo'), 180),
            'foto'              => $this->texto($this->request->post('foto'), 255),
            'cpf'               => $this->cpf($this->request->post('cpf')),
            'rg'                => $this->texto($this->request->post('rg'), 20),
            'data_nascimento'   => $this->data($this->request->post('data_nascimento')),
            'sexo'              => $this->texto($this->request->post('sexo'), 20) ?? 'NAO_INFORMADO',
            'estado_civil'      => $this->texto($this->request->post('estado_civil'), 30) ?? 'NAO_INFORMADO',
            'telefone'          => $this->texto($this->request->post('telefone'), 20),
            'whatsapp'          => $this->texto($this->request->post('whatsapp'), 20),
            'email'             => $this->texto($this->request->post('email'), 190),
            'endereco'          => $this->texto($this->request->post('endereco'), 255),
            'numero'            => $this->texto($this->request->post('numero'), 10),
            'complemento'       => $this->texto($this->request->post('complemento'), 100),
            'bairro'            => $this->texto($this->request->post('bairro'), 100),
            'cidade'            => $this->texto($this->request->post('cidade'), 100),
            'estado'            => $this->estado($this->request->post('estado')),
            'cep'               => $this->texto($this->request->post('cep'), 9),
            'data_ingresso'     => $this->data($this->request->post('data_ingresso')),
            'igreja_origem_id'  => $this->inteiro($this->request->post('igreja_origem_id')),
            'congregacao_id'    => $this->inteiro($this->request->post('congregacao_id')),
            'data_batismo'      => $this->data($this->request->post('data_batismo')),
            'situacao'          => $this->texto($this->request->post('situacao'), 20) ?? 'ATIVO',
            'observacoes'       => $this->texto($this->request->post('observacoes')),
            'created_by'        => Auth::id(),
            'updated_by'        => Auth::id(),
        ];
    }

    private function campos(): array
    {
        return [
            ['name' => 'codigo', 'label' => 'Código', 'type' => 'text', 'required' => true, 'maxlength' => 20, 'col' => 'col-md-4', 'default' => Membro::proximoCodigo()],
            ['name' => 'nome_completo', 'label' => 'Nome completo', 'type' => 'text', 'required' => true, 'maxlength' => 180, 'col' => 'col-md-8'],
            ['name' => 'cpf', 'label' => 'CPF', 'type' => 'text', 'maxlength' => 14, 'col' => 'col-md-3', 'placeholder' => '000.000.000-00'],
            ['name' => 'rg', 'label' => 'RG', 'type' => 'text', 'maxlength' => 20, 'col' => 'col-md-3'],
            ['name' => 'data_nascimento', 'label' => 'Data de nascimento', 'type' => 'date', 'col' => 'col-md-3'],
            ['name' => 'foto', 'label' => 'Foto (caminho ou URL)', 'type' => 'text', 'maxlength' => 255, 'col' => 'col-md-3'],
            ['name' => 'sexo', 'label' => 'Sexo', 'type' => 'select', 'col' => 'col-md-3', 'default' => 'NAO_INFORMADO', 'options' => $this->sexos()],
            ['name' => 'estado_civil', 'label' => 'Estado civil', 'type' => 'select', 'col' => 'col-md-3', 'default' => 'NAO_INFORMADO', 'options' => $this->estadosCivis()],
            ['name' => 'telefone', 'label' => 'Telefone', 'type' => 'tel', 'maxlength' => 20, 'col' => 'col-md-3'],
            ['name' => 'whatsapp', 'label' => 'WhatsApp', 'type' => 'tel', 'maxlength' => 20, 'col' => 'col-md-3'],
            ['name' => 'email', 'label' => 'E-mail', 'type' => 'email', 'maxlength' => 190, 'col' => 'col-md-3'],
            ['name' => 'congregacao_id', 'label' => 'Congregação', 'type' => 'select', 'required' => true, 'col' => 'col-md-6', 'options' => $this->congregacoesOptions(), 'placeholder' => 'Selecione a congregação...'],
            ['name' => 'igreja_origem_id', 'label' => 'Igreja de origem', 'type' => 'select', 'col' => 'col-md-6', 'options' => $this->igrejasOptions(), 'placeholder' => 'Selecione a igreja de origem...'],
            ['name' => 'data_ingresso', 'label' => 'Data de ingresso', 'type' => 'date', 'col' => 'col-md-3'],
            ['name' => 'data_batismo', 'label' => 'Data do batismo', 'type' => 'date', 'col' => 'col-md-3'],
            [
                'name' => 'situacao', 'label' => 'Situação', 'type' => 'select', 'required' => true, 'col' => 'col-md-3',
                'default' => 'ATIVO', 'options' => $this->situacoes(),
            ],
            ['name' => 'endereco', 'label' => 'Endereço', 'type' => 'text', 'maxlength' => 255, 'col' => 'col-12'],
            ['name' => 'numero', 'label' => 'Número', 'type' => 'text', 'maxlength' => 10, 'col' => 'col-md-2'],
            ['name' => 'complemento', 'label' => 'Complemento', 'type' => 'text', 'maxlength' => 100, 'col' => 'col-md-4'],
            ['name' => 'bairro', 'label' => 'Bairro', 'type' => 'text', 'maxlength' => 100, 'col' => 'col-md-6'],
            ['name' => 'cidade', 'label' => 'Cidade', 'type' => 'text', 'maxlength' => 100, 'col' => 'col-md-6'],
            ['name' => 'estado', 'label' => 'Estado (UF)', 'type' => 'text', 'maxlength' => 2, 'col' => 'col-md-2', 'placeholder' => 'PB'],
            ['name' => 'cep', 'label' => 'CEP', 'type' => 'text', 'maxlength' => 9, 'col' => 'col-md-4', 'placeholder' => '00000-000'],
            ['name' => 'observacoes', 'label' => 'Observações', 'type' => 'textarea', 'col' => 'col-12', 'rows' => 3],
        ];
    }

    private function colunas(): array
    {
        return [
            ['label' => 'Membro', 'key' => 'nome_completo', 'tipo' => 'principal', 'subtitulo' => 'codigo'],
            ['label' => 'CPF', 'key' => 'cpf', 'tipo' => 'cpf'],
            ['label' => 'Congregação', 'key' => 'congregacao_nome'],
            ['label' => 'Ingresso', 'key' => 'data_ingresso', 'tipo' => 'data'],
            ['label' => 'Telefone', 'key' => 'telefone'],
            [
                'label'   => 'Situação',
                'key'     => 'situacao',
                'tipo'    => 'badge',
                'rotulos' => $this->situacoes(),
                'cores'   => ['ATIVO' => 'success', 'INATIVO' => 'secondary', 'TRANSFERIDO' => 'info', 'FALECIDO' => 'dark'],
            ],
        ];
    }

    private function sexos(): array
    {
        return [
            'MASCULINO'    => 'Masculino',
            'FEMININO'     => 'Feminino',
            'NAO_INFORMADO' => 'Não informado',
        ];
    }

    private function estadosCivis(): array
    {
        return [
            'SOLTEIRO'      => 'Solteiro(a)',
            'CASADO'        => 'Casado(a)',
            'DIVORCIADO'    => 'Divorciado(a)',
            'VIUVO'         => 'Viúvo(a)',
            'UNIAO_ESTAVEL' => 'União estável',
            'NAO_INFORMADO' => 'Não informado',
        ];
    }

    private function situacoes(): array
    {
        return [
            'ATIVO'      => 'Ativo',
            'INATIVO'    => 'Inativo',
            'TRANSFERIDO' => 'Transferido',
            'FALECIDO'   => 'Falecido',
        ];
    }

    private function permissoes(): array
    {
        return [
            'criar'   => 'membros.criar',
            'editar'  => 'membros.editar',
            'excluir' => 'membros.excluir',
        ];
    }
}
