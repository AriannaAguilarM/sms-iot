<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

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
});