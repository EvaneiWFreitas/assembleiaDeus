<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// ----------------------------------------------------------------------
// Rotas públicas (autenticação)
// ----------------------------------------------------------------------
$routes->get('/', static function () { return redirect()->to('/dashboard'); });

$routes->group('', ['filter' => 'guest'], static function ($routes) {
    $routes->get('login', 'Auth::login');
    $routes->post('login', 'Auth::autenticar');
});

$routes->get('logout', 'Auth::logout');
$routes->get('senha', 'Auth::senha');
$routes->post('senha', 'Auth::alterarSenha');

// ----------------------------------------------------------------------
// Rotas protegidas
// ----------------------------------------------------------------------
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('dashboard', 'Dashboard::index');

    $routes->get('usuarios', 'Usuarios::index');
    $routes->get('usuarios/novo', 'Usuarios::novo');
    $routes->post('usuarios/salvar', 'Usuarios::salvar');
    $routes->get('usuarios/editar/(:num)', 'Usuarios::editar/$1');
    $routes->post('usuarios/atualizar/(:num)', 'Usuarios::atualizar/$1');
    $routes->post('usuarios/excluir/(:num)', 'Usuarios::excluir/$1');
    $routes->post('usuarios/bloquear/(:num)', 'Usuarios::alternarBloqueio/$1');

    $routes->get('roles', 'Roles::index');
    $routes->get('roles/novo', 'Roles::novo');
    $routes->post('roles/salvar', 'Roles::salvar');
    $routes->get('roles/editar/(:num)', 'Roles::editar/$1');
    $routes->post('roles/atualizar/(:num)', 'Roles::atualizar/$1');
    $routes->post('roles/excluir/(:num)', 'Roles::excluir/$1');

    // ------------------------------------------------------------------
    // Etapa 2: igreja, congregações, membros, visitantes, obreiros
    // ------------------------------------------------------------------
    $routes->get('igreja', 'Igreja::editar');
    $routes->post('igreja', 'Igreja::atualizar');

    $routes->get('congregacoes', 'Congregacoes::index');
    $routes->get('congregacoes/novo', 'Congregacoes::novo');
    $routes->post('congregacoes/salvar', 'Congregacoes::salvar');
    $routes->get('congregacoes/editar/(:num)', 'Congregacoes::editar/$1');
    $routes->post('congregacoes/atualizar/(:num)', 'Congregacoes::atualizar/$1');
    $routes->post('congregacoes/excluir/(:num)', 'Congregacoes::excluir/$1');

    $routes->get('membros', 'Membros::index');
    $routes->get('membros/novo', 'Membros::novo');
    $routes->post('membros/salvar', 'Membros::salvar');
    $routes->get('membros/editar/(:num)', 'Membros::editar/$1');
    $routes->post('membros/atualizar/(:num)', 'Membros::atualizar/$1');
    $routes->post('membros/excluir/(:num)', 'Membros::excluir/$1');
    $routes->get('membros/ficha/(:num)', 'Membros::ficha/$1');

    $routes->get('visitantes', 'Visitantes::index');
    $routes->get('visitantes/novo', 'Visitantes::novo');
    $routes->post('visitantes/salvar', 'Visitantes::salvar');
    $routes->get('visitantes/editar/(:num)', 'Visitantes::editar/$1');
    $routes->post('visitantes/atualizar/(:num)', 'Visitantes::atualizar/$1');
    $routes->post('visitantes/excluir/(:num)', 'Visitantes::excluir/$1');

    $routes->get('obreiros', 'Obreiros::index');
    $routes->get('obreiros/novo', 'Obreiros::novo');
    $routes->post('obreiros/salvar', 'Obreiros::salvar');
    $routes->get('obreiros/editar/(:num)', 'Obreiros::editar/$1');
    $routes->post('obreiros/atualizar/(:num)', 'Obreiros::atualizar/$1');
    $routes->post('obreiros/excluir/(:num)', 'Obreiros::excluir/$1');
});
