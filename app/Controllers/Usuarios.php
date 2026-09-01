<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Libraries\Auditoria;
use App\Models\RoleModel;
use App\Models\UsuarioModel;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * CRUD de usuários do sistema.
 */
class Usuarios extends BaseController
{
    private UsuarioModel $usuarios;
    private RoleModel $roles;

    public function __construct()
    {
        $this->usuarios = new UsuarioModel();
        $this->roles    = new RoleModel();
    }

    public function index(): string
    {
        $this->exigirPermissao('usuarios', 'visualizar');

        $busca = $this->request->getGet('q');
        $pagina = max(1, (int) ($this->request->getGet('page') ?? 1));
        $porPagina = 20;

        $this->dados['usuarios'] = $this->usuarios->listarComRoles($busca, $porPagina, ($pagina - 1) * $porPagina);
        $this->dados['total']    = $this->usuarios->countAllResults();
        $this->dados['busca']    = $busca;
        $this->dados['pagina']   = $pagina;
        $this->dados['porPagina'] = $porPagina;
        $this->dados['titulo']   = 'Usuários';

        return view('usuarios/index', $this->dados);
    }

    public function novo(): string
    {
        $this->exigirPermissao('usuarios', 'cadastrar');
        $this->dados['titulo'] = 'Novo Usuário';
        $this->dados['roles']  = $this->roles->findAll();

        return view('usuarios/form', $this->dados);
    }

    public function salvar(): RedirectResponse
    {
        $this->exigirPermissao('usuarios', 'cadastrar');

        $rules = [
            'nome'    => 'required|min_length[3]|max_length[150]',
            'email'   => 'required|valid_email|is_unique[usuarios.email]',
            'senha'   => 'required|min_length[8]|max_length[72]',
            'role_id' => 'required|integer|is_not_unique[roles.id]',
            'confirmar_senha' => 'required|matches[senha]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('erros', $this->validator->getErrors());
        }

        $dados = $this->dadosPost();
        $dados['senha'] = (string) $this->request->getPost('senha');

        $this->usuarios->insert($dados);
        $id = (int) $this->usuarios->getInsertID();

        (new Auditoria())->log('criar', 'usuarios', $id, null, ['nome' => $dados['nome'], 'email' => $dados['email']]);

        return redirect()->to('/usuarios')->with('sucesso', 'Usuário cadastrado com sucesso!');
    }

    public function editar(int $id): string
    {
        $this->exigirPermissao('usuarios', 'editar');

        $this->dados['titulo']   = 'Editar Usuário';
        $this->dados['registro'] = $this->usuarios->find($id);
        $this->dados['roles']    = $this->roles->findAll();

        return view('usuarios/form', $this->dados);
    }

    public function atualizar(int $id): RedirectResponse
    {
        $this->exigirPermissao('usuarios', 'editar');

        $antes = $this->usuarios->find($id);
        if ($antes === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $rules = [
            'nome'    => 'required|min_length[3]|max_length[150]',
            'email'   => "required|valid_email|is_unique[usuarios.email,id,{$id}]",
            'senha'   => 'permit_empty|min_length[8]|max_length[72]',
            'role_id' => 'required|integer|is_not_unique[roles.id]',
            'confirmar_senha' => 'permit_empty|matches[senha]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('erros', $this->validator->getErrors());
        }

        $dados = $this->dadosPost();
        $senha = (string) $this->request->getPost('senha');
        if ($senha !== '') {
            $dados['senha'] = $senha;
        }

        if (! $this->usuarios->update($id, $dados)) {
            return redirect()->back()->withInput()->with('erro', 'Não foi possível atualizar o usuário. Tente novamente.');
        }

        (new Auditoria())->log('editar', 'usuarios', $id, ['nome' => $antes['nome'], 'email' => $antes['email'], 'role_id' => $antes['role_id']], $dados);

        return redirect()->to('/usuarios')->with('sucesso', 'Usuário atualizado com sucesso!');
    }

    public function excluir(int $id): RedirectResponse
    {
        $this->exigirPermissao('usuarios', 'excluir');

        if ($id === (int) session()->get('usuario')['id']) {
            return redirect()->back()->with('erro', 'Você não pode excluir seu próprio usuário.');
        }

        $antes = $this->usuarios->find($id);
        if ($antes !== null) {
            if (! $this->usuarios->delete($id)) {
                return redirect()->back()->with('erro', 'Não foi possível excluir o usuário. Tente novamente.');
            }
            (new Auditoria())->log('excluir', 'usuarios', $id, ['nome' => $antes['nome'], 'email' => $antes['email']]);
        }

        return redirect()->to('/usuarios')->with('sucesso', 'Usuário excluído com sucesso!');
    }

    public function alternarBloqueio(int $id): RedirectResponse
    {
        $this->exigirPermissao('usuarios', 'editar');

        $usuario = $this->usuarios->find($id);
        if ($usuario !== null) {
            $this->usuarios->update($id, ['bloqueado' => (int) ! ((bool) $usuario['bloqueado'])]);
            (new Auditoria())->log('editar', 'usuarios', $id, ['bloqueado' => $usuario['bloqueado']], ['bloqueado' => (int) ! ((bool) $usuario['bloqueado'])]);
        }

        return redirect()->to('/usuarios')->with('sucesso', 'Situação do usuário atualizada.');
    }

    private function dadosPost(): array
    {
        return [
            'nome'    => trim((string) $this->request->getPost('nome')),
            'email'   => trim((string) $this->request->getPost('email')),
            'telefone' => trim((string) $this->request->getPost('telefone')),
            'role_id' => (int) $this->request->getPost('role_id'),
            'ativo'   => $this->request->getPost('ativo') ? 1 : 0,
        ];
    }
}
