<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/**
 * Cargos ministeriais disponíveis para obreiros.
 */
final class CargoMinisterial
{
    public static function options(): array
    {
        return Database::fetchAll(
            'SELECT id, nome FROM cargos_ministeriais WHERE ativo = 1 ORDER BY nome'
        );
    }

    public static function existe(int $id): bool
    {
        return (int) Database::fetchColumn(
            'SELECT COUNT(*) FROM cargos_ministeriais WHERE id = ? AND ativo = 1',
            [$id]
        ) > 0;
    }
}
