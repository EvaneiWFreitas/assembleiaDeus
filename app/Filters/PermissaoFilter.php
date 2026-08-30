<?php

declare(strict_types=1);

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Valida permissões do usuário logado contra o módulo/ação da rota.
 * Regras no Routes.php: ->asPermissions('modulo:acao') alimenta session('permissoes_requeridas').
 * Na prática, este filtro deriva a permissão exigida do próprio caminho da rota:
 *   /usuarios/editar/3  =>  modulo "usuarios", acao "editar"
 * Super admin passa sempre.
 */
class PermissaoFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $usuario = session()->get('usuario');

        if ($usuario === null || ($usuario['super_admin'] ?? false)) {
            return; // super admin: acesso total
        }

        $path = trim((string) $request->getUri()->getPath(), '/');
        $path = preg_replace('#^(admin/|api/)#', '', $path);
        $partes = explode('/', $path);
        $modulo = $partes[0] ?? 'dashboard';
        $acao    = $partes[1] ?? 'visualizar';

        // Mapear sub-rotinas comuns para a ação correta
        $map = ['novo' => 'cadastrar', 'salvar' => 'cadastrar', 'atualizar' => 'editar', 'remover' => 'excluir'];
        $acao = $map[$acao] ?? $acao;

        $permissoes = session()->get('permissoes') ?? [];
        $necessaria = $modulo . ':' . $acao;

        if (! in_array($necessaria, $permissoes, true) && ! in_array($modulo . ':*', $permissoes, true)) {
            if ($request->isAJAX()) {
                return service('response')->setStatusCode(403)->setJSON(['erro' => 'Você não tem permissão para esta ação.']);
            }

            return redirect()->back()->with('erro', 'Você não tem permissão para executar esta ação.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null): void
    {
    }
}
