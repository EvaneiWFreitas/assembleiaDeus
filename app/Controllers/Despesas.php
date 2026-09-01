<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Libraries\Auditoria;
use App\Models\CategoriaModel;
use App\Models\DespesaModel;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * CRUD de despesas.
 */
class Despesas extends BaseController
{
    private DespesaModel $despesas;
    private CategoriaModel $categorias;

    public function __construct()
    {
        $this->despesas   = new DespesaModel();
        $this->categorias = new CategoriaModel();
    }

    public function index(): string
    {
        $this->exigirPermissao('financeiro', 'visualizar');

        $this->dados['titulo']   = 'Despesas';
        $this->dados['despesas'] = $this->despesas->listar($this->request->getGet('mes'));
        $this->dados['formas']   = DespesaModel::FORMAS_PAGAMENTO;

        return view('despesas/index', $this->dados);
    }

    public function novo(): string
    {
        $this->exigirPermissao('financeiro', 'cadastrar');

        $this->dados['titulo']     = 'Nova Despesa';
        $this->dados['registro']   = null;
        $this->dados['categorias'] = $this->categorias->getDropdown('saida');
        $this->dados['formas']     = DespesaModel::FORMAS_PAGAMENTO;

        return view('despesas/form', $this->dados);
    }

    public function editar(int $id): string
    {
        $this->exigirPermissao('financeiro', 'editar');

        $this->dados['titulo']     = 'Editar Despesa';
        $this->dados['registro']   = $this->despesas->find($id);
        $this->dados['categorias'] = $this->categorias->getDropdown('saida');
        $this->dados['formas']     = DespesaModel::FORMAS_PAGAMENTO;

        if ($this->dados['registro'] === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('despesas/form', $this->dados);
    }

    public function salvar(): RedirectResponse
    {
        $this->exigirPermissao('financeiro', 'cadastrar');

        $dados = $this->dadosPost();

        if (! $this->validate($this->despesas->validationRules)) {
            return redirect()->back()->withInput()->with('erros', $this->validator->getErrors());
        }

        $this->despesas->insert($dados);
        $id = (int) $this->despesas->getInsertID();

        (new Auditoria())->log('criar', 'despesas', $id, null, $dados);

        return redirect()->to('/despesas')->with('sucesso', 'Despesa registrada com sucesso!');
    }

    public function atualizar(int $id): RedirectResponse
    {
        $this->exigirPermissao('financeiro', 'editar');

        $antes = $this->despesas->find($id);
        if ($antes === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $dados = $this->dadosPost();

        if (! $this->validate($this->despesas->validationRules)) {
            return redirect()->back()->withInput()->with('erros', $this->validator->getErrors());
        }

        $this->despesas->update($id, $dados);

        (new Auditoria())->log('editar', 'despesas', $id, $antes, $dados);

        return redirect()->to('/despesas')->with('sucesso', 'Despesa atualizada com sucesso!');
    }

    public function excluir(int $id): RedirectResponse
    {
        $this->exigirPermissao('financeiro', 'excluir');

        $antes = $this->despesas->find($id);
        if ($antes !== null) {
            $this->despesas->delete($id);
            (new Auditoria())->log('excluir', 'despesas', $id);
        }

        return redirect()->to('/despesas')->with('sucesso', 'Despesa excluída com sucesso!');
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