<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Configuracao;
use App\Models\ConteudoPublico;

final class EventoPublicoController extends Controller
{
    public function index(): void
    {
        $config = Configuracao::all();

        $this->render('site.eventos', [
            'title'   => $config['eventos_titulo'] ?? 'Eventos e programação',
            'eventos' => ConteudoPublico::eventos(60),
            'config'  => $config,
        ]);
    }

    public function show(string $id): void
    {
        $evento = ConteudoPublico::evento((int) $id);

        if ($evento === null) {
            (new SiteController())->notFound();
            return;
        }

        $this->render('site.evento', [
            'title'  => $evento['titulo'],
            'evento' => $evento,
            'config' => Configuracao::all(),
        ]);
    }
}
