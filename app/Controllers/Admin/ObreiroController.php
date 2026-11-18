<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Audit;
use App\Core\Auth;
use App\Core\Flash;
use App\Models\ArquivoMorto;
use App\Models\CargoMinisterial;
use App\Models\Obreiro;

/**
 * Cadastro de obreiros.
 */
final class ObreiroController extends BasePessoaController
{
    private const ROTA = 'obreiros';

    public function index(): void
    {
        $this->guard('obreiros.ver');
        $this->limparOld();

        $filtros = $this->filtrosDaRequisicao();
        $cargos = [];
        foreach (CargoMinisterial::options() as $cargo) {
            $cargos[(int) $cargo['id']] = $cargo['nome'];
        }

        $this->renderAdmin('admin.estrutura.index', [
            'title'             => 'Obreiros',
            'titulo'            => 'Obreiros',
            'descricao'         => 'Consulte e mantenha o cadastro de obreiros e seus cargos ministeriais.',
            'itens'             => Obreiro::all($filtros),
            'rota'              => 'admin/' . self::ROTA,
            'permissoes'        => $this->permissoes(),
            'nota'              => 'A emissão de diplomas e o histórico ministerial serão disponibilizados em etapas específicas.',
            'colunas'           => $this->colunas(),
            'filtrosFormulario' => $this->filtrosFormulario('admin/obreiros', $filtros, $this->situacoes(), [
                [
                    'name' => 'cargo_id', 'label' => 'Cargo', 'type' => 'select', 'col' => 'col-md-3',
                    'value' => $filtros['cargo_id'] ?? '', 'options' => $cargos,
                ],
            ]),
        ]);
    }

    public function criarForm(): void
    {
        $this->guard('obreiros.criar');

        $this->renderAdmin('admin.estrutura.form', [
            'title'     => 'Novo obreiro',
            'titulo'    => 'Novo obreiro',
            'subtitulo' => 'Informe os dados pessoais, o cargo ministerial e o vínculo com a congregação.',
            'acao'      => url('admin/obreiros'),
            'voltar'    => 'admin/obreiros',
            'botao'     => 'Cadastrar obreiro',
            'registro'  => [],
            'campos'    => $this->campos(),
        ]);
    }

    public function criar(): void
    {
        $this->guard('obreiros.criar');

        $dados = $this->normalizar();
        $erros = $this->validar($dados);

        if ($erros !== []) {
            $this->guardarOld($dados);
            $this->falhar($erros, 'obreiros/novo');
        }

        $id = Obreiro::create($dados);
        Audit::record('criar', 'obreiros', $dados, [], 'obreiros', $id);
        $this->limparOld();
        Flash::success('Obreiro cadastrado com sucesso.');
        $this->redirectAdmin('obreiros');
    }

    public function editarForm(string $id): void
    {
        $this->guard('obreiros.editar');

        $obreiro = Obreiro::find((int) $id);
        if ($obreiro === null) {
            Flash::error('Obreiro não encontrado.');
            $this->redirectAdmin('obreiros');
        }

        $this->renderAdmin('admin.estrutura.form', [
            'title'     => 'Editar obreiro',
            'titulo'    => 'Editar obreiro',
            'subtitulo' => 'Atualize os dados cadastrais e ministeriais do obreiro.',
            'acao'      => url('admin/obreiros/' . (int) $id . '/editar'),
            'voltar'    => 'admin/obreiros',
            'botao'     => 'Salvar alterações',
            'registro'  => $obreiro,
            'campos'    => $this->campos(),
        ]);
    }

    public function editar(string $id): void
    {
        $this->guard('obreiros.editar');

        $obreiro = Obreiro::find((int) $id);
        if ($obreiro === null) {
            Flash::error('Obreiro não encontrado.');
            $this->redirectAdmin('obreiros');
        }

        $dados = $this->normalizar();
        $erros = $this->validar($dados, (int) $id);

        if ($erros !== []) {
            $this->guardarOld($dados);
            $this->falhar($erros, 'obreiros/' . (int) $id . '/editar');
        }

        Obreiro::update((int) $id, $dados);
        Audit::record('editar', 'obreiros', $dados, $obreiro, 'obreiros', (string) (int) $id);
        $this->limparOld();
        Flash::success('Obreiro atualizado com sucesso.');
        $this->redirectAdmin('obreiros');
    }

    public function excluir(string $id): void
    {
        $this->guard('obreiros.excluir');

        $obreiro = Obreiro::find((int) $id);
        if ($obreiro === null) {
            Flash::error('Obreiro não encontrado.');
            $this->redirectAdmin('obreiros');
        }
        if (ArquivoMorto::estaArquivado((int) $id)) {
            Flash::error('Não é possível excluir um obreiro preservado no arquivo morto.');
            $this->redirectAdmin('obreiros');
        }

        Obreiro::delete((int) $id);
        Audit::record('excluir', 'obreiros', [], $obreiro, 'obreiros', (string) (int) $id);
        Flash::success('Obreiro excluído com sucesso.');
        $this->redirectAdmin('obreiros');
    }

    private function validar(array $dados, ?int $ignorarId = null): array
    {
        $erros = $this->validarPessoaComum($dados, [
            'codigo'         => 'o código',
            'nome_completo'  => 'o nome completo',
            'congregacao_id' => 'a congregação',
            'cargo_id'       => 'o cargo ministerial',
        ], static fn (int $id): bool => Obreiro::congregacaoPermitida($id));

        if (!in_array($dados['situacao'], array_keys($this->situacoes()), true)) {
            $erros[] = 'Selecione uma situação válida.';
        }
        if ($dados['cargo_id'] !== null && !CargoMinisterial::existe($dados['cargo_id'])) {
            $erros[] = 'Selecione um cargo ministerial válido.';
        }
        if ($dados['codigo'] !== null && Obreiro::codigoExiste($dados['codigo'], $ignorarId)) {
            $erros[] = 'Este código já está em uso.';
        }
        if ($dados['cpf'] !== null && Obreiro::cpfExiste($dados['cpf'], $ignorarId)) {
            $erros[] = 'Este CPF já está cadastrado.';
        }
        if ($dados['numero_registro_ministerial'] !== null
            && Obreiro::registroExiste($dados['numero_registro_ministerial'], $ignorarId)) {
            $erros[] = 'Este número de registro ministerial já está em uso.';
        }
        if ($dados['usuario_id'] !== null && Obreiro::usuarioVinculado($dados['usuario_id'], $ignorarId)) {
            $erros[] = 'Este usuário já está vinculado a outro obreiro.';
        }

        return $erros;
    }

    private function normalizar(): array
    {
        return [
            'codigo'                      => $this->texto($this->request->post('codigo'), 20),
            'nome_completo'               => $this->texto($this->request->post('nome_completo'), 180),
            'foto'                        => $this->texto($this->request->post('foto'), 255),
            'cpf'                         => $this->cpf($this->request->post('cpf')),
            'rg'                          => $this->texto($this->request->post('rg'), 20),
            'data_nascimento'             => $this->data($this->request->post('data_nascimento')),
            'telefone'                    => $this->texto($this->request->post('telefone'), 20),
            'whatsapp'                    => $this->texto($this->request->post('whatsapp'), 20),
            'email'                       => $this->texto($this->request->post('email'), 190),
            'endereco'                    => $this->texto($this->request->post('endereco'), 255),
            'numero'                      => $this->texto($this->request->post('numero'), 10),
            'complemento'                 => $this->texto($this->request->post('complemento'), 100),
            'bairro'                      => $this->texto($this->request->post('bairro'), 100),
            'cidade'                      => $this->texto($this->request->post('cidade'), 100),
            'estado'                      => $this->estado($this->request->post('estado')),
            'cep'                         => $this->texto($this->request->post('cep'), 9),
            'igreja_origem_id'            => $this->inteiro($this->request->post('igreja_origem_id')),
            'congregacao_id'              => $this->inteiro($this->request->post('congregacao_id')),
            'cargo_id'                    => $this->inteiro($this->request->post('cargo_id')),
            'data_consagracao'            => $this->data($this->request->post('data_consagracao')),
            'numero_registro_ministerial' => $this->texto($this->request->post('numero_registro_ministerial'), 40),
            'situacao'                    => $this->texto($this->request->post('situacao'), 20) ?? 'ATIVO',
            'observacoes'                 => $this->texto($this->request->post('observacoes')),
            'usuario_id'                  => $this->inteiro($this->request->post('usuario_id')),
            'created_by'                  => Auth::id(),
            'updated_by'                  => Auth::id(),
        ];
    }

    private function campos(): array
    {
        $cargos = [];
        foreach (CargoMinisterial::options() as $cargo) {
            $cargos[(int) $cargo['id']] = $cargo['nome'];
        }

        return [
            ['name' => 'codigo', 'label' => 'Código', 'type' => 'text', 'required' => true, 'maxlength' => 20, 'col' => 'col-md-4', 'default' => Obreiro::proximoCodigo()],
            ['name' => 'nome_completo', 'label' => 'Nome completo', 'type' => 'text', 'required' => true, 'maxlength' => 180, 'col' => 'col-md-8'],
            ['name' => 'cpf', 'label' => 'CPF', 'type' => 'text', 'maxlength' => 14, 'col' => 'col-md-3', 'placeholder' => '000.000.000-00'],
            ['name' => 'rg', 'label' => 'RG', 'type' => 'text', 'maxlength' => 20, 'col' => 'col-md-3'],
            ['name' => 'data_nascimento', 'label' => 'Data de nascimento', 'type' => 'date', 'col' => 'col-md-3'],
            ['name' => 'foto', 'label' => 'Foto (caminho ou URL)', 'type' => 'text', 'maxlength' => 255, 'col' => 'col-md-3'],
            ['name' => 'congregacao_id', 'label' => 'Congregação', 'type' => 'select', 'required' => true, 'col' => 'col-md-6', 'options' => $this->congregacoesOptions(), 'placeholder' => 'Selecione a congregação...'],
            ['name' => 'igreja_origem_id', 'label' => 'Igreja de origem', 'type' => 'select', 'col' => 'col-md-6', 'options' => $this->igrejasOptions(), 'placeholder' => 'Selecione a igreja de origem...'],
            ['name' => 'cargo_id', 'label' => 'Cargo ministerial', 'type' => 'select', 'required' => true, 'col' => 'col-md-4', 'options' => $cargos, 'placeholder' => 'Selecione o cargo...'],
            ['name' => 'data_consagracao', 'label' => 'Data de consagração', 'type' => 'date', 'col' => 'col-md-4'],
            ['name' => 'numero_registro_ministerial', 'label' => 'Registro ministerial', 'type' => 'text', 'maxlength' => 40, 'col' => 'col-md-4'],
            ['name' => 'situacao', 'label' => 'Situação', 'type' => 'select', 'required' => true, 'col' => 'col-md-4', 'default' => 'ATIVO', 'options' => $this->situacoes()],
            ['name' => 'usuario_id', 'label' => 'Usuário do sistema', 'type' => 'select', 'col' => 'col-md-8', 'options' => $this->usuariosOptions(), 'placeholder' => 'Selecione um usuário...'],
            ['name' => 'telefone', 'label' => 'Telefone', 'type' => 'tel', 'maxlength' => 20, 'col' => 'col-md-4'],
            ['name' => 'whatsapp', 'label' => 'WhatsApp', 'type' => 'tel', 'maxlength' => 20, 'col' => 'col-md-4'],
            ['name' => 'email', 'label' => 'E-mail', 'type' => 'email', 'maxlength' => 190, 'col' => 'col-md-4'],
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
            ['label' => 'Obreiro', 'key' => 'nome_completo', 'tipo' => 'principal', 'subtitulo' => 'codigo'],
            ['label' => 'Cargo', 'key' => 'cargo_nome'],
            ['label' => 'Congregação', 'key' => 'congregacao_nome'],
            ['label' => 'CPF', 'key' => 'cpf', 'tipo' => 'cpf'],
            ['label' => 'Consagração', 'key' => 'data_consagracao', 'tipo' => 'data'],
            [
                'label'   => 'Situação',
                'key'     => 'situacao',
                'tipo'    => 'badge',
                'rotulos' => $this->situacoes(),
                'cores'   => ['ATIVO' => 'success', 'AFASTADO' => 'warning', 'ARQUIVADO' => 'secondary', 'FALECIDO' => 'dark'],
            ],
        ];
    }

    private function situacoes(): array
    {
        return [
            'ATIVO'     => 'Ativo',
            'AFASTADO'  => 'Afastado',
            'ARQUIVADO' => 'Arquivado',
            'FALECIDO'  => 'Falecido',
        ];
    }

    private function permissoes(): array
    {
        return [
            'criar'   => 'obreiros.criar',
            'editar'  => 'obreiros.editar',
            'excluir' => 'obreiros.excluir',
        ];
    }
}
