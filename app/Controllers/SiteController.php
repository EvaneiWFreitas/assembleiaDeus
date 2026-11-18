<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Banner;
use App\Models\Configuracao;
use App\Models\ConteudoPublico;
use App\Models\Pagina;

final class SiteController extends Controller
{
    public function home(): void
    {
        $paginas = [];
        foreach (Pagina::todas() as $pagina) {
            $paginas[$pagina['slug']] = $pagina;
        }

        $this->render('site.home', [
            'title'       => Configuracao::get('ministerio_nome', 'Assembleia de Deus'),
            'config'      => Configuracao::all(),
            'slides'      => Banner::exibir(),
            'paginas'     => $paginas,
            'estatisticas' => ConteudoPublico::estatisticas(),
            'igrejas'     => ConteudoPublico::igrejas(),
            'congregacoes' => ConteudoPublico::congregacoes(),
            'pontos'      => ConteudoPublico::pontos(),
            'horarios'    => ConteudoPublico::horarios(),
            'eventos'     => ConteudoPublico::eventos(6),
            'noticias'    => ConteudoPublico::noticias(4),
            'galeria'     => ConteudoPublico::galeria(12),
        ]);
    }

    public function igrejas(): void
    {
        $this->render('site.estrutura', [
            'title'  => 'Igreja Matriz',
            'tipo'   => 'igrejas',
            'itens'  => ConteudoPublico::igrejas(),
            'config' => Configuracao::all(),
        ]);
    }

    public function congregacoes(): void
    {
        $this->render('site.estrutura', [
            'title'  => 'Congregações',
            'tipo'   => 'congregacoes',
            'itens'  => ConteudoPublico::congregacoes(),
            'config' => Configuracao::all(),
        ]);
    }

    public function pontosPregacao(): void
    {
        $this->render('site.estrutura', [
            'title'  => 'Pontos de Pregação',
            'tipo'   => 'pontos',
            'itens'  => ConteudoPublico::pontos(),
            'config' => Configuracao::all(),
        ]);
    }

    public function galeria(): void
    {
        $this->render('site.galeria', [
            'title'  => 'Galeria de Fotografias',
            'fotos'  => ConteudoPublico::galeria(60),
            'config' => Configuracao::all(),
        ]);
    }

    public function noticias(): void
    {
        $this->render('site.noticias', [
            'title'   => 'Notícias',
            'noticias' => ConteudoPublico::noticias(30),
            'config'  => Configuracao::all(),
        ]);
    }

    public function noticia(string $slug): void
    {
        $noticia = ConteudoPublico::noticia($slug);

        if ($noticia === null) {
            $this->notFound();
            return;
        }

        $this->render('site.noticia', [
            'title'   => $noticia['titulo'],
            'noticia' => $noticia,
            'config'  => Configuracao::all(),
        ]);
    }

    public function notFound(): void
    {
        $this->render('site.404', [
            'title'  => 'Página não encontrada',
            'config' => Configuracao::all(),
        ], 'site', 404);
    }
}
