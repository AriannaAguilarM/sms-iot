<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\LecturaModel;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * EstadisticasController
 * 
 * Endpoints de estadísticas y promedios para el dashboard.
 * 
 * GET /api/promedios            → Promedios globales
 * GET /api/estadisticas/semana  → Agrupado por día (últimos 7 días)
 * GET /api/estadisticas/hoy     → Solo lecturas de hoy
 */
class EstadisticasController extends BaseController
{
    protected LecturaModel $lecturaModel;

    public function __construct()
    {
        $this->lecturaModel = new LecturaModel();
    }

    // ──────────────────────────────────────────────────────────────────────
    // GET /api/promedios
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Retorna los promedios globales de todas las lecturas registradas.
     *
     * Respuesta:
     * {
     *   "temp_promedio":     24.5,
     *   "humedad_promedio":  58,
     *   "indice_promedio":   82,
     *   "ruido_promedio":    33.2,
     *   "temp_max":          30,
     *   "temp_min":          18,
     *   "ruido_max":         55,
     *   "total_lecturas":    120
     * }
     */
    public function promedios(): ResponseInterface
    {
        $promedios = $this->lecturaModel->getPromediosGlobales();

        // Si no hay datos aún, devolver ceros en lugar de null
        if (!$promedios || (int)$promedios['total_lecturas'] === 0) {
            return $this->responder(200, [
                'temp_promedio'    => 0,
                'humedad_promedio' => 0,
                'ruido_promedio'   => 0,
                'indice_promedio'  => 0,
                'temp_max'         => 0,
                'temp_min'         => 0,
                'ruido_max'        => 0,
                'ruido_min'        => 0,
                'total_lecturas'   => 0,
            ]);
        }

        return $this->responder(200, $promedios);
    }

    // ──────────────────────────────────────────────────────────────────────
    // GET /api/estadisticas/semana
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Retorna estadísticas agrupadas por día para los últimos 7 días.
     * Ideal para gráficas de tendencias semanales en el dashboard.
     *
     * Query param opcional: ?dias=14 (por defecto 7)
     *
     * Respuesta: array de objetos por día:
     * [
     *   { "dia": "2025-06-10", "temp_promedio": 23, "indice_promedio": 85, ... },
     *   ...
     * ]
     */
    public function semana(): ResponseInterface
    {
        $dias = (int)($this->request->getGet('dias') ?? 7);

        // Limitar a máximo 30 días para no sobrecargar
        $dias = min($dias, 30);

        $estadisticas = $this->lecturaModel->getEstadisticasPorDia($dias);

        return $this->responder(200, $estadisticas);
    }

    // ──────────────────────────────────────────────────────────────────────
    // GET /api/estadisticas/hoy
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Devuelve los promedios exclusivamente del día de hoy.
     * Útil para el indicador de calidad nocturna actual.
     */
    public function hoy(): ResponseInterface
    {
        $db = \Config\Database::connect();

        $query = $db->query("
            SELECT
                ROUND(AVG(temperatura), 2)  AS temp_promedio,
                ROUND(AVG(humedad), 2)      AS humedad_promedio,
                ROUND(AVG(ruido), 2)        AS ruido_promedio,
                ROUND(AVG(indice_sueno), 2) AS indice_promedio,
                SUM(movimiento)             AS total_movimientos,
                COUNT(*)                    AS total_lecturas,
                MIN(fecha)                  AS primera_lectura,
                MAX(fecha)                  AS ultima_lectura
            FROM lecturas
            WHERE DATE(fecha) = CURDATE()
        ");

        $resultado = $query->getRowArray();

        if (!$resultado || (int)$resultado['total_lecturas'] === 0) {
            return $this->responder(200, [
                'mensaje'          => 'Sin lecturas hoy todavía.',
                'total_lecturas'   => 0,
                'indice_promedio'  => null,
            ]);
        }

        // Agregar calificación textual del índice
        $resultado['calidad'] = $this->calificarIndice((float)$resultado['indice_promedio']);

        return $this->responder(200, $resultado);
    }

    // ──────────────────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Convierte el índice numérico en una calificación textual.
     */
    private function calificarIndice(float $indice): string
    {
        if ($indice >= 80) return 'Excelente';
        if ($indice >= 60) return 'Bueno';
        if ($indice >= 40) return 'Regular';
        return 'Deficiente';
    }

    private function responder(int $codigo, mixed $data): ResponseInterface
    {
        return $this->response
            ->setStatusCode($codigo)
            ->setContentType('application/json')
            ->setJSON($data);
    }
}