<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/**
 * Usuarios do painel administrativo.
 */
final class Usuario
{
    public static function count(): int
    {
        return (int) Database::fetchColumn('SELECT COUNT(*) FROM usuarios');
    }

    public static function find(int $id): ?array
    {
        return Database::fetch(
            'SELECT u.*, p.nome AS perfil_nome, p.nivel AS perfil_nivel
               FROM usuarios u
              INNER JOIN perfis p ON p.id = u.perfil_id
              WHERE u.id = ?
              LIMIT 1',
            [$id]
        );
    }

    public static function findByEmail(string $email): ?array
    {
        return Database::fetch(
            'SELECT * FROM usuarios WHERE email = ? LIMIT 1',
            [mb_strtolower(trim($email))]
        );
    }

    public static function criarSuperadmin(string $nome, string $email, string $senhaHash): string
    {
        return Database::insert(
            'INSERT INTO usuarios
                (nome, email, senha_hash, perfil_id, congregacao_id, must_change_password, ativo)
             VALUES
                (:nome, :email, :senha_hash, 1, NULL, 1, 1)',
            [
                ':nome'       => trim($nome),
                ':email'      => mb_strtolower(trim($email)),
                ':senha_hash' => $senhaHash,
            ]
        );
    }

    public static function atualizarSenha(int $id, string $senhaHash, bool $mustChange = false): void
    {
        Database::execute(
            'UPDATE usuarios
                SET senha_hash = ?, must_change_password = ?
              WHERE id = ?',
            [$senhaHash, $mustChange ? 1 : 0, $id]
        );
    }

    public static function emailExiste(string $email, ?int $ignorarId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM usuarios WHERE email = ?';
        $params = [mb_strtolower(trim($email))];

        if ($ignorarId !== null) {
            $sql .= ' AND id <> ?';
            $params[] = $ignorarId;
        }

        return (int) Database::fetchColumn($sql, $params) > 0;
    }

    public static function perfisOptions(): array
    {
        return Database::fetchAll(
            'SELECT id, nome, nivel
               FROM perfis
              WHERE ativo = 1
              ORDER BY nivel DESC, nome ASC'
        );
    }

    public static function contarSuperadmins(): int
    {
        return (int) Database::fetchColumn(
            'SELECT COUNT(*)
               FROM usuarios u
              INNER JOIN perfis p ON p.id = u.perfil_id
              WHERE p.nivel >= 100 AND u.ativo = 1'
        );
    }

    public static function create(array $dados): string
    {
        return Database::insert(
            'INSERT INTO usuarios
                (nome, email, senha_hash, perfil_id, congregacao_id, telefone,
                 must_change_password, ativo)
             VALUES
                (:nome, :email, :senha_hash, :perfil_id, :congregacao_id, :telefone,
                 :must_change_password, :ativo)',
            [
                ':nome'                => $dados['nome'],
                ':email'               => $dados['email'],
                ':senha_hash'          => $dados['senha_hash'],
                ':perfil_id'           => $dados['perfil_id'],
                ':congregacao_id'      => $dados['congregacao_id'],
                ':telefone'            => $dados['telefone'],
                ':must_change_password' => $dados['must_change_password'],
                ':ativo'               => $dados['ativo'],
            ]
        );
    }

    public static function update(int $id, array $dados): void
    {
        $sql = 'UPDATE usuarios
                   SET nome = :nome, email = :email, perfil_id = :perfil_id,
                       congregacao_id = :congregacao_id, telefone = :telefone,
                       must_change_password = :must_change_password, ativo = :ativo';
        $params = [
            ':nome'                 => $dados['nome'],
            ':email'                => $dados['email'],
            ':perfil_id'            => $dados['perfil_id'],
            ':congregacao_id'       => $dados['congregacao_id'],
            ':telefone'             => $dados['telefone'],
            ':must_change_password' => $dados['must_change_password'],
            ':ativo'                => $dados['ativo'],
        ];

        if (!empty($dados['senha_hash'])) {
            $sql .= ', senha_hash = :senha_hash';
            $params[':senha_hash'] = $dados['senha_hash'];
        }

        $sql .= ' WHERE id = :id';
        $params[':id'] = $id;

        Database::execute($sql, $params);
    }

    public static function delete(int $id): void
    {
        Database::execute('DELETE FROM usuarios WHERE id = ?', [$id]);
    }

    public static function all(): array
    {
        return Database::fetchAll(
            'SELECT u.id, u.nome, u.email, u.ativo, u.must_change_password,
                    u.ultimo_acesso, u.created_at,
                    p.nome AS perfil_nome, p.nivel AS perfil_nivel,
                    c.nome AS congregacao_nome
               FROM usuarios u
               INNER JOIN perfis p ON p.id = u.perfil_id
               LEFT JOIN congregacoes c ON c.id = u.congregacao_id
              ORDER BY p.nivel DESC, u.nome ASC'
        );
    }
}
