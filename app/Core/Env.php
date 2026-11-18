<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Leitor simples de arquivos .env, sem dependencias externas (ideal para FreeHost).
 * Suporta KEY=VALUE, linhas comentadas com #, aspas simples/duplas e prefixo export.
 */
final class Env
{
    /** @var array<string, string> */
    private static array $vars = [];

    private static bool $loaded = false;

    public static function load(string $path, bool $override = false): void
    {
        if (self::$loaded && !$override) {
            return;
        }

        if (!is_file($path)) {
            throw new \RuntimeException("Arquivo .env nao encontrado: {$path}");
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            throw new \RuntimeException("Nao foi possivel ler o arquivo .env: {$path}");
        }

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            if (str_starts_with($line, 'export ')) {
                $line = trim(substr($line, 7));
            }

            $separator = strpos($line, '=');
            if ($separator === false) {
                continue;
            }

            $key   = trim(substr($line, 0, $separator));
            $value = trim(substr($line, $separator + 1));

            if ($key === '') {
                continue;
            }

            if (strlen($value) >= 2) {
                $first = $value[0];
                $last  = $value[strlen($value) - 1];
                if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                    $value = substr($value, 1, -1);
                }
            }

            self::$vars[$key] = $value;
            $_ENV[$key] = $value;
        }

        self::$loaded = true;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return self::$vars[$key] ?? $_ENV[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        return array_key_exists($key, self::$vars) || array_key_exists($key, $_ENV);
    }

    /** @return array<string, string> */
    public static function all(): array
    {
        return self::$vars;
    }
}
