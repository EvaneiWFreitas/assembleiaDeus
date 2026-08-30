<?php

declare(strict_types=1);

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Protege rotas exigindo sessão autenticada.
 */
class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session()->get('logado')) {
            if ($request->isAJAX()) {
                return service('response')->setStatusCode(401)->setJSON(['erro' => 'Sessão expirada. Faça login novamente.']);
            }

            return redirect()->to('/login')->with('erro', 'Faça login para acessar o sistema.');
        }

        // Usuário bloqueado no meio da sessão
        if (session()->get('usuario')['bloqueado'] ?? false) {
            session()->destroy();

            return redirect()->to('/login')->with('erro', 'Sua conta foi bloqueada. Contate o administrador.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null): void
    {
    }
}
