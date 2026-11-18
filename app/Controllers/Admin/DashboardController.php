<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Acl;
use App\Core\AdminController;
use App\Core\Auth;
use App\Models\Dashboard;

/**
 * Painel inicial do usuario autenticado.
 */
final class DashboardController extends AdminController
{
    public function index(): void
    {
        $this->guard('dashboard.ver');

        $usuario = Auth::user();

        $this->renderAdmin('admin.dashboard.index', [
            'title' => 'Dashboard',
            'resumo' => Dashboard::resumo(
                Auth::congregacaoId(),
                Acl::can('site_contatos.ver')
            ),
            'eventos' => Dashboard::proximosEventos(Auth::congregacaoId()),
            'acessos' => Dashboard::ultimosAcessos((int) ($usuario['id'] ?? 0)),
        ]);
    }
}
