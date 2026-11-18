<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Exige sessao autenticada para areas do painel.
 */
final class AuthMiddleware
{
    public static function handle(): void
    {
        if (!Auth::check()) {
            Flash::error('Entre com sua conta para acessar o painel.');
            header('Location: ' . url('admin/login'));
            exit;
        }

        Auth::touch();

        $usuario = Auth::user();
        if ($usuario === null || (int) $usuario['must_change_password'] !== 1) {
            return;
        }

        $uri = Request::capture()->uri;
        if (str_contains($uri, '/admin/trocar-senha') || str_contains($uri, '/admin/logout')) {
            return;
        }

        Flash::warning('Por segurança, altere sua senha antes de continuar.');
        header('Location: ' . url('admin/trocar-senha'));
        exit;
    }
}
