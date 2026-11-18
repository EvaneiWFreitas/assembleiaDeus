<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Mensagens flash armazenadas na sessao (sucesso, erro, aviso).
 */
final class Flash
{
    private const KEY = '_flash_messages';

    public static function success(string $message): void
    {
        self::add('success', $message);
    }

    public static function error(string $message): void
    {
        self::add('danger', $message);
    }

    public static function warning(string $message): void
    {
        self::add('warning', $message);
    }

    public static function info(string $message): void
    {
        self::add('info', $message);
    }

    private static function add(string $type, string $message): void
    {
        $_SESSION[self::KEY][] = ['type' => $type, 'message' => $message];
    }

    public static function consume(): array
    {
        $messages = $_SESSION[self::KEY] ?? [];
        unset($_SESSION[self::KEY]);

        return $messages;
    }

    public static function has(): bool
    {
        return !empty($_SESSION[self::KEY]);
    }
}
