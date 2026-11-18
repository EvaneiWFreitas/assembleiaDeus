<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Base dos controllers do painel administrativo.
 */
abstract class AdminController extends Controller
{
    protected function guard(?string $permissao = null): void
    {
        AuthMiddleware::handle();
        AclMiddleware::handle($permissao);
    }

    protected function renderAdmin(string $view, array $data = [], int $status = 200): void
    {
        $this->render($view, $data, 'admin', $status);
    }

    protected function renderAuth(string $view, array $data = [], int $status = 200): void
    {
        $this->render($view, $data, 'auth', $status);
    }

    protected function redirectAdmin(string $path = 'dashboard'): void
    {
        $this->redirect('admin/' . ltrim($path, '/'));
    }
}
