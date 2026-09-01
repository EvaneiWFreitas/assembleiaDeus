<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Libraries\Auditoria;
use App\Models\CategoriaModel;
use App\Models\ReceitaModel;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * CRUD de receitas.
 */
class Receitas extends BaseController
{
    private ReceitaModel $receitas;
    private CategoriaModel $categorias;

    public function __construct()
    {
        $this->receitas   = new ReceitaModel();
        $this->categorias = new CategoriaModel();
    }

    public function index(): string
    {
        $this->exigirPermissao('financeiro', 'visualizar');

        $this->dados['titulo']   = 'Receitas';
        $this->dados['receitas'] = $this->receitas->listar($this->request->getGet('mes'));
        $this->dados['formas']   = ReceitaModel::FORMAS_PAGAMENTO;

        return view('receitas/index', $this->dados);
    }

    public function novo(): string
    {
        $this->exigirPermissao('financeiro', 'cadastrar');

        $this->dados['titulo']     = 'Nova Receita';
        $this->dados['registro']   = null;
        $this->dados['categorias'] = $this->categorias->getDropdown('entrada');
        $this->dados['formas']     = ReceitaModel::FORMAS_PAGAMENTO;

        return view('receitas/form', $this->dados);
    }

    public function editar(int $id): string
    {
        $this->exigirPermissao('financeiro', 'editar');

        $this->dados['titulo']     = 'Editar Receita';
        $this->dados['registro']   = $this->receitas->find($id);
        $this->dados['categorias'] = $this->categorias->getDropdown('entrada');
        $this->dados['formas']     = ReceitaModel::FORMAS_PAGAMENTO;

        if ($this->dados['registro'] === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('receitas/form', $this->dados);
    }

    public function salvar(): RedirectResponse
    {
        $this->exigirPermissao('financeiro', 'cadastrar');

        $dados = $this->dadosPost();

        if (! $this->validate($this->receitas->validationRules)) {
            return redirect()->back()->withInput()->with('erros', $this->validator->getErrors());
        }

        $this->receitas->insert($dados);
        $id = (int) $this->receitas->getInsertID();

        (new Auditoria())->log('criar', 'receitas', $id, null, $dados);

        return redirect()->to('/receitas')->with('sucesso', 'Receita registrada com sucesso!');
    }

    public function atualizar(int $id): RedirectResponse
    {
        $this->exigirPermissao('financeiro', 'editar');

        $antes = $this->receitas->find($id);
        if ($antes === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $dados = $this->dadosPost();

        if (! $this->validate($this->receitas->validationRules)) {
            return redirect()->back()->withInput()->with('erros', $this->validator->getErrors());
        }

        $this->receitas->update($id, $dados);

        (new Auditoria())->log('editar', 'receitas', $id, $antes, $dados);

        return redirect()->to('/receitas')->with('sucesso', 'Receita atualizada com sucesso!');
    }

    public function excluir(int $id): RedirectResponse
    {
        $this->exigirPermissao('financeiro', 'excluir');

        $antes = $this->receitas->find($id);
        if ($antes !== null) {
            $this->receitas->delete($id);
            (new Auditoria())->log('excluir', 'receitas', $id);
        }

        return redirect()->to('/receitas')->with('sucesso', 'Receita excluída com sucesso!');
    }

    private function dadosPost(): array
    {
        return [
            'categoria_id'    => $this->request->getPost('categoria_id') ?: null,
            'descricao'       => trim((string) $this->request->getPost('descricao')),
            'valor'           => number_format(limpar_decimal($this->request->getPost('valor')), 2, '.', ''),
            'data'            => $this->request->getPost('data'),
            'forma_pagamento' => $this->request->getPost('forma_pagamento') ?: null,
            'observacoes'     => trim((string) $this->request->getPost('observacoes')) ?: null,
        ];
    }
}