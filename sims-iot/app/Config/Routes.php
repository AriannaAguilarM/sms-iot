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
    'filter'    => 'cors',           // Aplica CORS a todos los endpoints
], function ($routes) {

    // ── LECTURAS (ESP32 → API) ─────────────────────────────────────────
    $routes->post('lecturas',        'LecturasController::store');
    $routes->get('lecturas/ultima',  'LecturasController::ultima');
    $routes->get('lecturas',         'LecturasController::index');

    // ── ESTADÍSTICAS (Dashboard) ───────────────────────────────────────
    $routes->get('promedios',              'EstadisticasController::promedios');
    $routes->get('estadisticas/semana',    'EstadisticasController::semana');
    $routes->get('estadisticas/hoy',       'EstadisticasController::hoy');

    // ── ALERTAS (Dashboard + Sistema) ─────────────────────────────────
    $routes->get('alertas/resumen',    'AlertasController::resumen');
    $routes->get('alertas',            'AlertasController::index');
    $routes->delete('alertas/limpiar', 'AlertasController::limpiar');

    // ── CONFIGURACIÓN (Usuario → ESP32) ───────────────────────────────
    $routes->get('config',              'ConfigController::index');
    $routes->post('config/umbrales',    'ConfigController::actualizarUmbrales');

    // ── Usuarios (ESP32 → API) ─────────────────────────────────────────
    $routes->get('listar', 'Api\UsuariosController::listar');
    $routes->post('crear', 'Api\UsuariosController::crear');
    $routes->put('editar/(:num)', 'Api\UsuariosController::editar/$1');
    $routes->delete('eliminar/(:num)', 'Api\UsuariosController::eliminar/$1');
});