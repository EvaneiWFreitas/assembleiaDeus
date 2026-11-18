<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Flash;
use App\Models\Configuracao;
use App\Models\Contato;

final class ContatoController extends Controller
{
    public function index(): void
    {
        $this->render('site.contato', [
            'title'  => 'Contato',
            'config' => Configuracao::all(),
        ]);
    }

    public function enviar(): void
    {
        if (!$this->request->isPost()) {
            $this->redirect('contato');
        }

        if (!Csrf::validate($this->request->post('_csrf'))) {
            http_response_code(419);
            $this->redirect('contato');
        }

        $nome     = trim((string) $this->request->post('nome', ''));
        $email    = trim((string) $this->request->post('email', ''));
        $telefone = trim((string) $this->request->post('telefone', ''));
        $assunto  = trim((string) $this->request->post('assunto', ''));
        $mensagem = trim((string) $this->request->post('mensagem', ''));
        $lgpd     = $this->request->post('lgpd');

        $_SESSION['old'] = [
            'nome'     => $nome,
            'email'    => $email,
            'telefone' => $telefone,
            'assunto'  => $assunto,
            'mensagem' => $mensagem,
        ];

        $erros = [];

        if (mb_strlen($nome) < 3 || mb_strlen($nome) > 150) {
            $erros[] = 'Informe seu nome completo (entre 3 e 150 caracteres).';
        }

        if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $erros[] = 'Informe um e-mail válido.';
        }

        if (mb_strlen($telefone) > 20) {
            $erros[] = 'Telefone muito longo.';
        }

        if (mb_strlen($assunto) > 180) {
            $erros[] = 'Assunto muito longo.';
        }

        if (mb_strlen($mensagem) < 10 || mb_strlen($mensagem) > 5000) {
            $erros[] = 'A mensagem deve ter entre 10 e 5000 caracteres.';
        }

        if ($lgpd === null) {
            $erros[] = 'É necessário concordar com o tratamento dos dados conforme a LGPD.';
        }

        if ($erros !== []) {
            Flash::error(implode(' ', $erros));
            $this->redirect('contato');
        }

        $contatoId = Contato::criar([
            'nome'     => $nome,
            'email'    => $email,
            'telefone' => $telefone,
            'assunto'  => $assunto !== '' ? $assunto : 'Contato pelo site',
            'mensagem' => $mensagem,
            'ip'       => $this->request->ip(),
        ]);

        Contato::registrarConsentimento((int) $contatoId, $this->request->ip());

        unset($_SESSION['old']);
        Flash::success('Mensagem enviada com sucesso. Obrigado pelo contato!');
        $this->redirect('contato');
    }
}
