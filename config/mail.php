<?php

declare(strict_types=1);

/**
 * Configuracao de e-mail (recuperacao de senha e formulario de contato).
 * Credenciais sempre via .env.
 */

return [
    'driver'     => env('MAIL_DRIVER', 'smtp'),
    'host'       => env('MAIL_HOST', ''),
    'port'       => (int) env('MAIL_PORT', '587'),
    'encryption' => env('MAIL_ENCRYPTION', 'tls'),
    'username'   => env('MAIL_USER', ''),
    'password'   => env('MAIL_PASS', ''),
    'from' => [
        'address' => env('MAIL_FROM', ''),
        'name'    => env('MAIL_FROM_NAME', env('APP_NAME', 'Assembleia de Deus')),
    ],
];
