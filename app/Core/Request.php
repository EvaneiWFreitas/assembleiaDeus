<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Abstracao da requisicao HTTP (entrada).
 */
final class Request
{
    public readonly string $method;
    public readonly string $uri;

    private array $query;
    private array $post;
    private array $files;
    private array $server;

    private function __construct()
    {
        $this->server = $_SERVER;
        $this->method = strtoupper((string) ($this->server['REQUEST_METHOD'] ?? 'GET'));
        $this->uri    = (string) ($this->server['REQUEST_URI'] ?? '/');
        $this->query  = $_GET;
        $this->post   = $_POST;
        $this->files  = $_FILES;
    }

    public static function capture(): self
    {
        static $instance = null;
        $instance ??= new self();

        return $instance;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->post[$key] ?? $this->query[$key] ?? $default;
    }

    public function post(string $key, mixed $default = null): mixed
    {
        return $this->post[$key] ?? $default;
    }

    public function query(string $key, mixed $default = null): mixed
    {
        return $this->query[$key] ?? $default;
    }

    public function file(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }

    public function isMethod(string $method): bool
    {
        return $this->method === strtoupper($method);
    }

    public function isPost(): bool
    {
        return $this->isMethod('POST');
    }

    public function isAjax(): bool
    {
        return strtolower((string) ($this->server['HTTP_X_REQUESTED_WITH'] ?? '')) === 'xmlhttprequest';
    }

    public function ip(): string
    {
        return (string) ($this->server['HTTP_X_FORWARDED_FOR'] ?? $this->server['REMOTE_ADDR'] ?? '');
    }

    public function userAgent(): string
    {
        return (string) ($this->server['HTTP_USER_AGENT'] ?? '');
    }

    /** @return array<string, mixed> */
    public function only(array $keys): array
    {
        $data = [];
        foreach ($keys as $key) {
            $data[$key] = $this->input($key);
        }

        return $data;
    }
}
