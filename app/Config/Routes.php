<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// ----------------------------------------------------------------------
// Rotas públicas (autenticação)
// ----------------------------------------------------------------------
$routes->get('/', 'Home::index');
$routes->get('site/ministerios', 'Site::ministerios');
$routes->get('site/celulas', 'Site::celulas');
$routes->get('site/discipulados', 'Site::discipulados');
$routes->get('site/evento/(:num)', 'Site::evento/$1');
$routes->post('site/evento/(:num)/confirmar', 'Site::confirmarPresenca/$1');

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
    $routes->get('igreja/editar', 'Igreja::editar');
    $routes->post('igreja/atualizar', 'Igreja::atualizar');

    $routes->get('banners', 'Banners::index');
    $routes->get('banners/novo', 'Banners::novo');
    $routes->post('banners/salvar', 'Banners::salvar');
    $routes->get('banners/editar/(:num)', 'Banners::editar/$1');
    $routes->post('banners/atualizar/(:num)', 'Banners::atualizar/$1');
    $routes->post('banners/excluir/(:num)', 'Banners::excluir/$1');
    $routes->post('banners/alternarAtivo/(:num)', 'Banners::alternarAtivo/$1');

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

    $routes->get('carteirinhas', 'Carteirinhas::index');
    $routes->get('carteirinhas/imprimir', 'Carteirinhas::imprimir');
    $routes->get('carteirinhas/imprimirtudo', 'Carteirinhas::imprimirTodas');

    $routes->get('cargos', 'Cargos::index');
    $routes->get('cargos/novo', 'Cargos::novo');
    $routes->post('cargos/salvar', 'Cargos::salvar');
    $routes->get('cargos/editar/(:num)', 'Cargos::editar/$1');
    $routes->post('cargos/atualizar/(:num)', 'Cargos::atualizar/$1');
    $routes->post('cargos/excluir/(:num)', 'Cargos::excluir/$1');

    // ------------------------------------------------------------------
    // Etapa 3: departamentos, ministérios, células, discipulado, cursos
    // ------------------------------------------------------------------
    $routes->get('departamentos', 'Departamentos::index');
    $routes->get('departamentos/novo', 'Departamentos::novo');
    $routes->post('departamentos/salvar', 'Departamentos::salvar');
    $routes->get('departamentos/editar/(:num)', 'Departamentos::editar/$1');
    $routes->post('departamentos/atualizar/(:num)', 'Departamentos::atualizar/$1');
    $routes->post('departamentos/excluir/(:num)', 'Departamentos::excluir/$1');

    $routes->get('ministerios', 'Ministerios::index');
    $routes->get('ministerios/novo', 'Ministerios::novo');
    $routes->post('ministerios/salvar', 'Ministerios::salvar');
    $routes->get('ministerios/editar/(:num)', 'Ministerios::editar/$1');
    $routes->post('ministerios/atualizar/(:num)', 'Ministerios::atualizar/$1');
    $routes->post('ministerios/excluir/(:num)', 'Ministerios::excluir/$1');

    $routes->get('celulas', 'Celulas::index');
    $routes->get('celulas/novo', 'Celulas::novo');
    $routes->post('celulas/salvar', 'Celulas::salvar');
    $routes->get('celulas/editar/(:num)', 'Celulas::editar/$1');
    $routes->post('celulas/atualizar/(:num)', 'Celulas::atualizar/$1');
    $routes->post('celulas/excluir/(:num)', 'Celulas::excluir/$1');
    $routes->get('celulas/membros/(:num)', 'Celulas::membros/$1');
    $routes->post('celulas/vincularMembro/(:num)', 'Celulas::vincularMembro/$1');
    $routes->post('celulas/removerMembro/(:num)/(:num)', 'Celulas::removerMembro/$1/$2');

    $routes->get('discipulados', 'Discipulados::index');
    $routes->get('discipulados/novo', 'Discipulados::novo');
    $routes->post('discipulados/salvar', 'Discipulados::salvar');
    $routes->get('discipulados/editar/(:num)', 'Discipulados::editar/$1');
    $routes->post('discipulados/atualizar/(:num)', 'Discipulados::atualizar/$1');
    $routes->post('discipulados/excluir/(:num)', 'Discipulados::excluir/$1');
    $routes->post('discipulados/encontro/(:num)', 'Discipulados::registrarEncontro/$1');

    $routes->get('cursos', 'Cursos::index');
    $routes->get('cursos/novo', 'Cursos::novo');
    $routes->post('cursos/salvar', 'Cursos::salvar');
    $routes->get('cursos/editar/(:num)', 'Cursos::editar/$1');
    $routes->post('cursos/atualizar/(:num)', 'Cursos::atualizar/$1');
    $routes->post('cursos/excluir/(:num)', 'Cursos::excluir/$1');
    $routes->post('cursos/adicionarAula/(:num)', 'Cursos::adicionarAula/$1');
    $routes->post('cursos/matricular/(:num)', 'Cursos::matricular/$1');
    $routes->post('cursos/atualizarAluno/(:num)/(:num)', 'Cursos::atualizarAluno/$1/$2');
    $routes->post('cursos/removerAluno/(:num)/(:num)', 'Cursos::removerAluno/$1/$2');

    // ------------------------------------------------------------------
    // Etapa 5: financeiro
    // ------------------------------------------------------------------
    $routes->get('financeiro', 'Financeiro::index');

    $routes->get('dizimos', 'Dizimos::index');
    $routes->get('dizimos/novo', 'Dizimos::novo');
    $routes->post('dizimos/salvar', 'Dizimos::salvar');
    $routes->get('dizimos/editar/(:num)', 'Dizimos::editar/$1');
    $routes->post('dizimos/atualizar/(:num)', 'Dizimos::atualizar/$1');
    $routes->post('dizimos/excluir/(:num)', 'Dizimos::excluir/$1');

    $routes->get('ofertas', 'Ofertas::index');
    $routes->get('ofertas/novo', 'Ofertas::novo');
    $routes->post('ofertas/salvar', 'Ofertas::salvar');
    $routes->get('ofertas/editar/(:num)', 'Ofertas::editar/$1');
    $routes->post('ofertas/atualizar/(:num)', 'Ofertas::atualizar/$1');
    $routes->post('ofertas/excluir/(:num)', 'Ofertas::excluir/$1');

    $routes->get('despesas', 'Despesas::index');
    $routes->get('despesas/novo', 'Despesas::novo');
    $routes->post('despesas/salvar', 'Despesas::salvar');
    $routes->get('despesas/editar/(:num)', 'Despesas::editar/$1');
    $routes->post('despesas/atualizar/(:num)', 'Despesas::atualizar/$1');
    $routes->post('despesas/excluir/(:num)', 'Despesas::excluir/$1');

    $routes->get('receitas', 'Receitas::index');
    $routes->get('receitas/novo', 'Receitas::novo');
    $routes->post('receitas/salvar', 'Receitas::salvar');
    $routes->get('receitas/editar/(:num)', 'Receitas::editar/$1');
    $routes->post('receitas/atualizar/(:num)', 'Receitas::atualizar/$1');
    $routes->post('receitas/excluir/(:num)', 'Receitas::excluir/$1');

    $routes->get('categorias', 'Categorias::index');
    $routes->get('categorias/novo', 'Categorias::novo');
    $routes->post('categorias/salvar', 'Categorias::salvar');
    $routes->get('categorias/editar/(:num)', 'Categorias::editar/$1');
    $routes->post('categorias/atualizar/(:num)', 'Categorias::atualizar/$1');
    $routes->post('categorias/excluir/(:num)', 'Categorias::excluir/$1');

    // ------------------------------------------------------------------
    // Etapa 4: agenda
    // ------------------------------------------------------------------
    $routes->get('agenda', 'Agenda::index');
    $routes->get('agenda/novo', 'Agenda::novo');
    $routes->post('agenda/salvar', 'Agenda::salvar');
    $routes->get('agenda/editar/(:num)', 'Agenda::editar/$1');
    $routes->post('agenda/atualizar/(:num)', 'Agenda::atualizar/$1');
    $routes->post('agenda/excluir/(:num)', 'Agenda::excluir/$1');
    $routes->get('agenda/calendario', 'Agenda::calendario');
    $routes->get('agenda/presencas/(:num)', 'Agenda::presencas/$1');
    $routes->post('agenda/presencas/(:num)/adicionar', 'Agenda::adicionarPresenca/$1');
    $routes->post('agenda/presencas/remover/(:num)', 'Agenda::removerPresenca/$1');
});
