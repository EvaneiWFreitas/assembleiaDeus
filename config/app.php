<?php

declare(strict_types=1);

/**
 * Configurações gerais da aplicação.
 * Os valores vêm de env(), que lê o arquivo .env carregado no bootstrap.
 */

return [
    'name'      => env('APP_NAME', 'Assembleia de Deus'),
    'env'       => env('APP_ENV', 'production'),
    'debug'     => filter_var(env('APP_DEBUG', 'false'), FILTER_VALIDATE_BOOLEAN),
    'url'       => rtrim(env('APP_URL', ''), '/'),
    'timezone'  => env('APP_TIMEZONE', 'America/Sao_Paulo'),
    'locale'    => env('APP_LOCALE', 'pt_BR'),
    'key'       => env('APP_KEY', ''),

    'paths' => [
        'root'      => ROOT_PATH,
        'app'       => ROOT_PATH . '/app',
        'config'    => ROOT_PATH . '/config',
        'modules'   => ROOT_PATH . '/modules',
        'public'    => ROOT_PATH . '/public',
        'storage'   => ROOT_PATH . '/storage',
        'uploads'   => ROOT_PATH . '/public/uploads',
        'logs'      => ROOT_PATH . '/storage/logs',
        'documents' => ROOT_PATH . '/storage/documentos',
        'backups'   => ROOT_PATH . '/storage/backups',
    ],

    'database' => [
        'connection' => env('DB_CONNECTION', 'mysql'),
    ],

    'pagination' => [
        'per_page' => 20,
        'links'    => 5,
    ],

    'session' => [
        'name'           => env('SESSION_NAME', 'assembleiadeus_session'),
        'lifetime'       => 7200,
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
        'cookie_secure'   => env('APP_ENV', 'production') === 'production',
    ],

    'uploads' => [
        'max_size'      => 2 * 1024 * 1024,
        'allowed_image' => ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'],
        'allowed_doc'   => ['application/pdf' => 'pdf'],
    ],

    'security' => [
        'login_max_attempts' => 5,
        'login_lock_minutes' => 15,
        'token_lifetime'     => 3600,
    ],
];
