<?php

declare(strict_types=1);

/**
 * Front controller do painel administrativo.
 */

require_once __DIR__ . '/../bootstrap.php';

use App\Core\CsrfMiddleware;
use App\Core\Request;
use App\Core\Router;

CsrfMiddleware::handle(Request::capture());

$router = new Router(
    require ROOT_PATH . '/config/admin_routes.php',
    'App\\Controllers\\Admin\\',
    'AuthController'
);

$router->dispatch(
    (string) ($_GET['route'] ?? ''),
    (string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')
);
