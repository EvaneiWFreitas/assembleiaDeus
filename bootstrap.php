<?php

declare(strict_types=1);

/**
 * bootstrap.php - unico ponto de carregamento da aplicacao.
 * Chamado por index.php (site) e admin/index.php (painel).
 */

require_once __DIR__ . '/app/Core/Application.php';

App\Core\Application::boot(__DIR__);
