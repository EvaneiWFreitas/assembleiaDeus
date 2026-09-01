<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Libraries\Auditoria;
use App\Models\PermissaoModel;
use App\Models\RoleModel;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Perfis (roles) e atribuição de permissões.
 */
class Roles extends BaseController
{
    private RoleModel $roles;
    private PermissaoModel $permissoes;

    public function __construct()
    {
        $this->roles      = new RoleModel();
        $this->permissoes = new PermissaoModel();
    }

    public function index(): string
    {
        $this->exigirPermissao('roles', 'visualizar');
        $this->dados['titulo'] = 'Perfis & Permissões';
        $this->dados['roles']  = $this->roles->findAll();

        return view('roles/index', $this->dados);
    }

    public function novo(): string
    {
        $this->exigirPermissao('roles', 'cadastrar');
        $this->dados['titulo'] = 'Novo Perfil';

        return view('roles/form', $this->dados);
    }

    public function salvar(): RedirectResponse
    {
        $this->exigirPermissao('roles', 'cadastrar');

        if (! $this->validate($this->roles->validationRules)) {
            return redirect()->back()->withInput()->with('erros', $this->validator->getErrors());
        }

        $dados = [
            'nome'      => trim((string) $this->request->getPost('nome')),
            'slug'      => trim((string) $this->request->getPost('slug')) ?: url_title($this->request->getPost('nome'), '-', true),
            'descricao' => trim((string) $this->request->getPost('descricao')),
        ];

        $this->roles->insert($dados);
        $id = (int) $this->roles->getInsertID();

        $this->sincronizar($id);
        (new Auditoria())->log('criar', 'roles', $id, null, $dados);

        return redirect()->to('/roles')->with('sucesso', 'Perfil cadastrado com sucesso!');
    }

    public function editar(int $id): string
    {
        $this->exigirPermissao('roles', 'editar');
        $this->dados['titulo']      = 'Editar Perfil';
        $this->dados['registro']    = $this->roles->find($id);
        $this->dados['permissoes']  = $this->permissoes->getAgrupadas();
        $this->dados['selecionadas'] = array_column($this->roles->getPermissoes($id), 'id');

        return view('roles/form', $this->dados);
    }

    public function atualizar(int $id): RedirectResponse
    {
        $this->exigirPermissao('roles', 'editar');

        $antes = $this->roles->find($id);
        if ($antes === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $rules = $this->roles->validationRules;
        $rules['slug'] = str_replace('{id}', (string) $id, $rules['slug']) . "|is_unique[roles.slug,id,{$id}]";

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('erros', $this->validator->getErrors());
        }

        $dados = [
            'nome'      => trim((string) $this->request->getPost('nome')),
            'slug'      => trim((string) $this->request->getPost('slug')) ?: $antes['slug'],
            'descricao' => trim((string) $this->request->getPost('descricao')),
        ];

        $this->roles->update($id, $dados);
        $this->sincronizar($id);

        (new Auditoria())->log('editar', 'roles', $id, $antes, $dados);

        return redirect()->to('/roles')->with('sucesso', 'Perfil atualizado com sucesso!');
    }

    public function excluir(int $id): RedirectResponse
    {
        $this->exigirPermissao('roles', 'excluir');

        $role = $this->roles->find($id);
        if ($role !== null) {
            if ((bool) $role['super_admin']) {
                return redirect()->back()->with('erro', 'O perfil Super Administrador não pode ser excluído.');
            }
            $emUso = db_connect()->table('usuarios')->where('role_id', $id)->countAllResults();
            if ($emUso > 0) {
                return redirect()->back()->with('erro', 'Existem usuários vinculados a este perfil.');
            }

            $this->roles->delete($id);
            (new Auditoria())->log('excluir', 'roles', $id, $role);
        }

        return redirect()->to('/roles')->with('sucesso', 'Perfil excluído com sucesso!');
    }

    private function sincronizar(int $roleId): void
    {
        $selecionadas = (array) ($this->request->getPost('permissoes') ?? []);
        $this->roles->sincronizarPermissoes($roleId, $selecionadas);
    }
}
