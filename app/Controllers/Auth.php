<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Libraries\Auditoria;
use App\Models\UsuarioModel;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Autenticação: login, logout e alteração de senha.
 */
class Auth extends BaseController
{
    private const MAX_TENTATIVAS = 5;
    private const MINUTOS_BLOQUEIO = 15;

    private UsuarioModel $usuarios;

    public function __construct()
    {
        $this->usuarios = new UsuarioModel();
        helper(['form', 'text', 'util']);
    }

    public function login(): string|RedirectResponse
    {
        if (session()->get('logado')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login', $this->dados);
    }

    public function autenticar(): RedirectResponse
    {
        $rules = [
            'email' => 'required|valid_email',
            'senha' => 'required|min_length[6]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('erros', $this->validator->getErrors());
        }

        $email = $this->request->getPost('email');
        $senha = (string) $this->request->getPost('senha');

        $usuario = $this->usuarios->buscarPorEmail($email);

        if ($usuario === null || ! password_verify($senha, $usuario['senha_hash'])) {
            if ($usuario !== null) {
                $this->registrarFalha($usuario);
            }
            (new Auditoria())->log('login_falha', 'auth', isset($usuario['id']) ? (int) $usuario['id'] : null);

            return redirect()->back()->withInput()->with('erro', 'E-mail ou senha inválidos.');
        }

        if (! (bool) $usuario['ativo']) {
            return redirect()->back()->withInput()->with('erro', 'Sua conta está inativa. Contate o administrador.');
        }

        if ((bool) $usuario['bloqueado']) {
            return redirect()->back()->withInput()->with('erro', 'Sua conta está bloqueada. Contate o administrador.');
        }

        if ($usuario['bloqueado_ate'] !== null && strtotime($usuario['bloqueado_ate']) > time()) {
            return redirect()->back()->withInput()->with('erro', 'Conta temporariamente bloqueada por tentativas excessivas. Tente novamente mais tarde.');
        }

        $this->iniciarSessao($usuario);
        (new Auditoria())->log('login', 'auth', (int) $usuario['id']);

        return redirect()->to('/dashboard')->with('sucesso', 'Bem-vindo(a), ' . $usuario['nome'] . '!');
    }

    public function logout(): RedirectResponse
    {
        if (session()->get('logado')) {
            (new Auditoria())->log('logout', 'auth', (int) session()->get('usuario')['id']);
        }
        session()->destroy();

        return redirect()->to('/login')->with('sucesso', 'Sessão encerrada com sucesso.');
    }

    public function senha(): string|RedirectResponse
    {
        return view('auth/senha', $this->dados);
    }

    public function alterarSenha(): RedirectResponse
    {
        $rules = [
            'senha_atual'     => 'required',
            'senha'           => 'required|min_length[8]|max_length[72]',
            'confirma_senha'  => 'required|matches[senha]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('erros', $this->validator->getErrors());
        }

        $usuarioId = (int) session()->get('usuario')['id'];
        $usuario   = $this->usuarios->find($usuarioId);

        if ($usuario === null || ! password_verify((string) $this->request->getPost('senha_atual'), $usuario['senha_hash'])) {
            return redirect()->back()->with('erro', 'A senha atual está incorreta.');
        }

        $this->usuarios->update($usuarioId, [
            'senha'              => $this->request->getPost('senha'),
            'deve_alterar_senha' => 0,
        ]);

        (new Auditoria())->log('alterar_senha', 'auth', $usuarioId);

        return redirect()->to('/dashboard')->with('sucesso', 'Senha alterada com sucesso!');
    }

    private function iniciarSessao(array $usuario): void
    {
        $roleModel = new \App\Models\RoleModel();
        $role      = $roleModel->withDeleted()->find((int) $usuario['role_id']);
        $permissoesList = [];
        if (! ($role['super_admin'] ?? false)) {
            foreach ($roleModel->getPermissoes((int) $usuario['role_id']) as $p) {
                $permissoesList[] = $p['modulo'] . ':' . $p['acao'];
            }
        }

        session()->regenerate(true);
        session()->set([
            'logado'      => true,
            'usuario'     => [
                'id'          => (int) $usuario['id'],
                'nome'        => $usuario['nome'],
                'email'       => $usuario['email'],
                'foto'        => $usuario['foto'],
                'role'        => $role['nome'] ?? '',
                'super_admin' => (bool) ($role['super_admin'] ?? false),
            ],
            'permissoes'  => $permissoesList,
            'deve_alterar_senha' => (bool) $usuario['deve_alterar_senha'],
        ]);

        $this->usuarios->update((int) $usuario['id'], [
            'tentativas_login' => 0,
            'bloqueado_ate'    => null,
            'ultimo_login'     => date('Y-m-d H:i:s'),
        ]);
    }

    private function registrarFalha(array $usuario): void
    {
        $tentativas = (int) $usuario['tentativas_login'] + 1;

        $dados = ['tentativas_login' => $tentativas];

        if ($tentativas >= self::MAX_TENTATIVAS) {
            $dados['bloqueado_ate'] = date('Y-m-d H:i:s', time() + self::MINUTOS_BLOQUEIO * 60);
            $dados['tentativas_login'] = 0;
        }

        $this->usuarios->update((int) $usuario['id'], $dados);
    }
}
