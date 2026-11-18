<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Resolvedor de rotas com parametros nomeados.
 * Exemplo de padrao: noticias/{slug}
 */
final class Router
{
    private array $routes;
    private string $namespace;
    private string $notFoundController;

    public function __construct(
        array $routes,
        string $namespace = 'App\\Controllers\\',
        string $notFoundController = 'SiteController'
    ) {
        $this->namespace = rtrim($namespace, '\\') . '\\';
        $this->notFoundController = $notFoundController;
        $this->routes = [];

        foreach ($routes as $pattern => $definition) {
            if (is_int($pattern)) {
                $pattern = (string) ($definition['pattern'] ?? '');
            }

            $this->routes[] = [
                'pattern'    => (string) $pattern,
                'definition' => $definition,
            ];
        }
    }

    public function dispatch(string $route, string $method = 'GET'): void
    {
        $route = trim(rawurldecode($route), '/');

        foreach ($this->routes as $rota) {
            $definition = $rota['definition'];
            $params = $this->match($rota['pattern'], $route);
            if ($params === null) {
                continue;
            }

            $metodos = (array) ($definition['method'] ?? 'GET');
            $metodos = array_map(static fn (string $item): string => strtoupper($item), $metodos);
            if (!in_array('ANY', $metodos, true) && !in_array(strtoupper($method), $metodos, true)) {
                continue;
            }

            [$controllerName, $action] = explode('@', (string) $definition['uses'], 2);
            $class = $this->namespace . $controllerName;

            if (!class_exists($class)) {
                throw new \RuntimeException("Controller nao encontrado: {$class}");
            }

            $controller = new $class();
            if (!method_exists($controller, $action)) {
                throw new \RuntimeException("Acao nao encontrada: {$class}@{$action}");
            }

            $arguments = array_merge($params, $definition['params'] ?? []);
            $controller->{$action}(...$arguments);

            return;
        }

        http_response_code(404);
        $class = $this->namespace . $this->notFoundController;
        (new $class())->notFound();
    }

    private function match(string $pattern, string $route): ?array
    {
        $segments   = explode('/', trim($pattern, '/'));
        $routeParts = explode('/', $route);
        $regex      = [];

        foreach ($segments as $segment) {
            if (preg_match('/^\{(\w+)\}$/', $segment, $matches)) {
                $regex[] = '(?P<' . $matches[1] . '>[^/]+)';
                continue;
            }
            $regex[] = preg_quote($segment, '#');
        }

        $patternRegex = '#^' . implode('/', $regex) . '$#';

        if (!preg_match($patternRegex, $route, $matches)) {
            return null;
        }

        $params = [];
        foreach ($matches as $key => $value) {
            if (is_string($key)) {
                $params[$key] = $value;
            }
        }

        return $params;
    }
}
