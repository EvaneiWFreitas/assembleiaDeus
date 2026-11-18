<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/**
 * Consultas de logs para a tela de auditoria.
 */
final class Acesso
{
    public static function ultimosAcessos(int $limite = 20): array
    {
        return Database::fetchAll(
            'SELECT l.acao, l.detalhes, l.ip, l.created_at, u.nome AS usuario_nome
               FROM logs_acesso l
               LEFT JOIN usuarios u ON u.id = l.usuario_id
              ORDER BY l.id DESC
              LIMIT ' . (int) $limite
        );
    }

    public static function ultimasAlteracoes(int $limite = 20): array
    {
        return Database::fetchAll(
            'SELECT a.acao, a.modulo, a.tabela, a.registro_id, a.created_at,
                    u.nome AS usuario_nome
               FROM auditoria a
               LEFT JOIN usuarios u ON u.id = a.usuario_id
              ORDER BY a.id DESC
              LIMIT ' . (int) $limite
        );
    }
}
