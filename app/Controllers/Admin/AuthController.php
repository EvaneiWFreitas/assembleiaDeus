<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\AdminController;
use App\Core\Audit;
use App\Core\Auth;
use App\Core\Flash;
use App\Models\Usuario;

/**
 * Login, instalacao inicial, troca de senha e perfil do usuario.
 */
final class AuthController extends AdminController
{
    public function loginForm(): void
    {
        if (Auth::check()) {
            $this->redirectAdmin();
        }

        if (Usuario::count() === 0) {
            $this->redirectAdmin('instalar');
        }

        $this->renderAuth('admin.auth.login', [
            'title' => 'Entrar no painel',
            'email' => old('email'),
        ]);
    }

    public function login(): void
    {
        if (Auth::check()) {
            $this->redirectAdmin();
        }

        if (Usuario::count() === 0) {
            $this->redirectAdmin('instalar');
        }

        $email = trim((string) $this->request->post('email', ''));
        $senha = (string) $this->request->post('senha', '');

        if ($email === '' || $senha === '') {
            Flash::error('Informe o e-mail e a senha.');
            $this->rememberEmail($email);
            $this->redirectAdmin('login');
        }

        $resultado = Auth::attempt($email, $senha);
        if (!$resultado['ok']) {
            Flash::error((string) $resultado['message']);
            $this->rememberEmail($email);
            $this->redirectAdmin('login');
        }

        unset($_SESSION['old']);
        Flash::success('Bem-vindo ao painel administrativo.');
        $this->redirectAdmin(($resultado['must_change_password'] ?? false) ? 'trocar-senha' : 'dashboard');
    }

    public function logout(): void
    {
        if (Auth::check()) {
            Auth::logout();
        }

        Flash::info('Sua sessão foi encerrada.');
        $this->redirectAdmin('login');
    }

    public function instalarForm(): void
    {
        if (Usuario::count() > 0) {
            Flash::warning('A instalação inicial já foi concluída.');
            $this->redirectAdmin('login');
        }

        $this->renderAuth('admin.auth.instalar', [
            'title' => 'Instalação inicial',
            'nome'  => old('nome'),
            'email' => old('email'),
        ]);
    }

    public function instalar(): void
    {
        if (Usuario::count() > 0) {
            Flash::warning('A instalação inicial já foi concluída.');
            $this->redirectAdmin('login');
        }

        $nome = trim((string) $this->request->post('nome', ''));
        $email = mb_strtolower(trim((string) $this->request->post('email', '')));
        $senha = (string) $this->request->post('senha', '');
        $confirmacao = (string) $this->request->post('senha_confirmacao', '');

        $_SESSION['old'] = ['nome' => $nome, 'email' => $email];

        if (mb_strlen($nome) < 3) {
            Flash::error('Informe o nome completo do administrador.');
            $this->redirectAdmin('instalar');
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            Flash::error('Informe um e-mail válido.');
            $this->redirectAdmin('instalar');
        }

        if (mb_strlen($senha) < 8) {
            Flash::error('A senha deve ter ao menos 8 caracteres.');
            $this->redirectAdmin('instalar');
        }

        if ($senha !== $confirmacao) {
            Flash::error('As senhas informadas não conferem.');
            $this->redirectAdmin('instalar');
        }

        $usuarioId = Usuario::criarSuperadmin($nome, $email, hashSenha($senha));

        Audit::record(
            'criar',
            'seguranca',
            ['usuario_id' => (int) $usuarioId, 'email' => $email],
            [],
            'usuarios',
            (string) $usuarioId
        );

        Auth::instance()->login((int) $usuarioId);
        Audit::access((int) $usuarioId, 'LOGIN', 'Instalação inicial concluída');

        unset($_SESSION['old']);
        Flash::success('Administrador criado. Por segurança, redefine a senha abaixo.');
        $this->redirectAdmin('trocar-senha');
    }

    public function trocarSenhaForm(): void
    {
        $this->guard();

        $this->renderAuth('admin.auth.trocar-senha', [
            'title' => 'Alterar senha',
        ]);
    }

    public function trocarSenha(): void
    {
        $this->guard();

        $usuario = Auth::user();
        $atual = (string) $this->request->post('senha_atual', '');
        $nova = (string) $this->request->post('senha_nova', '');
        $confirmacao = (string) $this->request->post('senha_confirmacao', '');

        if (!password_verify($atual, (string) $usuario['senha_hash'])) {
            Flash::error('A senha atual está incorreta.');
            $this->redirectAdmin('trocar-senha');
        }

        if (mb_strlen($nova) < 8) {
            Flash::error('A nova senha deve ter ao menos 8 caracteres.');
            $this->redirectAdmin('trocar-senha');
        }

        if ($nova !== $confirmacao) {
            Flash::error('As novas senhas não conferem.');
            $this->redirectAdmin('trocar-senha');
        }

        if (password_verify($nova, (string) $usuario['senha_hash'])) {
            Flash::error('Escolha uma senha diferente da atual.');
            $this->redirectAdmin('trocar-senha');
        }

        Usuario::atualizarSenha((int) $usuario['id'], hashSenha($nova), false);
        Auth::clearPermissionsCache();

        Audit::access((int) $usuario['id'], 'TROCA_SENHA', 'Senha do usuário alterada');
        Audit::record('editar', 'seguranca', ['senha' => '***'], [], 'usuarios', (string) $usuario['id']);

        Flash::success('Senha alterada com sucesso.');
        $this->redirectAdmin();
    }

    public function perfilForm(): void
    {
        $this->guard();

        $this->renderAdmin('admin.auth.perfil', [
            'title'  => 'Meu perfil',
            'usuario' => Auth::user(),
            'perfil'  => Auth::perfil(),
        ]);
    }

    public function notFound(): void
    {
        $this->renderAdmin('admin.erro.404', ['title' => 'Página não encontrada'], 404);
    }

    private function rememberEmail(string $email): void
    {
        $_SESSION['old'] = ['email' => $email];
    }
}
