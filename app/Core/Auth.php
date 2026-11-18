<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Autenticacao do painel administrativo.
 * Mantem o usuario na sessao e registra acessos em logs_acesso.
 */
final class Auth
{
    private static ?self $instance = null;

    private ?array $user = null;
    private bool $loaded = false;

    public static function instance(): self
    {
        self::$instance ??= new self();

        return self::$instance;
    }

    public static function check(): bool
    {
        return self::instance()->currentUser() !== null;
    }

    public static function id(): ?int
    {
        $user = self::instance()->currentUser();

        return $user === null ? null : (int) $user['id'];
    }

    public static function user(): ?array
    {
        return self::instance()->currentUser();
    }

    public static function perfil(): ?array
    {
        $user = self::instance()->currentUser();
        if ($user === null) {
            return null;
        }

        return [
            'id'    => (int) $user['perfil_id'],
            'nome'  => (string) $user['perfil_nome'],
            'slug'  => (string) $user['perfil_slug'],
            'nivel' => (int) $user['perfil_nivel'],
        ];
    }

    public static function congregacaoId(): ?int
    {
        $user = self::instance()->currentUser();
        if ($user === null || empty($user['congregacao_id'])) {
            return null;
        }

        return (int) $user['congregacao_id'];
    }

    /**
     * @return array{ok: bool, message: string, must_change_password?: bool}
     */
    public static function attempt(string $email, string $senha): array
    {
        $email = mb_strtolower(trim($email));

        $usuario = Database::fetch(
            'SELECT u.*, p.nome AS perfil_nome, p.slug AS perfil_slug, p.nivel AS perfil_nivel
               FROM usuarios u
               INNER JOIN perfis p ON p.id = u.perfil_id
              WHERE u.email = ?
              LIMIT 1',
            [$email]
        );

        if ($usuario === null) {
            Audit::access(null, 'LOGIN_FALHA', 'Tentativa com e-mail não cadastrado');

            return ['ok' => false, 'message' => 'E-mail ou senha inválidos.'];
        }

        if (!empty($usuario['bloqueado_ate']) && strtotime((string) $usuario['bloqueado_ate']) <= time()) {
            Database::execute(
                'UPDATE usuarios SET tentativas_falha = 0, bloqueado_ate = NULL WHERE id = ?',
                [(int) $usuario['id']]
            );
            $usuario['tentativas_falha'] = 0;
            $usuario['bloqueado_ate'] = null;
        }

        if (!empty($usuario['bloqueado_ate'])) {
            Audit::access(null, 'BLOQUEIO', 'Tentativa de login em conta bloqueada: ' . $email);

            return [
                'ok' => false,
                'message' => 'Conta bloqueada por excesso de tentativas. Tente novamente em alguns minutos.',
            ];
        }

        if ((int) $usuario['ativo'] !== 1 || !password_verify($senha, (string) $usuario['senha_hash'])) {
            self::registerFailure($usuario, $email);

            return ['ok' => false, 'message' => 'E-mail ou senha inválidos.'];
        }

        if (senhaPrecisaRehash((string) $usuario['senha_hash'])) {
            Database::execute(
                'UPDATE usuarios SET senha_hash = ? WHERE id = ?',
                [hashSenha($senha), (int) $usuario['id']]
            );
        }

        Database::execute(
            'UPDATE usuarios
                SET ultimo_acesso = NOW(), tentativas_falha = 0, bloqueado_ate = NULL
              WHERE id = ?',
            [(int) $usuario['id']]
        );

        self::instance()->login((int) $usuario['id']);
        Audit::access((int) $usuario['id'], 'LOGIN', 'Login realizado com sucesso');

        return [
            'ok' => true,
            'must_change_password' => (int) $usuario['must_change_password'] === 1,
            'message' => 'Login realizado com sucesso.',
        ];
    }

    public function login(int $userId): void
    {
        $_SESSION['auth_user_id'] = $userId;
        $_SESSION['auth_last_activity'] = time();
        unset($_SESSION['auth_permissions']);

        session_regenerate_id(true);

        $this->user = null;
        $this->loaded = false;
    }

    public static function logout(): void
    {
        $usuario = self::instance()->currentUser();
        if ($usuario !== null) {
            Audit::access((int) $usuario['id'], 'LOGOUT', 'Sessão encerrada');
        }

        self::instance()->clear();
        session_regenerate_id(true);
    }

    public static function touch(): void
    {
        $_SESSION['auth_last_activity'] = time();
    }

    public static function clearPermissionsCache(): void
    {
        unset($_SESSION['auth_permissions']);
    }

    private function currentUser(): ?array
    {
        if ($this->loaded) {
            return $this->user;
        }

        $this->loaded = true;
        $id = $this->sessionUserId();
        if ($id === null) {
            return $this->user = null;
        }

        $usuario = Database::fetch(
            'SELECT u.*,
                    p.nome AS perfil_nome, p.slug AS perfil_slug, p.nivel AS perfil_nivel,
                    c.nome AS congregacao_nome
               FROM usuarios u
               INNER JOIN perfis p ON p.id = u.perfil_id
               LEFT JOIN congregacoes c ON c.id = u.congregacao_id
              WHERE u.id = ? AND u.ativo = 1
              LIMIT 1',
            [$id]
        );

        if ($usuario === null) {
            $this->clear();

            return $this->user = null;
        }

        return $this->user = $usuario;
    }

    private static function registerFailure(array $usuario, string $email): void
    {
        $maxTentativas = max(1, (int) config('app.security.login_max_attempts', 5));
        $minutosBloqueio = max(1, (int) config('app.security.login_lock_minutes', 15));
        $tentativas = (int) $usuario['tentativas_falha'] + 1;
        $bloqueadoAte = $tentativas >= $maxTentativas
            ? date('Y-m-d H:i:s', time() + ($minutosBloqueio * 60))
            : null;

        Database::execute(
            'UPDATE usuarios SET tentativas_falha = ?, bloqueado_ate = ? WHERE id = ?',
            [$tentativas, $bloqueadoAte, (int) $usuario['id']]
        );

        Audit::access((int) $usuario['id'], 'LOGIN_FALHA', 'Senha inválida para ' . $email);

        if ($bloqueadoAte !== null) {
            Audit::access(null, 'BLOQUEIO', 'Usuário id ' . (int) $usuario['id'] . ' bloqueado');
        }
    }

    private function sessionUserId(): ?int
    {
        $id = $_SESSION['auth_user_id'] ?? null;

        return is_numeric($id) ? (int) $id : null;
    }

    private function clear(): void
    {
        unset(
            $_SESSION['auth_user_id'],
            $_SESSION['auth_last_activity'],
            $_SESSION['auth_permissions']
        );

        $this->user = null;
        $this->loaded = false;
    }
}
