<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Libraries\Auditoria;
use App\Models\CategoriaModel;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * CRUD de categorias de lançamento financeiro.
 */
class Categorias extends BaseController
{
    private CategoriaModel $categorias;

    public function __construct()
    {
        $this->categorias = new CategoriaModel();
    }

    public function index(): string
    {
        $this->exigirPermissao('financeiro', 'visualizar');

        $this->dados['titulo']     = 'Categorias';
        $this->dados['categorias'] = $this->categorias->listar($this->request->getGet('tipo'));
        $this->dados['tipos']      = CategoriaModel::TIPOS;

        return view('categorias/index', $this->dados);
    }

    public function novo(): string
    {
        $this->exigirPermissao('financeiro', 'cadastrar');

        $this->dados['titulo']   = 'Nova Categoria';
        $this->dados['registro'] = null;
        $this->dados['tipos']    = CategoriaModel::TIPOS;

        return view('categorias/form', $this->dados);
    }

    public function salvar(): RedirectResponse
    {
        $this->exigirPermissao('financeiro', 'cadastrar');

        if (! $this->validate($this->categorias->validationRules)) {
            return redirect()->back()->withInput()->with('erros', $this->validator->getErrors());
        }

        $dados = $this->dadosPost();
        $this->categorias->insert($dados);
        $id = (int) $this->categorias->getInsertID();

        (new Auditoria())->log('criar', 'categorias', $id, null, $dados);

        return redirect()->to('/categorias')->with('sucesso', 'Categoria cadastrada com sucesso!');
    }

    public function editar(int $id): string
    {
        $this->exigirPermissao('financeiro', 'editar');

        $this->dados['titulo']   = 'Editar Categoria';
        $this->dados['registro'] = $this->categorias->find($id);
        $this->dados['tipos']    = CategoriaModel::TIPOS;

        if ($this->dados['registro'] === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('categorias/form', $this->dados);
    }

    public function atualizar(int $id): RedirectResponse
    {
        $this->exigirPermissao('financeiro', 'editar');

        $antes = $this->categorias->find($id);
        if ($antes === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if (! $this->validate($this->categorias->validationRules)) {
            return redirect()->back()->withInput()->with('erros', $this->validator->getErrors());
        }

        $dados = $this->dadosPost();
        $this->categorias->update($id, $dados);

        (new Auditoria())->log('editar', 'categorias', $id, $antes, $dados);

        return redirect()->to('/categorias')->with('sucesso', 'Categoria atualizada com sucesso!');
    }

    public function excluir(int $id): RedirectResponse
    {
        $this->exigirPermissao('financeiro', 'excluir');

        $antes = $this->categorias->find($id);
        if ($antes !== null) {
            $this->categorias->delete($id);
            (new Auditoria())->log('excluir', 'categorias', $id);
        }

        return redirect()->to('/categorias')->with('sucesso', 'Categoria excluída com sucesso!');
    }

    private function dadosPost(): array
    {
        return [
            'nome'  => trim((string) $this->request->getPost('nome')),
            'tipo'  => $this->request->getPost('tipo'),
            'ativo' => $this->request->getPost('ativo') ? 1 : 0,
        ];
    }
}