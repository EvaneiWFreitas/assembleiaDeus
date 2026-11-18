<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Classe base dos controllers.
 */
abstract class Controller
{
    protected Request $request;

    public function __construct()
    {
        $this->request = Request::capture();
    }

    protected function render(string $view, array $data = [], string $layout = 'site', int $status = 200): void
    {
        View::render($view, $data, $layout, $status);
    }

    protected function json(array $payload, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    protected function redirect(string $path = ''): void
    {
        header('Location: ' . url($path));
        exit;
    }

    protected function abort(int $code = 404): never
    {
        http_response_code($code);
        exit;
    }
}
