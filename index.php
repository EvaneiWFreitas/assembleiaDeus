<?php

declare(strict_types=1);

/**
 * Front controller do site institucional.
 */

require_once __DIR__ . '/bootstrap.php';

use App\Core\Router;

$router = new Router(require ROOT_PATH . '/config/routes.php');

$router->dispatch(
    (string) ($_GET['route'] ?? ''),
    (string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')
);
