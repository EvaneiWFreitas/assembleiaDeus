<?php

declare(strict_types=1);

/**
 * Metadados de permissoes usados pelo AclMiddleware e pela renderizacao do menu.
 * As permissoes efetivas estao no banco (tabelas permissoes / perfil_permissao).
 * Este arquivo apenas descreve os modulos e regras transversais.
 */

return [
    'modules' => [
        'dashboard'      => ['label' => 'Dashboard',      'icon' => 'fas fa-gauge-high',   'prefix' => 'dashboard'],
        'igrejas'        => ['label' => 'Igreja Matriz',  'icon' => 'fas fa-church',       'prefix' => 'igrejas'],
        'congregacoes'   => ['label' => 'Congregações',   'icon' => 'fas fa-place-of-worship', 'prefix' => 'congregacoes'],
        'pontos_pregacao'=> ['label' => 'Pontos de pregação','icon' => 'fas fa-map-marker-alt','prefix' => 'pontos_pregacao'],
        'membros'        => ['label' => 'Membros',        'icon' => 'fas fa-users',        'prefix' => 'membros'],
        'obreiros'       => ['label' => 'Obreiros',       'icon' => 'fas fa-user-tie',     'prefix' => 'obreiros'],
        'arquivo_morto'  => ['label' => 'Arquivo Morto',  'icon' => 'fas fa-box-archive',   'prefix' => 'arquivo_morto'],
        'financeiro'     => ['label' => 'Financeiro',     'icon' => 'fas fa-coins',        'prefix' => 'financeiro'],
        'batismos'       => ['label' => 'Batismos',       'icon' => 'fas fa-water',        'prefix' => 'batismos'],
        'consagracoes'   => ['label' => 'Consagrações',   'icon' => 'fas fa-hands-praying', 'prefix' => 'consagracoes'],
        'diplomas'       => ['label' => 'Diplomas',       'icon' => 'fas fa-scroll',       'prefix' => 'diplomas'],
        'cartas_convite' => ['label' => 'Cartas-Convite', 'icon' => 'fas fa-envelope-open-text', 'prefix' => 'cartas_convite'],
        'carteirinhas'   => ['label' => 'Carteirinhas',   'icon' => 'fas fa-id-card',      'prefix' => 'carteirinhas'],
        'eventos'        => ['label' => 'Eventos',        'icon' => 'fas fa-calendar-days', 'prefix' => 'eventos'],
        'eventos_textos' => ['label' => 'Textos da agenda', 'icon' => 'fas fa-pen-nib', 'prefix' => 'eventos'],
        'relatorios'     => ['label' => 'Relatórios',     'icon' => 'fas fa-chart-bar',    'prefix' => 'relatorios'],
        'site'           => ['label' => 'Site Institucional','icon' => 'fas fa-globe',     'prefix' => 'site'],
        'ministerio_historia' => ['label' => 'Nossa História', 'icon' => 'fas fa-book', 'prefix' => 'ministerio'],
        'ministerio_palavra'  => ['label' => 'Palavra Pastoral', 'icon' => 'fas fa-bible', 'prefix' => 'ministerio'],
        'cultos_horarios'     => ['label' => 'Horários e programação', 'icon' => 'fas fa-clock', 'prefix' => 'horarios'],
        'site_banner'    => ['label' => 'Slides da página inicial', 'icon' => 'fas fa-images',     'prefix' => 'site'],
        'site_textos'    => ['label' => 'Textos do carrossel',      'icon' => 'fas fa-pen-nib',    'prefix' => 'site'],
        'site_historia'  => ['label' => 'História na home',         'icon' => 'fas fa-book-open',  'prefix' => 'site'],
        'site_noticias'  => ['label' => 'Notícias do site',  'icon' => 'fas fa-newspaper',  'prefix' => 'site_noticias'],
        'site_galeria'   => ['label' => 'Galeria do site',   'icon' => 'fas fa-images',     'prefix' => 'site_galeria'],
        'site_contatos'  => ['label' => 'Mensagens recebidas','icon' => 'fas fa-inbox',     'prefix' => 'site_contatos'],
        'configuracoes'        => ['label' => 'Configurações',  'icon' => 'fas fa-gears',        'prefix' => 'configuracoes'],
        'configuracoes_identidade' => ['label' => 'Identidade da igreja', 'icon' => 'fas fa-building-columns', 'prefix' => 'configuracoes'],
        'seguranca'      => ['label' => 'Segurança',      'icon' => 'fas fa-shield-halved','prefix' => 'seguranca'],
        'auditoria'      => ['label' => 'Auditoria',      'icon' => 'fas fa-file-circle-check', 'prefix' => 'auditoria'],
    ],

    'scoped_profiles' => [4, 5, 7],

    'sensitive_actions' => [
        'financeiro.editar',
        'financeiro.excluir',
        'financeiro.cancelar',
        'arquivo_morto.restaurar',
        'arquivo_morto.arquivar',
        'seguranca.usuarios_excluir',
        'seguranca.permissoes_editar',
        'seguranca.restore',
    ],

    'audit_actions' => [
        'criar', 'editar', 'excluir', 'arquivar', 'restaurar',
        'cancelar', 'emitir', 'transferir', 'aprovar',
    ],
];
