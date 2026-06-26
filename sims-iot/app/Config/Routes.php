<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

$routes = Services::routes();

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');
$routes->get('dashboard', 'Home::dashboard');
$routes->get('historial', 'Home::historial');
$routes->get('estadisticas', 'Home::estadisticas');
$routes->get('alertas', 'Home::alertas');
$routes->get('configuracion', 'Home::configuracion');
$routes->get('usuarios', 'Home::usuarios');
$routes->get('acerca', 'Home::acerca');

$routes->group('api', [
    'namespace' => 'App\Controllers\Api',
    'filter'    => 'cors',
], function ($routes) {

    // ── LECTURAS ─────────────────────────────────────────
    $routes->post('lecturas',        'LecturasController::store');
    $routes->get('lecturas/ultima',  'LecturasController::ultima');
    $routes->get('lecturas',         'LecturasController::index');

    // ── ESTADÍSTICAS ───────────────────────────────────────
    $routes->get('promedios',              'EstadisticasController::promedios');
    $routes->get('estadisticas/semana',    'EstadisticasController::semana');
    $routes->get('estadisticas/hoy',       'EstadisticasController::hoy');

    // ── ALERTAS ──────────────────────────────────────────
    $routes->get('alertas/resumen',    'AlertasController::resumen');
    $routes->get('alertas',            'AlertasController::index');
    $routes->delete('alertas/limpiar', 'AlertasController::limpiar');

    // ── CONFIGURACIÓN (ConfigController) ────────────────────
    $routes->get('config',              'ConfigController::index');
    $routes->post('config/umbrales',    'ConfigController::umbrales');
    $routes->post('config/inicializar', 'ConfigController::inicializar');

    // ── USUARIOS ─────────────────────────────────────────
    $routes->get('usuarios/listar',    'UsuariosController::listar');
    $routes->post('usuarios/crear',    'UsuariosController::crear');
    $routes->put('usuarios/editar/(:num)', 'UsuariosController::editar/$1');
    $routes->delete('usuarios/eliminar/(:num)', 'UsuariosController::eliminar/$1');
});