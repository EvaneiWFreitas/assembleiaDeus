<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/**
 * Configuracoes institucionais gravadas no banco (tabela configuracoes).
 */
final class Configuracao
{
    private static ?array $cache = null;

    public static function all(): array
    {
        if (self::$cache === null) {
            $rows = Database::fetchAll('SELECT `chave`, `valor` FROM `configuracoes`');
            self::$cache = [];

            foreach ($rows as $row) {
                self::$cache[$row['chave']] = $row['valor'];
            }
        }

        return self::$cache;
    }

    public static function get(string $chave, string $default = ''): string
    {
        $value = self::all()[$chave] ?? $default;

        return $value === null ? $default : (string) $value;
    }

    public static function set(string $chave, string $valor): void
    {
        Database::execute(
            'INSERT INTO `configuracoes` (`chave`, `valor`)
             VALUES (:chave, :valor)
             ON DUPLICATE KEY UPDATE `valor` = VALUES(`valor`)',
            [
                ':chave' => mb_substr($chave, 0, 80),
                ':valor' => $valor,
            ]
        );

        self::flush();
    }

    public static function flush(): void
    {
        self::$cache = null;
    }
}
