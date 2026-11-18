<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Contato
{
    public static function criar(array $dados): string
    {
        Database::execute(
            'INSERT INTO `contatos`
                (`nome`, `email`, `telefone`, `assunto`, `mensagem`, `ip`)
             VALUES
                (:nome, :email, :telefone, :assunto, :mensagem, :ip)',
            [
                ':nome'     => $dados['nome'],
                ':email'    => $dados['email'],
                ':telefone' => $dados['telefone'],
                ':assunto'  => $dados['assunto'],
                ':mensagem' => $dados['mensagem'],
                ':ip'       => $dados['ip'],
            ]
        );

        return Database::connection()->lastInsertId();
    }

    public static function todas(): array
    {
        return Database::fetchAll(
            'SELECT `id`, `nome`, `email`, `telefone`, `assunto`, `mensagem`, `ip`,
                    `lido`, `respondido`, `respondido_por`, `respondido_em`, `created_at`
             FROM `contatos`
             ORDER BY `created_at` DESC, `id` DESC'
        );
    }

    public static function buscar(int $id): ?array
    {
        return Database::fetch(
            'SELECT `id`, `nome`, `email`, `telefone`, `assunto`, `mensagem`, `ip`,
                    `lido`, `respondido`, `respondido_por`, `respondido_em`, `created_at`
             FROM `contatos`
             WHERE `id` = :id
             LIMIT 1',
            [':id' => $id]
        );
    }

    public static function marcarLido(int $id): int
    {
        Database::execute(
            'UPDATE `contatos` SET `lido` = 1 WHERE `id` = :id',
            [':id' => $id]
        );

        return 1;
    }

    public static function marcarRespondido(int $id, int $usuarioId): int
    {
        Database::execute(
            'UPDATE `contatos`
             SET `respondido` = 1, `respondido_por` = :usuario, `respondido_em` = NOW()
             WHERE `id` = :id',
            [':id' => $id, ':usuario' => $usuarioId]
        );

        return 1;
    }

    public static function excluir(int $id): int
    {
        Database::execute('DELETE FROM `contatos` WHERE `id` = :id', [':id' => $id]);

        return 1;
    }

    public static function registrarConsentimento(int $contatoId, string $ip): void
    {
        Database::execute(
            'INSERT INTO `consentimentos_lgpd`
                (`titular_tipo`, `titular_id`, `finalidade`, `consentido`, `termo_versao`, `ip`)
             VALUES
                (:tipo, :id, :finalidade, 1, :versao, :ip)',
            [
                ':tipo'      => 'CONTATO',
                ':id'        => $contatoId,
                ':finalidade' => 'contato',
                ':versao'    => Configuracao::get('lgpd_termo_versao', '1.0'),
                ':ip'        => $ip,
            ]
        );
    }
}
