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
