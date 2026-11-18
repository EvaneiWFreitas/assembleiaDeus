<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Valida o token CSRF em requisicoes que alteram estado.
 */
final class CsrfMiddleware
{
    private const METODOS = ['POST', 'PUT', 'PATCH', 'DELETE'];

    public static function handle(Request $request): void
    {
        if (!in_array($request->method, self::METODOS, true)) {
            return;
        }

        if (Csrf::validate($request->post('_csrf'))) {
            return;
        }

        header('HTTP/1.1 419 Session Expired', true, 419);
        header('Content-Type: text/html; charset=utf-8');
        echo '<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><title>Sessão expirada</title></head>'
            . '<body style="font-family:system-ui;padding:32px;max-width:640px;margin:auto">'
            . '<h1>Sessão expirada</h1>'
            . '<p>O token de segurança não é mais válido. Volte à página e tente novamente.</p>'
            . '<p><a href="' . e(url('admin/login')) . '">Ir para o login</a></p>'
            . '</body></html>';
        exit;
    }
}
