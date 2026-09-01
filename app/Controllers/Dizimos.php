<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Libraries\Auditoria;
use App\Models\DizimoModel;
use App\Models\MembroModel;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * CRUD de dízimos.
 */
class Dizimos extends BaseController
{
    private DizimoModel $dizimos;
    private MembroModel $membros;

    public function __construct()
    {
        $this->dizimos = new DizimoModel();
        $this->membros = new MembroModel();
    }

    public function index(): string
    {
        $this->exigirPermissao('financeiro', 'visualizar');

        $this->dados['titulo'] = 'Dízimos';
        $this->dados['dizimos'] = $this->dizimos->listar($this->request->getGet('mes'));
        $this->dados['formas'] = DizimoModel::FORMAS_PAGAMENTO;

        return view('dizimos/index', $this->dados);
    }

    public function novo(): string
    {
        $this->exigirPermissao('financeiro', 'cadastrar');

        $this->dados['titulo']   = 'Novo Dízimo';
        $this->dados['registro'] = null;
        $this->dados['membros']  = $this->membros->getDropdown();
        $this->dados['formas']   = DizimoModel::FORMAS_PAGAMENTO;

        return view('dizimos/form', $this->dados);
    }

    public function editar(int $id): string
    {
        $this->exigirPermissao('financeiro', 'editar');

        $this->dados['titulo']   = 'Editar Dízimo';
        $this->dados['registro'] = $this->dizimos->find($id);
        $this->dados['membros']  = $this->membros->getDropdown();
        $this->dados['formas']   = DizimoModel::FORMAS_PAGAMENTO;

        if ($this->dados['registro'] === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('dizimos/form', $this->dados);
    }

    public function salvar(): RedirectResponse
    {
        $this->exigirPermissao('financeiro', 'cadastrar');

        $dados = $this->dadosPost();

        if (! $this->validate($this->dizimos->validationRules)) {
            return redirect()->back()->withInput()->with('erros', $this->validator->getErrors());
        }

        $this->dizimos->insert($dados);
        $id = (int) $this->dizimos->getInsertID();

        (new Auditoria())->log('criar', 'dizimos', $id, null, $dados);

        return redirect()->to('/dizimos')->with('sucesso', 'Dízimo registrado com sucesso!');
    }

    public function atualizar(int $id): RedirectResponse
    {
        $this->exigirPermissao('financeiro', 'editar');

        $antes = $this->dizimos->find($id);
        if ($antes === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $dados = $this->dadosPost();

        if (! $this->validate($this->dizimos->validationRules)) {
            return redirect()->back()->withInput()->with('erros', $this->validator->getErrors());
        }

        $this->dizimos->update($id, $dados);

        (new Auditoria())->log('editar', 'dizimos', $id, $antes, $dados);

        return redirect()->to('/dizimos')->with('sucesso', 'Dízimo atualizado com sucesso!');
    }

    public function excluir(int $id): RedirectResponse
    {
        $this->exigirPermissao('financeiro', 'excluir');

        $antes = $this->dizimos->find($id);
        if ($antes !== null) {
            $this->dizimos->delete($id);
            (new Auditoria())->log('excluir', 'dizimos', $id);
        }

        return redirect()->to('/dizimos')->with('sucesso', 'Dízimo excluído com sucesso!');
    }

    private function dadosPost(): array
    {
        return [
            'membro_id'       => $this->request->getPost('membro_id') ?: null,
            'valor'           => number_format(limpar_decimal($this->request->getPost('valor')), 2, '.', ''),
            'data'            => $this->request->getPost('data'),
            'forma_pagamento' => $this->request->getPost('forma_pagamento') ?: null,
            'observacoes'     => trim((string) $this->request->getPost('observacoes')) ?: null,
        ];
    }
}