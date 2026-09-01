<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Libraries\Auditoria;
use App\Models\DepartamentoModel;
use App\Models\MembroModel;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * CRUD de departamentos.
 */
class Departamentos extends BaseController
{
    private DepartamentoModel $departamentos;
    private MembroModel $membros;

    public function __construct()
    {
        $this->departamentos = new DepartamentoModel();
        $this->membros       = new MembroModel();
    }

    public function index(): string
    {
        $this->exigirPermissao('departamentos', 'visualizar');

        $this->dados['titulo']        = 'Departamentos';
        $this->dados['departamentos'] = $this->departamentos->listarComLideres();

        return view('departamentos/index', $this->dados);
    }

    public function novo(): string
    {
        $this->exigirPermissao('departamentos', 'cadastrar');

        $this->dados['titulo']   = 'Novo Departamento';
        $this->dados['registro'] = null;
        $this->dados['membros']  = $this->membros->getDropdown();

        return view('departamentos/form', $this->dados);
    }

    public function editar(int $id): string
    {
        $this->exigirPermissao('departamentos', 'editar');

        $this->dados['titulo']   = 'Editar Departamento';
        $this->dados['registro'] = $this->departamentos->find($id);
        $this->dados['membros']  = $this->membros->getDropdown();

        if ($this->dados['registro'] === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('departamentos/form', $this->dados);
    }

    public function salvar(): RedirectResponse
    {
        $this->exigirPermissao('departamentos', 'cadastrar');

        if (! $this->validate($this->departamentos->validationRules)) {
            return redirect()->back()->withInput()->with('erros', $this->validator->getErrors());
        }

        $dados = $this->dadosPost();
        $this->departamentos->insert($dados);
        $id = (int) $this->departamentos->getInsertID();

        (new Auditoria())->log('criar', 'departamentos', $id, null, ['nome' => $dados['nome']]);

        return redirect()->to('/departamentos')->with('sucesso', 'Departamento cadastrado com sucesso!');
    }

    public function atualizar(int $id): RedirectResponse
    {
        $this->exigirPermissao('departamentos', 'editar');

        $antes = $this->departamentos->find($id);
        if ($antes === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if (! $this->validate($this->departamentos->validationRules)) {
            return redirect()->back()->withInput()->with('erros', $this->validator->getErrors());
        }

        $dados = $this->dadosPost();
        $this->departamentos->update($id, $dados);
        (new Auditoria())->log('editar', 'departamentos', $id, ['nome' => $antes['nome']], $dados);

        return redirect()->to('/departamentos')->with('sucesso', 'Departamento atualizado com sucesso!');
    }

    public function excluir(int $id): RedirectResponse
    {
        $this->exigirPermissao('departamentos', 'excluir');

        $antes = $this->departamentos->find($id);
        if ($antes !== null) {
            $this->departamentos->delete($id);
            (new Auditoria())->log('excluir', 'departamentos', $id, ['nome' => $antes['nome']]);
        }

        return redirect()->to('/departamentos')->with('sucesso', 'Departamento excluído com sucesso!');
    }

    private function dadosPost(): array
    {
        return [
            'nome'          => trim((string) $this->request->getPost('nome')),
            'descricao'     => trim((string) $this->request->getPost('descricao')) ?: null,
            'lider_id'      => $this->request->getPost('lider_id') ?: null,
            'vice_lider_id' => $this->request->getPost('vice_lider_id') ?: null,
            'cor'           => $this->request->getPost('cor') ?: null,
            'ativo'         => $this->request->getPost('ativo') ? 1 : 0,
        ];
    }
}
