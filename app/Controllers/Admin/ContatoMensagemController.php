<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Acl;
use App\Core\AdminController;
use App\Core\Audit;
use App\Core\Auth;
use App\Core\Flash;
use App\Models\Contato;

final class ContatoMensagemController extends AdminController
{
    public function index(): void
    {
        $this->guard('site_contatos.ver');

        $contatos = Contato::todas();
        $naoLidos = 0;
        foreach ($contatos as $contato) {
            if ((int) ($contato['lido'] ?? 0) === 0) {
                $naoLidos++;
            }
        }

        $this->renderAdmin('admin.contatos.index', [
            'title'         => 'Mensagens recebidas',
            'subtitulo'     => 'Mensagens enviadas pelo formulário Fale conosco do site.',
            'contatos'      => $contatos,
            'naoLidos'      => $naoLidos,
            'podeResponder' => Acl::can('site_contatos.responder'),
            'podeExcluir'   => Acl::can('site_contatos.excluir'),
        ]);
    }

    public function ver(string $id): void
    {
        $this->guard('site_contatos.ver');

        $id = (int) $id;
        $contato = Contato::buscar($id);
        if ($contato === null) {
            Flash::error('Mensagem não encontrada.');
            $this->redirectAdmin('contatos');
        }

        if ((int) ($contato['lido'] ?? 0) === 0) {
            Contato::marcarLido($id);
            $contato = Contato::buscar($id);
        }

        $this->renderAdmin('admin.contatos.ver', [
            'title'         => 'Mensagem de ' . (string) ($contato['nome'] ?? ''),
            'subtitulo'     => 'Mensagem recebida pelo formulário Fale conosco.',
            'contato'       => $contato,
            'podeResponder' => Acl::can('site_contatos.responder'),
            'podeExcluir'   => Acl::can('site_contatos.excluir'),
        ]);
    }

    public function marcarLido(string $id): void
    {
        $this->guard('site_contatos.ver');

        $id = (int) $id;
        if (Contato::buscar($id) === null) {
            Flash::error('Mensagem não encontrada.');
            $this->redirectAdmin('contatos');
        }

        Contato::marcarLido($id);
        Flash::success('Mensagem marcada como lida.');
        $this->redirectAdmin('contatos');
    }

    public function marcarRespondido(string $id): void
    {
        $this->guard('site_contatos.responder');

        $id = (int) $id;
        $anterior = Contato::buscar($id);
        if ($anterior === null) {
            Flash::error('Mensagem não encontrada.');
            $this->redirectAdmin('contatos');
        }

        $usuarioId = Auth::id();
        if ($usuarioId === null) {
            Flash::error('Não foi possível identificar o usuário respondente.');
            $this->redirectAdmin('contatos');
        }

        $novo = [
            'respondido'     => 1,
            'respondido_por' => $usuarioId,
        ];
        Contato::marcarRespondido($id, $usuarioId);
        Audit::record('editar', 'site_contatos', $novo, $anterior, 'contatos', (string) $id);

        Flash::success('Mensagem marcada como respondida.');
        $this->redirectAdmin('contatos/' . $id);
    }

    public function excluir(string $id): void
    {
        $this->guard('site_contatos.excluir');

        $id = (int) $id;
        $anterior = Contato::buscar($id);
        if ($anterior === null) {
            Flash::error('Mensagem não encontrada.');
            $this->redirectAdmin('contatos');
        }

        Contato::excluir($id);
        Audit::record('excluir', 'site_contatos', [], $anterior, 'contatos', (string) $id);

        Flash::success('Mensagem excluída.');
        $this->redirectAdmin('contatos');
    }
}
