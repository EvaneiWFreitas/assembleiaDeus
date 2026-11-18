<?php

declare(strict_types=1);

/**
 * Rotas do painel administrativo.
 * O prefixo /admin/ e removido pelo .htaccess antes do dispatch.
 */

return [
    ['pattern' => '',             'uses' => 'DashboardController@index'],
    ['pattern' => 'dashboard',    'uses' => 'DashboardController@index'],
    ['pattern' => 'perfil',       'uses' => 'AuthController@perfilForm'],

    ['pattern' => 'login',        'uses' => 'AuthController@loginForm'],
    ['pattern' => 'login',        'uses' => 'AuthController@login', 'method' => 'POST'],
    ['pattern' => 'logout',       'uses' => 'AuthController@logout', 'method' => 'POST'],
    ['pattern' => 'instalar',     'uses' => 'AuthController@instalarForm'],
    ['pattern' => 'instalar',     'uses' => 'AuthController@instalar', 'method' => 'POST'],
    ['pattern' => 'trocar-senha', 'uses' => 'AuthController@trocarSenhaForm'],
    ['pattern' => 'trocar-senha', 'uses' => 'AuthController@trocarSenha', 'method' => 'POST'],

    ['pattern' => 'usuarios',               'uses' => 'UsuarioController@index'],
    ['pattern' => 'usuarios/novo',          'uses' => 'UsuarioController@criarForm'],
    ['pattern' => 'usuarios',               'uses' => 'UsuarioController@criar', 'method' => 'POST'],
    ['pattern' => 'usuarios/{id}/editar',   'uses' => 'UsuarioController@editarForm'],
    ['pattern' => 'usuarios/{id}/editar',   'uses' => 'UsuarioController@editar', 'method' => 'POST'],
    ['pattern' => 'usuarios/{id}/excluir',  'uses' => 'UsuarioController@excluir', 'method' => 'POST'],
    ['pattern' => 'auditoria',              'uses' => 'AuditController@index'],

    ['pattern' => 'igrejas',                 'uses' => 'IgrejaController@index'],
    ['pattern' => 'igrejas',                 'uses' => 'IgrejaController@criar', 'method' => 'POST'],
    ['pattern' => 'igrejas/novo',            'uses' => 'IgrejaController@criarForm'],
    ['pattern' => 'igrejas/{id}/editar',     'uses' => 'IgrejaController@editarForm'],
    ['pattern' => 'igrejas/{id}/editar',     'uses' => 'IgrejaController@editar', 'method' => 'POST'],
    ['pattern' => 'igrejas/{id}/excluir',    'uses' => 'IgrejaController@excluir', 'method' => 'POST'],

    ['pattern' => 'congregacoes',              'uses' => 'CongregacaoController@index'],
    ['pattern' => 'congregacoes',              'uses' => 'CongregacaoController@criar', 'method' => 'POST'],
    ['pattern' => 'congregacoes/novo',         'uses' => 'CongregacaoController@criarForm'],
    ['pattern' => 'congregacoes/{id}/editar',  'uses' => 'CongregacaoController@editarForm'],
    ['pattern' => 'congregacoes/{id}/editar',  'uses' => 'CongregacaoController@editar', 'method' => 'POST'],
    ['pattern' => 'congregacoes/{id}/excluir', 'uses' => 'CongregacaoController@excluir', 'method' => 'POST'],

    ['pattern' => 'pontos',                 'uses' => 'PontoPregacaoController@index'],
    ['pattern' => 'pontos',                 'uses' => 'PontoPregacaoController@criar', 'method' => 'POST'],
    ['pattern' => 'pontos/novo',            'uses' => 'PontoPregacaoController@criarForm'],
    ['pattern' => 'pontos/{id}/editar',     'uses' => 'PontoPregacaoController@editarForm'],
    ['pattern' => 'pontos/{id}/editar',     'uses' => 'PontoPregacaoController@editar', 'method' => 'POST'],
    ['pattern' => 'pontos/{id}/excluir',    'uses' => 'PontoPregacaoController@excluir', 'method' => 'POST'],

    ['pattern' => 'membros',              'uses' => 'MembroController@index'],
    ['pattern' => 'membros',              'uses' => 'MembroController@criar', 'method' => 'POST'],
    ['pattern' => 'membros/novo',         'uses' => 'MembroController@criarForm'],
    ['pattern' => 'membros/{id}/editar',  'uses' => 'MembroController@editarForm'],
    ['pattern' => 'membros/{id}/editar',  'uses' => 'MembroController@editar', 'method' => 'POST'],
    ['pattern' => 'membros/{id}/excluir', 'uses' => 'MembroController@excluir', 'method' => 'POST'],

    ['pattern' => 'obreiros',              'uses' => 'ObreiroController@index'],
    ['pattern' => 'obreiros',              'uses' => 'ObreiroController@criar', 'method' => 'POST'],
    ['pattern' => 'obreiros/novo',         'uses' => 'ObreiroController@criarForm'],
    ['pattern' => 'obreiros/{id}/editar',  'uses' => 'ObreiroController@editarForm'],
    ['pattern' => 'obreiros/{id}/editar',  'uses' => 'ObreiroController@editar', 'method' => 'POST'],
    ['pattern' => 'obreiros/{id}/excluir', 'uses' => 'ObreiroController@excluir', 'method' => 'POST'],

    ['pattern' => 'arquivo-morto',                  'uses' => 'ArquivoMortoController@index'],
    ['pattern' => 'arquivo-morto/arquivar',         'uses' => 'ArquivoMortoController@arquivarForm'],
    ['pattern' => 'arquivo-morto/arquivar',         'uses' => 'ArquivoMortoController@arquivar', 'method' => 'POST'],
    ['pattern' => 'arquivo-morto/{id}/restaurar',   'uses' => 'ArquivoMortoController@restaurarForm'],
    ['pattern' => 'arquivo-morto/{id}/restaurar',   'uses' => 'ArquivoMortoController@restaurar', 'method' => 'POST'],
    ['pattern' => 'arquivo-morto/{id}',             'uses' => 'ArquivoMortoController@detalhes'],

    ['pattern' => 'ministerio/historia',        'uses' => 'MinisterioController@historiaForm'],
    ['pattern' => 'ministerio/historia',        'uses' => 'MinisterioController@salvarHistoria', 'method' => 'POST'],
    ['pattern' => 'ministerio/historia/imagem/remover', 'uses' => 'MinisterioController@removerImagem', 'method' => 'POST'],

    ['pattern' => 'ministerio/palavra-pastoral', 'uses' => 'MinisterioController@palavraForm'],
    ['pattern' => 'ministerio/palavra-pastoral', 'uses' => 'MinisterioController@salvarPalavra', 'method' => 'POST'],
    ['pattern' => 'ministerio/palavra-pastoral/imagem/remover', 'uses' => 'MinisterioController@removerImagemPalavra', 'method' => 'POST'],
    ['pattern' => 'ministerio/palavra-pastoral/foto/remover', 'uses' => 'MinisterioController@removerFotoPastor', 'method' => 'POST'],

    ['pattern' => 'eventos',                    'uses' => 'EventoController@index'],
    ['pattern' => 'eventos/novo',               'uses' => 'EventoController@novoForm'],
    ['pattern' => 'eventos',                    'uses' => 'EventoController@criar', 'method' => 'POST'],
    ['pattern' => 'eventos/textos',             'uses' => 'EventoController@textosForm'],
    ['pattern' => 'eventos/textos',             'uses' => 'EventoController@salvarTextos', 'method' => 'POST'],
    ['pattern' => 'eventos/{id}/editar',        'uses' => 'EventoController@editarForm'],
    ['pattern' => 'eventos/{id}/editar',        'uses' => 'EventoController@editar', 'method' => 'POST'],
    ['pattern' => 'eventos/{id}/foto/remover',  'uses' => 'EventoController@removerFoto', 'method' => 'POST'],
    ['pattern' => 'eventos/{id}/excluir',       'uses' => 'EventoController@excluir', 'method' => 'POST'],

    ['pattern' => 'horarios',                   'uses' => 'HorarioController@index'],
    ['pattern' => 'horarios/textos',            'uses' => 'HorarioController@salvarTextos', 'method' => 'POST'],
    ['pattern' => 'horarios/novo',              'uses' => 'HorarioController@novoForm'],
    ['pattern' => 'horarios',                   'uses' => 'HorarioController@criar', 'method' => 'POST'],
    ['pattern' => 'horarios/{id}/editar',       'uses' => 'HorarioController@editarForm'],
    ['pattern' => 'horarios/{id}/editar',       'uses' => 'HorarioController@editar', 'method' => 'POST'],
    ['pattern' => 'horarios/{id}/excluir',      'uses' => 'HorarioController@excluir', 'method' => 'POST'],

    ['pattern' => 'configuracoes/identidade',   'uses' => 'IdentidadeController@form'],
    ['pattern' => 'configuracoes/identidade',   'uses' => 'IdentidadeController@salvar', 'method' => 'POST'],
    ['pattern' => 'configuracoes/identidade/logo/remover', 'uses' => 'IdentidadeController@removerLogo', 'method' => 'POST'],

    ['pattern' => 'configuracoes/contato',      'uses' => 'ContatoInfoController@form'],
    ['pattern' => 'configuracoes/contato',      'uses' => 'ContatoInfoController@salvar', 'method' => 'POST'],
    ['pattern' => 'configuracoes/contato/foto/remover', 'uses' => 'ContatoInfoController@removerFoto', 'method' => 'POST'],

    ['pattern' => 'site',                       'uses' => 'SiteController@bannerForm'],
    ['pattern' => 'site',                       'uses' => 'SiteController@salvarBanner', 'method' => 'POST'],
    ['pattern' => 'site/banner/remover-slide',  'uses' => 'SiteController@removerSlide', 'method' => 'POST'],
    ['pattern' => 'site/banner/remover',        'uses' => 'SiteController@removerTodos', 'method' => 'POST'],

    ['pattern' => 'site/carrossel-textos',      'uses' => 'SiteController@textosForm'],
    ['pattern' => 'site/carrossel-textos',      'uses' => 'SiteController@salvarTextos', 'method' => 'POST'],

    ['pattern' => 'site/noticias-textos',       'uses' => 'SiteController@noticiasTextosForm'],
    ['pattern' => 'site/noticias-textos',       'uses' => 'SiteController@salvarNoticiasTextos', 'method' => 'POST'],

    ['pattern' => 'noticias',                         'uses' => 'NoticiaController@index'],
    ['pattern' => 'noticias/novo',                    'uses' => 'NoticiaController@novoForm'],
    ['pattern' => 'noticias',                         'uses' => 'NoticiaController@criar', 'method' => 'POST'],
    ['pattern' => 'noticias/{id}/editar',             'uses' => 'NoticiaController@editarForm'],
    ['pattern' => 'noticias/{id}/editar',             'uses' => 'NoticiaController@editar', 'method' => 'POST'],
    ['pattern' => 'noticias/{id}/imagem/remover',     'uses' => 'NoticiaController@removerImagem', 'method' => 'POST'],
    ['pattern' => 'noticias/{id}/video/remover',      'uses' => 'NoticiaController@removerVideo', 'method' => 'POST'],
    ['pattern' => 'noticias/{id}/excluir',            'uses' => 'NoticiaController@excluir', 'method' => 'POST'],

    ['pattern' => 'galeria',                    'uses' => 'GaleriaController@index'],
    ['pattern' => 'galeria/novo',               'uses' => 'GaleriaController@novoForm'],
    ['pattern' => 'galeria',                    'uses' => 'GaleriaController@criar', 'method' => 'POST'],
    ['pattern' => 'galeria/{id}/editar',        'uses' => 'GaleriaController@editarForm'],
    ['pattern' => 'galeria/{id}/editar',        'uses' => 'GaleriaController@editar', 'method' => 'POST'],
    ['pattern' => 'galeria/{id}/excluir',       'uses' => 'GaleriaController@excluir', 'method' => 'POST'],

    ['pattern' => 'contatos',                   'uses' => 'ContatoMensagemController@index'],
    ['pattern' => 'contatos/{id}',              'uses' => 'ContatoMensagemController@ver'],
    ['pattern' => 'contatos/{id}/lido',         'uses' => 'ContatoMensagemController@marcarLido', 'method' => 'POST'],
    ['pattern' => 'contatos/{id}/respondido',   'uses' => 'ContatoMensagemController@marcarRespondido', 'method' => 'POST'],
    ['pattern' => 'contatos/{id}/excluir',      'uses' => 'ContatoMensagemController@excluir', 'method' => 'POST'],

    ['pattern' => 'site/historia',              'uses' => 'SiteController@historiaForm'],
    ['pattern' => 'site/historia',              'uses' => 'SiteController@salvarHistoria', 'method' => 'POST'],
    ['pattern' => 'site/historia/imagem/remover', 'uses' => 'SiteController@removerImagemHistoria', 'method' => 'POST'],
];
