<?php

declare(strict_types=1);

/**
 * Funcoes auxiliares globais, carregadas no bootstrap.
 */

use App\Core\Acl;
use App\Core\Auth;
use App\Core\Config;
use App\Core\Env;

if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed
    {
        return Env::get($key, $default);
    }
}

if (!function_exists('config')) {
    function config(string $key, mixed $default = null): mixed
    {
        return Config::get($key, $default);
    }
}

if (!function_exists('e')) {
    function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        $base = rtrim((string) config('app.url', ''), '/');

        return $path === '' ? $base : $base . '/' . ltrim($path, '/');
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return url('assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('old')) {
    function old(string $key, mixed $default = ''): mixed
    {
        return $_SESSION['old'][$key] ?? $default;
    }
}

if (!function_exists('dataExtenso')) {
    function dataExtenso(?string $data): string
    {
        if ($data === null || $data === '') {
            return '';
        }

        $timestamp = strtotime($data);
        if ($timestamp === false) {
            return '';
        }

        $meses = [
            1 => 'janeiro', 2 => 'fevereiro', 3 => 'março', 4 => 'abril',
            5 => 'maio', 6 => 'junho', 7 => 'julho', 8 => 'agosto',
            9 => 'setembro', 10 => 'outubro', 11 => 'novembro', 12 => 'dezembro',
        ];

        $dia = (int) date('d', $timestamp);
        $mes = $meses[(int) date('n', $timestamp)];
        $ano = date('Y', $timestamp);

        return "{$dia} de {$mes} de {$ano}";
    }
}

if (!function_exists('soDigitos')) {
    function soDigitos(mixed $valor): string
    {
        return preg_replace('/\D+/', '', (string) $valor) ?? '';
    }
}

if (!function_exists('formatarCnpj')) {
    function formatarCnpj(mixed $cnpj): string
    {
        $digitos = soDigitos($cnpj);
        if (strlen($digitos) !== 14) {
            return (string) $cnpj;
        }

        return substr($digitos, 0, 2) . '.' . substr($digitos, 2, 3) . '.' . substr($digitos, 5, 3)
            . '/' . substr($digitos, 8, 4) . '-' . substr($digitos, 12, 2);
    }
}

if (!function_exists('formatarCpf')) {
    function formatarCpf(mixed $cpf): string
    {
        $digitos = soDigitos($cpf);
        if (strlen($digitos) !== 11) {
            return (string) $cpf;
        }

        return substr($digitos, 0, 3) . '.' . substr($digitos, 3, 3) . '.' . substr($digitos, 6, 3)
            . '-' . substr($digitos, 9, 2);
    }
}

if (!function_exists('hashSenha')) {
    function hashSenha(string $senha): string
    {
        $algo = defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_BCRYPT;

        return password_hash($senha, $algo);
    }
}

if (!function_exists('senhaPrecisaRehash')) {
    function senhaPrecisaRehash(string $hash): bool
    {
        $algo = defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_BCRYPT;

        return password_needs_rehash($hash, $algo);
    }
}

if (!function_exists('auth')) {
    function auth(): Auth
    {
        return Auth::instance();
    }
}

if (!function_exists('can')) {
    function can(string $permissao): bool
    {
        return Acl::can($permissao);
    }
}

if (!function_exists('dd')) {
    function dd(mixed ...$values): never
    {
        echo '<pre style="background:#0b1f3a;color:#fff;padding:16px;overflow:auto">';
        foreach ($values as $value) {
            var_export($value);
            echo "\n";
        }
        echo '</pre>';
        exit;
    }
}
