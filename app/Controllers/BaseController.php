<?php

namespace App\Controllers;

use App\Libraries\Auditoria;
use App\Models\IgrejaModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */

    // protected $session;

    /**
     * Dados comuns passados para todas as views do layout.
     */
    protected array $dados = [];

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Load here all helpers you want to be available in your controllers that extend BaseController.
        // Caution: Do not put the this below the parent::initController() call below.
        // $this->helpers = ['form', 'url'];

        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        helper(['form', 'text', 'util']);

        $this->dados['usuario']    = session()->get('usuario');
        $this->dados['permissoes'] = session()->get('permissoes') ?? [];
        $this->dados['igreja']     = (new IgrejaModel())->first() ?? [];
    }

    /**
     * Usuário logado é super admin?
     */
    protected function ehSuperAdmin(): bool
    {
        return session()->get('usuario')['super_admin'] ?? false;
    }

    /**
     * Verifica se o usuário possui uma permissão específica.
     */
    protected function temPermissao(string $modulo, string $acao): bool
    {
        if ($this->ehSuperAdmin()) {
            return true;
        }
        $permissoes = session()->get('permissoes') ?? [];

        return in_array($modulo . ':' . $acao, $permissoes, true)
            || in_array($modulo . ':*', $permissoes, true);
    }

    /**
     * Bloqueia a execução caso o usuário não tenha a permissão.
     */
    protected function exigirPermissao(string $modulo, string $acao): void
    {
        if (! $this->temPermissao($modulo, $acao)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Acesso não autorizado.');
        }
    }
}
