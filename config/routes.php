<?php

declare(strict_types=1);

/**
 * Rotas públicas. O painel terá seu próprio arquivo de rotas na ETAPA 5.
 */

return [
    ''                    => ['uses' => 'SiteController@home'],
    'historia'            => ['uses' => 'PaginaController@show', 'params' => ['slug' => 'historia']],
    'missao-visao-valores'=> ['uses' => 'PaginaController@show', 'params' => ['slug' => 'missao-visao-valores']],
    'palavra-pastoral'    => ['uses' => 'PaginaController@show', 'params' => ['slug' => 'palavra-pastoral']],
    'igrejas'             => ['uses' => 'SiteController@igrejas'],
    'congregacoes'        => ['uses' => 'SiteController@congregacoes'],
    'pontos-pregacao'     => ['uses' => 'SiteController@pontosPregacao'],
    'eventos'             => ['uses' => 'EventoPublicoController@index'],
    'eventos/{id}'        => ['uses' => 'EventoPublicoController@show'],
    'galeria'             => ['uses' => 'SiteController@galeria'],
    'noticias'            => ['uses' => 'SiteController@noticias'],
    'noticias/{slug}'     => ['uses' => 'SiteController@noticia'],
    'contato'             => ['uses' => 'ContatoController@index'],
    'contato/enviar'      => ['uses' => 'ContatoController@enviar', 'method' => 'POST'],
    'validar/{codigo}'    => ['uses' => 'ValidacaoController@verificar'],
];
