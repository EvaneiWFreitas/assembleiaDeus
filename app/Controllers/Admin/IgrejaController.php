<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Audit;
use App\Core\Flash;
use App\Models\Igreja;
use App\Models\Usuario;
use PDOException;

/**
 * Cadastro de igrejas matriz.
 */
final class IgrejaController extends BaseEstruturaController
{
    private const ROTA = 'igrejas';

    public function index(): void
    {
        $this->guard('igrejas.ver');
        $this->limparOld();

        $igrejas = Igreja::all();
        foreach ($igrejas as &$igreja) {
            $igreja['cidade_uf'] = trim($igreja['cidade'] . ' - ' . $igreja['estado'], ' -');
        }
        unset($igreja);

        $this->renderAdmin('admin.estrutura.index', [
            'title'       => 'Igrejas matriz',
            'titulo'      => 'Igrejas matriz',
            'descricao'   => 'Cadastre a matriz, seus dados de identificação e o pastor responsável.',
            'itens'       => $igrejas,
            'rota'        => 'admin/' . self::ROTA,
            'permissoes'  => $this->permissoes(),
            'nota'        => 'A igreja matriz não pode ser excluída enquanto houver congregações vinculadas.',
            'colunas'     => [
                ['label' => 'Igreja', 'key' => 'nome', 'tipo' => 'principal', 'subtitulo' => 'cidade_uf'],
                ['label' => 'CNPJ', 'key' => 'cnpj', 'tipo' => 'cnpj'],
                ['label' => 'Pastor responsável', 'key' => 'pastor_nome'],
                ['label' => 'Congregações', 'key' => 'total_congregacoes', 'tipo' => 'contador'],
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
        $this->guard('igrejas.criar');

        $this->renderAdmin('admin.estrutura.form', [
            'title'     => 'Nova igreja matriz',
            'titulo'    => 'Nova igreja matriz',
            'subtitulo' => 'Preencha os dados de identificação da matriz.',
            'acao'      => url('admin/igrejas'),
            'voltar'    => 'admin/igrejas',
            'botao'     => 'Cadastrar igreja',
            'registro'  => [],
            'campos'    => $this->campos(),
        ]);
    }

    public function criar(): void
    {
        $this->guard('igrejas.criar');

        $dados = $this->normalizar();
        $erros = $this->validarObrigatorios($dados, [
            'nome'     => 'o nome da igreja',
            'endereco' => 'o endereço',
            'cidade'   => 'a cidade',
            'estado'   => 'o estado',
        ]);

        if (!in_array($dados['situacao'], ['ATIVA', 'INATIVA', 'EM_PLANTIO'], true)) {
            $erros[] = 'Selecione uma situação válida.';
        }
        if ($dados['cnpj'] !== null && Igreja::cnpjExiste($dados['cnpj'])) {
            $erros[] = 'Este CNPJ já está cadastrado.';
        }

        if ($erros !== []) {
            $this->guardarOld($dados);
            $this->falhar($erros, 'igrejas/novo');
        }

        $id = Igreja::create($dados);
        Audit::record('criar', 'igrejas', $dados, [], 'igrejas_matriz', $id);
        $this->limparOld();
        Flash::success('Igreja matriz cadastrada com sucesso.');
        $this->redirectAdmin('igrejas');
    }

    public function editarForm(string $id): void
    {
        $this->guard('igrejas.editar');

        $igreja = Igreja::find((int) $id);
        if ($igreja === null) {
            Flash::error('Igreja matriz não encontrada.');
            $this->redirectAdmin('igrejas');
        }

        $this->renderAdmin('admin.estrutura.form', [
            'title'     => 'Editar igreja matriz',
            'titulo'    => 'Editar igreja matriz',
            'subtitulo' => 'Atualize os dados cadastrais da matriz.',
            'acao'      => url('admin/igrejas/' . (int) $id . '/editar'),
            'voltar'    => 'admin/igrejas',
            'botao'     => 'Salvar alterações',
            'registro'  => $igreja,
            'campos'    => $this->campos(),
        ]);
    }

    public function editar(string $id): void
    {
        $this->guard('igrejas.editar');

        $igreja = Igreja::find((int) $id);
        if ($igreja === null) {
            Flash::error('Igreja matriz não encontrada.');
            $this->redirectAdmin('igrejas');
        }

        $dados = $this->normalizar();
        $erros = $this->validarObrigatorios($dados, [
            'nome'     => 'o nome da igreja',
            'endereco' => 'o endereço',
            'cidade'   => 'a cidade',
            'estado'   => 'o estado',
        ]);

        if (!in_array($dados['situacao'], ['ATIVA', 'INATIVA', 'EM_PLANTIO'], true)) {
            $erros[] = 'Selecione uma situação válida.';
        }
        if ($dados['cnpj'] !== null && Igreja::cnpjExiste($dados['cnpj'], (int) $id)) {
            $erros[] = 'Este CNPJ já está cadastrado.';
        }

        if ($erros !== []) {
            $this->guardarOld($dados);
            $this->falhar($erros, 'igrejas/' . (int) $id . '/editar');
        }

        Igreja::update((int) $id, $dados);
        Audit::record('editar', 'igrejas', $dados, $igreja, 'igrejas_matriz', (string) (int) $id);
        $this->limparOld();
        Flash::success('Igreja matriz atualizada com sucesso.');
        $this->redirectAdmin('igrejas');
    }

    public function excluir(string $id): void
    {
        $this->guard('igrejas.excluir');

        $igreja = Igreja::find((int) $id);
        if ($igreja === null) {
            Flash::error('Igreja matriz não encontrada.');
            $this->redirectAdmin('igrejas');
        }

        try {
            Igreja::delete((int) $id);
        } catch (PDOException) {
            Flash::error('Não foi possível excluir: existem congregações vinculadas a esta igreja.');
            $this->redirectAdmin('igrejas');
        }

        Audit::record('excluir', 'igrejas', [], $igreja, 'igrejas_matriz', (string) (int) $id);
        Flash::success('Igreja matriz excluída com sucesso.');
        $this->redirectAdmin('igrejas');
    }

    private function normalizar(): array
    {
        return [
            'nome'                  => $this->texto($this->request->post('nome'), 150),
            'cnpj'                  => soDigitos($this->request->post('cnpj', '')),
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
            'data_fundacao'         => $this->data($this->request->post('data_fundacao')),
            'foto'                  => $this->texto($this->request->post('foto'), 255),
            'situacao'              => $this->texto($this->request->post('situacao'), 20) ?? 'ATIVA',
            'observacoes'           => $this->texto($this->request->post('observacoes')),
        ];
    }

    private function campos(): array
    {
        $pastoras = [];
        foreach (Usuario::all() as $usuario) {
            $pastoras[(int) $usuario['id']] = $usuario['nome'];
        }

        return [
            ['name' => 'nome', 'label' => 'Nome da igreja', 'type' => 'text', 'required' => true, 'maxlength' => 150, 'col' => 'col-md-8'],
            ['name' => 'cnpj', 'label' => 'CNPJ', 'type' => 'text', 'maxlength' => 18, 'col' => 'col-md-4', 'placeholder' => '00.000.000/0001-00'],
            [
                'name' => 'situacao', 'label' => 'Situação', 'type' => 'select', 'required' => true, 'col' => 'col-md-4', 'default' => 'ATIVA',
                'options' => [
                    'ATIVA'      => 'Ativa',
                    'INATIVA'    => 'Inativa',
                    'EM_PLANTIO' => 'Em plantio',
                ],
            ],
            ['name' => 'data_fundacao', 'label' => 'Data de fundação', 'type' => 'date', 'col' => 'col-md-4'],
            [
                'name' => 'pastor_responsavel_id', 'label' => 'Pastor responsável', 'type' => 'select', 'col' => 'col-md-4',
                'options' => $pastoras, 'placeholder' => 'Selecione um usuário...',
            ],
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
            'criar'   => 'igrejas.criar',
            'editar'  => 'igrejas.editar',
            'excluir' => 'igrejas.excluir',
        ];
    }
}
