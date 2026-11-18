<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Audit;
use App\Core\Flash;
use App\Models\Congregacao;
use App\Models\Igreja;
use App\Models\Usuario;
use PDOException;

/**
 * Cadastro de congregações.
 */
final class CongregacaoController extends BaseEstruturaController
{
    private const ROTA = 'congregacoes';

    public function index(): void
    {
        $this->guard('congregacoes.ver');
        $this->limparOld();

        $congregacoes = Congregacao::all();
        foreach ($congregacoes as &$congregacao) {
            $congregacao['cidade_uf'] = trim($congregacao['cidade'] . ' - ' . $congregacao['estado'], ' -');
        }
        unset($congregacao);

        $this->renderAdmin('admin.estrutura.index', [
            'title'      => 'Congregações',
            'titulo'     => 'Congregações',
            'descricao'  => 'Gerencie as congregações vinculadas à igreja matriz.',
            'itens'      => $congregacoes,
            'rota'       => 'admin/' . self::ROTA,
            'permissoes' => $this->permissoes(),
            'nota'       => 'Uma congregação não pode ser excluída enquanto houver pontos de pregação vinculados.',
            'colunas'    => [
                ['label' => 'Congregação', 'key' => 'nome', 'tipo' => 'principal', 'subtitulo' => 'cidade_uf'],
                ['label' => 'Código', 'key' => 'codigo'],
                ['label' => 'Igreja matriz', 'key' => 'igreja_nome'],
                ['label' => 'Pastor responsável', 'key' => 'pastor_nome'],
                ['label' => 'Pontos', 'key' => 'total_pontos', 'tipo' => 'contador'],
                [
                    'label'   => 'Situação',
                    'key'     => 'situacao',
                    'tipo'    => 'badge',
                    'rotulos' => ['ATIVA' => 'Ativa', 'INATIVA' => 'Inativa', 'EM_PLANTIO' => 'Em plantio'],
                    'cores'   => ['ATIVA' => 'success', 'INATIVA' => 'secondary', 'EM_PLANTIO' => 'warning'],
                ],
            ],
        ]);
    }

    public function criarForm(): void
    {
        $this->guard('congregacoes.criar');

        $this->renderAdmin('admin.estrutura.form', [
            'title'     => 'Nova congregação',
            'titulo'    => 'Nova congregação',
            'subtitulo' => 'Vincule a congregação a uma igreja matriz.',
            'acao'      => url('admin/congregacoes'),
            'voltar'    => 'admin/congregacoes',
            'botao'     => 'Cadastrar congregação',
            'registro'  => [],
            'campos'    => $this->campos(),
        ]);
    }

    public function criar(): void
    {
        $this->guard('congregacoes.criar');

        $dados = $this->normalizar();
        $erros = $this->validar($dados);

        if ($erros !== []) {
            $this->guardarOld($dados);
            $this->falhar($erros, 'congregacoes/novo');
        }

        $id = Congregacao::create($dados);
        Audit::record('criar', 'congregacoes', $dados, [], 'congregacoes', $id);
        $this->limparOld();
        Flash::success('Congregação cadastrada com sucesso.');
        $this->redirectAdmin('congregacoes');
    }

    public function editarForm(string $id): void
    {
        $this->guard('congregacoes.editar');

        $congregacao = Congregacao::find((int) $id);
        if ($congregacao === null) {
            Flash::error('Congregação não encontrada.');
            $this->redirectAdmin('congregacoes');
        }

        $this->renderAdmin('admin.estrutura.form', [
            'title'     => 'Editar congregação',
            'titulo'    => 'Editar congregação',
            'subtitulo' => 'Atualize os dados da congregação.',
            'acao'      => url('admin/congregacoes/' . (int) $id . '/editar'),
            'voltar'    => 'admin/congregacoes',
            'botao'     => 'Salvar alterações',
            'registro'  => $congregacao,
            'campos'    => $this->campos(),
        ]);
    }

    public function editar(string $id): void
    {
        $this->guard('congregacoes.editar');

        $congregacao = Congregacao::find((int) $id);
        if ($congregacao === null) {
            Flash::error('Congregação não encontrada.');
            $this->redirectAdmin('congregacoes');
        }

        $dados = $this->normalizar();
        $erros = $this->validar($dados, (int) $id);

        if ($erros !== []) {
            $this->guardarOld($dados);
            $this->falhar($erros, 'congregacoes/' . (int) $id . '/editar');
        }

        Congregacao::update((int) $id, $dados);
        Audit::record('editar', 'congregacoes', $dados, $congregacao, 'congregacoes', (string) (int) $id);
        $this->limparOld();
        Flash::success('Congregação atualizada com sucesso.');
        $this->redirectAdmin('congregacoes');
    }

    public function excluir(string $id): void
    {
        $this->guard('congregacoes.excluir');

        $congregacao = Congregacao::find((int) $id);
        if ($congregacao === null) {
            Flash::error('Congregação não encontrada.');
            $this->redirectAdmin('congregacoes');
        }

        try {
            Congregacao::delete((int) $id);
        } catch (PDOException) {
            Flash::error('Não foi possível excluir: existem pontos de pregação vinculados a esta congregação.');
            $this->redirectAdmin('congregacoes');
        }

        Audit::record('excluir', 'congregacoes', [], $congregacao, 'congregacoes', (string) (int) $id);
        Flash::success('Congregação excluída com sucesso.');
        $this->redirectAdmin('congregacoes');
    }

    private function validar(array $dados, ?int $ignorarId = null): array
    {
        $erros = $this->validarObrigatorios($dados, [
            'codigo'            => 'o código',
            'nome'              => 'o nome da congregação',
            'igreja_matriz_id'  => 'a igreja matriz',
            'endereco'          => 'o endereço',
            'cidade'            => 'a cidade',
            'estado'            => 'o estado',
        ]);

        if ($dados['igreja_matriz_id'] !== null && !Congregacao::igrejaPermitida($dados['igreja_matriz_id'])) {
            $erros[] = 'Você não pode vincular esta congregação a outra igreja matriz.';
        }
        if (!in_array($dados['situacao'], ['ATIVA', 'INATIVA', 'EM_PLANTIO'], true)) {
            $erros[] = 'Selecione uma situação válida.';
        }
        if ($dados['codigo'] !== null && Congregacao::codigoExiste($dados['codigo'], $ignorarId)) {
            $erros[] = 'Este código já está em uso.';
        }
        if ($dados['email'] !== null && !filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
            $erros[] = 'Informe um e-mail válido.';
        }

        return $erros;
    }

    private function normalizar(): array
    {
        return [
            'codigo'                => $this->texto($this->request->post('codigo'), 20),
            'nome'                  => $this->texto($this->request->post('nome'), 150),
            'igreja_matriz_id'      => $this->inteiro($this->request->post('igreja_matriz_id')),
            'endereco'              => $this->texto($this->request->post('endereco'), 255),
            'numero'                => $this->texto($this->request->post('numero'), 10),
            'complemento'           => $this->texto($this->request->post('complemento'), 100),
            'bairro'                => $this->texto($this->request->post('bairro'), 100),
            'cidade'                => $this->texto($this->request->post('cidade'), 100),
            'estado'                => $this->estado($this->request->post('estado')),
            'cep'                   => $this->texto($this->request->post('cep'), 9),
            'telefone'              => $this->texto($this->request->post('telefone'), 20),
            'email'                 => $this->texto($this->request->post('email'), 190),
            'pastor_responsavel_id' => $this->inteiro($this->request->post('pastor_responsavel_id')),
            'data_abertura'         => $this->data($this->request->post('data_abertura')),
            'foto'                  => $this->texto($this->request->post('foto'), 255),
            'situacao'              => $this->texto($this->request->post('situacao'), 20) ?? 'ATIVA',
            'observacoes'           => $this->texto($this->request->post('observacoes')),
        ];
    }

    private function campos(): array
    {
        $igrejas = [];
        foreach (Igreja::options() as $igreja) {
            $igrejas[(int) $igreja['id']] = $igreja['nome'];
        }

        $pastoras = [];
        foreach (Usuario::all() as $usuario) {
            $pastoras[(int) $usuario['id']] = $usuario['nome'];
        }

        return [
            ['name' => 'codigo', 'label' => 'Código', 'type' => 'text', 'required' => true, 'maxlength' => 20, 'col' => 'col-md-4', 'help' => 'Identificador único, ex.: CONG-01.'],
            ['name' => 'nome', 'label' => 'Nome da congregação', 'type' => 'text', 'required' => true, 'maxlength' => 150, 'col' => 'col-md-8'],
            ['name' => 'igreja_matriz_id', 'label' => 'Igreja matriz', 'type' => 'select', 'required' => true, 'col' => 'col-md-6', 'options' => $igrejas, 'placeholder' => 'Selecione a matriz...'],
            [
                'name' => 'situacao', 'label' => 'Situação', 'type' => 'select', 'required' => true, 'col' => 'col-md-3', 'default' => 'ATIVA',
                'options' => [
                    'ATIVA'      => 'Ativa',
                    'INATIVA'    => 'Inativa',
                    'EM_PLANTIO' => 'Em plantio',
                ],
            ],
            ['name' => 'data_abertura', 'label' => 'Data de abertura', 'type' => 'date', 'col' => 'col-md-3'],
            ['name' => 'pastor_responsavel_id', 'label' => 'Pastor responsável', 'type' => 'select', 'col' => 'col-md-6', 'options' => $pastoras, 'placeholder' => 'Selecione um usuário...'],
            ['name' => 'endereco', 'label' => 'Endereço', 'type' => 'text', 'required' => true, 'maxlength' => 255, 'col' => 'col-12'],
            ['name' => 'numero', 'label' => 'Número', 'type' => 'text', 'maxlength' => 10, 'col' => 'col-md-2'],
            ['name' => 'complemento', 'label' => 'Complemento', 'type' => 'text', 'maxlength' => 100, 'col' => 'col-md-4'],
            ['name' => 'bairro', 'label' => 'Bairro', 'type' => 'text', 'maxlength' => 100, 'col' => 'col-md-6'],
            ['name' => 'cidade', 'label' => 'Cidade', 'type' => 'text', 'required' => true, 'maxlength' => 100, 'col' => 'col-md-6'],
            ['name' => 'estado', 'label' => 'Estado (UF)', 'type' => 'text', 'required' => true, 'maxlength' => 2, 'col' => 'col-md-2', 'placeholder' => 'PB'],
            ['name' => 'cep', 'label' => 'CEP', 'type' => 'text', 'maxlength' => 9, 'col' => 'col-md-4', 'placeholder' => '00000-000'],
            ['name' => 'telefone', 'label' => 'Telefone', 'type' => 'tel', 'maxlength' => 20, 'col' => 'col-md-3'],
            ['name' => 'email', 'label' => 'E-mail', 'type' => 'email', 'maxlength' => 190, 'col' => 'col-md-5'],
            ['name' => 'foto', 'label' => 'Foto (caminho ou URL)', 'type' => 'text', 'maxlength' => 255, 'col' => 'col-md-7'],
            ['name' => 'observacoes', 'label' => 'Observações', 'type' => 'textarea', 'col' => 'col-12', 'rows' => 3],
        ];
    }

    private function permissoes(): array
    {
        return [
            'criar'   => 'congregacoes.criar',
            'editar'  => 'congregacoes.editar',
            'excluir' => 'congregacoes.excluir',
        ];
    }
}
