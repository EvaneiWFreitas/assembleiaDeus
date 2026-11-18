<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\AdminController;
use App\Models\Acesso;

/**
 * Consulta de logs de acesso e alteracoes registradas.
 */
final class AuditController extends AdminController
{
    public function index(): void
    {
        $this->guard('auditoria.ver');

        $this->renderAdmin('admin.auditoria.index', [
            'title' => 'Auditoria',
            'acessos' => Acesso::ultimosAcessos(),
            'alteracoes' => Acesso::ultimasAlteracoes(),
        ]);
    }
}
