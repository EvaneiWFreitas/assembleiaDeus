<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Audit;
use App\Core\Flash;
use App\Models\Congregacao;
use App\Models\PontoPregacao;
use App\Models\Usuario;

/**
 * Cadastro de pontos de pregação.
 */
final class PontoPregacaoController extends BaseEstruturaController
{
    private const ROTA = 'pontos';

    public function index(): void
    {
        $this->guard('pontos_pregacao.ver');
        $this->limparOld();

        $pontos = PontoPregacao::all();
        foreach ($pontos as &$ponto) {
            $ponto['cidade_uf'] = trim($ponto['cidade'] . ' - ' . $ponto['estado'], ' -');
            $ponto['responsavel'] = $ponto['responsavel_usuario'] ?: ($ponto['responsavel_nome'] ?: '—');
        }
        unset($ponto);

        $this->renderAdmin('admin.estrutura.index', [
            'title'      => 'Pontos de pregação',
            'titulo'     => 'Pontos de pregação',
            'descricao'  => 'Gerencie os locais de pregação vinculados às congregações.',
            'itens'      => $pontos,
            'rota'       => 'admin/' . self::ROTA,
            'permissoes' => $this->permissoes(),
            'nota'       => 'Pontos ativos são exibidos automaticamente no site institucional.',
            'colunas'    => [
                ['label' => 'Ponto', 'key' => 'nome', 'tipo' => 'principal', 'subtitulo' => 'cidade_uf'],
                ['label' => 'Congregação', 'key' => 'congregacao_nome'],
                ['label' => 'Igreja matriz', 'key' => 'igreja_nome'],
                ['label' => 'Responsável', 'key' => 'responsavel'],
                ['label' => 'Dias e horários', 'key' => 'dias_horarios'],
                [
                    'label'   => 'Situação',
                    'key'     => 'situacao',
                    'tipo'    => 'badge',
                    'rotulos' => ['ATIVO' => 'Ativo', 'INATIVO' => 'Inativo'],
                    'cores'   => ['ATIVO' => 'success', 'INATIVO' => 'secondary'],
                ],
            ],
        ]);
    }

    public function criarForm(): void
    {
        $this->guard('pontos_pregacao.criar');

        $this->renderAdmin('admin.estrutura.form', [
            'title'     => 'Novo ponto de pregação',
            'titulo'    => 'Novo ponto de pregação',
            'subtitulo' => 'Vincule o ponto a uma congregação e informe o endereço.',
            'acao'      => url('admin/pontos'),
            'voltar'    => 'admin/pontos',
            'botao'     => 'Cadastrar ponto',
            'registro'  => [],
            'campos'    => $this->campos(),
        ]);
    }

    public function criar(): void
    {
        $this->guard('pontos_pregacao.criar');

        $dados = $this->normalizar();
        $erros = $this->validar($dados);

        if ($erros !== []) {
            $this->guardarOld($dados);
            $this->falhar($erros, 'pontos/novo');
        }

        $id = PontoPregacao::create($dados);
        Audit::record('criar', 'pontos_pregacao', $dados, [], 'pontos_pregacao', $id);
        $this->limparOld();
        Flash::success('Ponto de pregação cadastrado com sucesso.');
        $this->redirectAdmin('pontos');
    }

    public function editarForm(string $id): void
    {
        $this->guard('pontos_pregacao.editar');

        $ponto = PontoPregacao::find((int) $id);
        if ($ponto === null) {
            Flash::error('Ponto de pregação não encontrado.');
            $this->redirectAdmin('pontos');
        }

        $this->renderAdmin('admin.estrutura.form', [
            'title'     => 'Editar ponto de pregação',
            'titulo'    => 'Editar ponto de pregação',
            'subtitulo' => 'Atualize os dados do ponto de pregação.',
            'acao'      => url('admin/pontos/' . (int) $id . '/editar'),
            'voltar'    => 'admin/pontos',
            'botao'     => 'Salvar alterações',
            'registro'  => $ponto,
            'campos'    => $this->campos(),
        ]);
    }

    public function editar(string $id): void
    {
        $this->guard('pontos_pregacao.editar');

        $ponto = PontoPregacao::find((int) $id);
        if ($ponto === null) {
            Flash::error('Ponto de pregação não encontrado.');
            $this->redirectAdmin('pontos');
        }

        $dados = $this->normalizar();
        $erros = $this->validar($dados);

        if ($erros !== []) {
            $this->guardarOld($dados);
            $this->falhar($erros, 'pontos/' . (int) $id . '/editar');
        }

        PontoPregacao::update((int) $id, $dados);
        Audit::record('editar', 'pontos_pregacao', $dados, $ponto, 'pontos_pregacao', (string) (int) $id);
        $this->limparOld();
        Flash::success('Ponto de pregação atualizado com sucesso.');
        $this->redirectAdmin('pontos');
    }

    public function excluir(string $id): void
    {
        $this->guard('pontos_pregacao.excluir');

        $ponto = PontoPregacao::find((int) $id);
        if ($ponto === null) {
            Flash::error('Ponto de pregação não encontrado.');
            $this->redirectAdmin('pontos');
        }

        PontoPregacao::delete((int) $id);
        Audit::record('excluir', 'pontos_pregacao', [], $ponto, 'pontos_pregacao', (string) (int) $id);
        Flash::success('Ponto de pregação excluído com sucesso.');
        $this->redirectAdmin('pontos');
    }

    private function validar(array $dados): array
    {
        $erros = $this->validarObrigatorios($dados, [
            'nome'            => 'o nome do ponto',
            'congregacao_id'  => 'a congregação',
            'endereco'        => 'o endereço',
            'cidade'          => 'a cidade',
            'estado'          => 'o estado',
        ]);

        if ($dados['congregacao_id'] !== null && !PontoPregacao::congregacaoPermitida($dados['congregacao_id'])) {
            $erros[] = 'Você não pode vincular este ponto a outra congregação.';
        }
        if (!in_array($dados['situacao'], ['ATIVO', 'INATIVO'], true)) {
            $erros[] = 'Selecione uma situação válida.';
        }

        return $erros;
    }

    private function normalizar(): array
    {
        return [
            'nome'             => $this->texto($this->request->post('nome'), 150),
            'congregacao_id'   => $this->inteiro($this->request->post('congregacao_id')),
            'endereco'         => $this->texto($this->request->post('endereco'), 255),
            'numero'           => $this->texto($this->request->post('numero'), 10),
            'bairro'           => $this->texto($this->request->post('bairro'), 100),
            'cidade'           => $this->texto($this->request->post('cidade'), 100),
            'estado'           => $this->estado($this->request->post('estado')),
            'cep'              => $this->texto($this->request->post('cep'), 9),
            'responsavel_id'   => $this->inteiro($this->request->post('responsavel_id')),
            'responsavel_nome' => $this->texto($this->request->post('responsavel_nome'), 150),
            'dias_horarios'    => $this->texto($this->request->post('dias_horarios'), 255),
            'data_abertura'    => $this->data($this->request->post('data_abertura')),
            'foto'             => $this->texto($this->request->post('foto'), 255),
            'situacao'         => $this->texto($this->request->post('situacao'), 20) ?? 'ATIVO',
            'observacoes'      => $this->texto($this->request->post('observacoes')),
        ];
    }

    private function campos(): array
    {
        $congregacoes = [];
        foreach (Congregacao::options() as $congregacao) {
            $rotulo = $congregacao['igreja_nome'] . ' / ' . $congregacao['nome'];
            $congregacoes[(int) $congregacao['id']] = $rotulo;
        }

        $responsaveis = [];
        foreach (Usuario::all() as $usuario) {
            $responsaveis[(int) $usuario['id']] = $usuario['nome'];
        }

        return [
            ['name' => 'nome', 'label' => 'Nome do ponto', 'type' => 'text', 'required' => true, 'maxlength' => 150, 'col' => 'col-md-8'],
            [
                'name' => 'situacao', 'label' => 'Situação', 'type' => 'select', 'required' => true, 'col' => 'col-md-4', 'default' => 'ATIVO',
                'options' => ['ATIVO' => 'Ativo', 'INATIVO' => 'Inativo'],
            ],
            ['name' => 'congregacao_id', 'label' => 'Congregação', 'type' => 'select', 'required' => true, 'col' => 'col-md-8', 'options' => $congregacoes, 'placeholder' => 'Selecione a congregação...'],
            ['name' => 'data_abertura', 'label' => 'Data de abertura', 'type' => 'date', 'col' => 'col-md-4'],
            ['name' => 'endereco', 'label' => 'Endereço', 'type' => 'text', 'required' => true, 'maxlength' => 255, 'col' => 'col-12'],
            ['name' => 'numero', 'label' => 'Número', 'type' => 'text', 'maxlength' => 10, 'col' => 'col-md-2'],
            ['name' => 'bairro', 'label' => 'Bairro', 'type' => 'text', 'maxlength' => 100, 'col' => 'col-md-4'],
            ['name' => 'cidade', 'label' => 'Cidade', 'type' => 'text', 'required' => true, 'maxlength' => 100, 'col' => 'col-md-3'],
            ['name' => 'estado', 'label' => 'Estado (UF)', 'type' => 'text', 'required' => true, 'maxlength' => 2, 'col' => 'col-md-3', 'placeholder' => 'PB'],
            ['name' => 'cep', 'label' => 'CEP', 'type' => 'text', 'maxlength' => 9, 'col' => 'col-md-3', 'placeholder' => '00000-000'],
            ['name' => 'dias_horarios', 'label' => 'Dias e horários', 'type' => 'text', 'maxlength' => 255, 'col' => 'col-md-9', 'placeholder' => 'Ex.: Domingos às 15h; quartas às 19h'],
            ['name' => 'responsavel_id', 'label' => 'Responsável (usuário)', 'type' => 'select', 'col' => 'col-md-6', 'options' => $responsaveis, 'placeholder' => 'Selecione um usuário...'],
            ['name' => 'responsavel_nome', 'label' => 'Responsável (nome livre)', 'type' => 'text', 'maxlength' => 150, 'col' => 'col-md-6', 'help' => 'Use quando o responsável não tiver acesso ao sistema.'],
            ['name' => 'foto', 'label' => 'Foto (caminho ou URL)', 'type' => 'text', 'maxlength' => 255, 'col' => 'col-md-6'],
            ['name' => 'observacoes', 'label' => 'Observações', 'type' => 'textarea', 'col' => 'col-12', 'rows' => 3],
        ];
    }

    private function permissoes(): array
    {
        return [
            'criar'   => 'pontos_pregacao.criar',
            'editar'  => 'pontos_pregacao.editar',
            'excluir' => 'pontos_pregacao.excluir',
        ];
    }
}
