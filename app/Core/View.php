<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Renderizacao de views com layout e partials reutilizaveis.
 */
final class View
{
    public static function render(string $view, array $data = [], string $layout = 'site', int $status = 200): void
    {
        http_response_code($status);

        $file = ROOT_PATH . '/app/Views/' . str_replace('.', '/', $view) . '.php';
        if (!is_file($file)) {
            throw new \RuntimeException("View nao encontrada: {$view}");
        }

        extract($data, EXTR_SKIP);

        ob_start();
        require $file;
        $content = ob_get_clean();

        if ($layout === '') {
            echo $content;
            return;
        }

        $layoutFile = ROOT_PATH . '/app/Views/layouts/' . $layout . '.php';
        if (!is_file($layoutFile)) {
            throw new \RuntimeException("Layout nao encontrado: {$layout}");
        }

        require $layoutFile;
    }

    public static function partial(string $name, array $data = []): void
    {
        $file = ROOT_PATH . '/app/Views/partials/' . str_replace('.', '/', $name) . '.php';
        if (!is_file($file)) {
            throw new \RuntimeException("Partial nao encontrada: {$name}");
        }

        extract($data, EXTR_SKIP);
        require $file;
    }
}
