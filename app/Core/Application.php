<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Inicializador da aplicacao: constantes, autoload, configuracoes,
 * timezone, tratamento de erros e sessao segura.
 */
final class Application
{
    public static function boot(string $rootPath): void
    {
        $rootPath = rtrim(str_replace('\\', '/', $rootPath), '/');

        if (!defined('ROOT_PATH')) {
            define('ROOT_PATH', $rootPath);
        }

        require_once ROOT_PATH . '/app/Core/Env.php';
        require_once ROOT_PATH . '/app/Core/Config.php';
        require_once ROOT_PATH . '/app/Core/Database.php';
        require_once ROOT_PATH . '/app/Helpers/helpers.php';

        Env::load(ROOT_PATH . '/.env');
        self::registerAutoloader();
        Config::load(ROOT_PATH . '/config');

        date_default_timezone_set((string) Config::get('app.timezone', 'UTC'));
        mb_internal_encoding('UTF-8');

        self::configureErrors();
        self::configureSession();
    }

    private static function registerAutoloader(): void
    {
        spl_autoload_register(static function (string $class): void {
            $prefixes = [
                'App\\'    => ROOT_PATH . '/app/',
                'Modules\\' => ROOT_PATH . '/modules/',
            ];

            foreach ($prefixes as $prefix => $baseDirectory) {
                if (!str_starts_with($class, $prefix)) {
                    continue;
                }

                $relative = substr($class, strlen($prefix));
                $file     = $baseDirectory . str_replace('\\', '/', $relative) . '.php';

                if (is_file($file)) {
                    require_once $file;
                }

                return;
            }
        });
    }

    private static function configureErrors(): void
    {
        $debug = (bool) Config::get('app.debug', false);
        $log   = (string) Config::get('app.paths.logs', ROOT_PATH . '/storage/logs');

        if (!is_dir($log)) {
            @mkdir($log, 0755, true);
        }

        error_reporting(E_ALL);
        ini_set('display_errors', $debug ? '1' : '0');
        ini_set('display_startup_errors', $debug ? '1' : '0');
        ini_set('log_errors', '1');
        ini_set('error_log', $log . '/php-' . date('Y-m-d') . '.log');

        set_exception_handler(static function (\Throwable $e) use ($debug): void {
            error_log('[FATAL] ' . $e->getMessage() . ' em ' . $e->getFile() . ':' . $e->getLine());

            if (!headers_sent()) {
                http_response_code(500);
                header('Content-Type: text/html; charset=utf-8');
            }

            if ($debug) {
                echo '<pre style="font-family:monospace;padding:16px">';
                echo htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "\n";
                echo htmlspecialchars($e->getFile() . ':' . $e->getLine(), ENT_QUOTES, 'UTF-8') . "\n";
                echo htmlspecialchars($e->getTraceAsString(), ENT_QUOTES, 'UTF-8');
                echo '</pre>';
                return;
            }

            echo '<h1>Erro interno</h1><p>Nao foi possivel concluir a solicitacao. Tente novamente mais tarde.</p>';
        });
    }

    private static function configureSession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        $config = Config::get('app.session', []);

        session_name((string) ($config['name'] ?? 'assembleiadeus_session'));

        session_set_cookie_params([
            'lifetime' => (int) ($config['lifetime'] ?? 7200),
            'path'     => '/',
            'domain'   => '',
            'secure'   => (bool) ($config['cookie_secure'] ?? false),
            'httponly' => (bool) ($config['cookie_httponly'] ?? true),
            'samesite' => (string) ($config['cookie_samesite'] ?? 'Lax'),
        ]);

        session_start();
    }
}
