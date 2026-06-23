<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

/**
 * ============================================================
 *  RUTAS API REST — Sistema Inteligente de Monitoreo del Sueño
 * ============================================================
 * 
 * INSTRUCCIONES:
 *   Copia este bloque al final de tu archivo:
 *   app/Config/Routes.php
 * 
 *   Asegúrate de que $routes ya esté definido antes de este bloque.
 * ============================================================
 */

// ─── Registrar el filtro CORS en app/Config/Filters.php ───────────────────
// Agrega esto en el array 'aliases' de tu Filters.php:
//
//   'cors' => \App\Filters\CorsFilter::class,
//
// Y en el array 'globals' → 'before':
//
//   'cors',
//
// ──────────────────────────────────────────────────────────────────────────

$routes->group('api', [
    'namespace' => 'App\Controllers\Api',
    'filter'    => 'cors',           // Aplica CORS a todos los endpoints
], function ($routes) {

    // ── LECTURAS (ESP32 → API) ─────────────────────────────────────────
    //   POST /api/lecturas        Registrar nueva lectura del sensor
    //   GET  /api/lecturas        Listar lecturas (soporta ?limit=50 ?fecha_inicio=... ?fecha_fin=...)
    //   GET  /api/lecturas/ultima Lectura más reciente (tiempo real)

    $routes->post('lecturas',        'LecturasController::store');
    $routes->get('lecturas/ultima',  'LecturasController::ultima');
    $routes->get('lecturas',         'LecturasController::index');

    // ── ESTADÍSTICAS (Dashboard) ───────────────────────────────────────
    //   GET /api/promedios              Promedios globales de todas las lecturas
    //   GET /api/estadisticas/semana    Datos agrupados por día (últimos 7 días)
    //   GET /api/estadisticas/hoy       Promedios del día actual

    $routes->get('promedios',              'EstadisticasController::promedios');
    $routes->get('estadisticas/semana',    'EstadisticasController::semana');
    $routes->get('estadisticas/hoy',       'EstadisticasController::hoy');

    // ── ALERTAS (Dashboard + Sistema) ─────────────────────────────────
    //   GET    /api/alertas          Listar alertas recientes
    //   GET    /api/alertas/resumen  Conteo de alertas por nivel (alto/medio/bajo)
    //   DELETE /api/alertas/limpiar  Eliminar alertas antiguas (>30 días)

    $routes->get('alertas/resumen',    'AlertasController::resumen');
    $routes->get('alertas',            'AlertasController::index');
    $routes->delete('alertas/limpiar', 'AlertasController::limpiar');

    // ── CONFIGURACIÓN (Usuario → ESP32) ───────────────────────────────
    //   GET  /api/config           ESP32 lee umbrales al iniciar
    //   POST /api/config/umbrales  Usuario actualiza umbrales desde el dashboard

    $routes->get('config',              'ConfigController::index');
    $routes->post('config/umbrales',    'ConfigController::actualizarUmbrales');
});