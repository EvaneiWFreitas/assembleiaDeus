<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Configuracao;
use App\Models\Pagina;

final class PaginaController extends Controller
{
    public function show(string $slug): void
    {
        $pagina = Pagina::findBySlug($slug);

        if ($pagina === null) {
            (new SiteController())->notFound();
            return;
        }

        $this->render('site.pagina', [
            'title'  => $pagina['titulo'],
            'pagina' => $pagina,
            'config' => Configuracao::all(),
        ]);
    }
}
