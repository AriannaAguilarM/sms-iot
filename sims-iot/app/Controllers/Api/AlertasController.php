<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\AlertaModel;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * AlertasController
 * 
 * Gestiona la consulta y limpieza de alertas generadas automáticamente.
 * 
 * GET    /api/alertas          → Listar alertas recientes
 * DELETE /api/alertas/limpiar  → Eliminar alertas con más de 30 días
 * GET    /api/alertas/resumen  → Conteo por nivel (alto/medio/bajo)
 */
class AlertasController extends BaseController
{
    protected AlertaModel $alertaModel;

    public function __construct()
    {
        $this->alertaModel = new AlertaModel();
    }

    // ──────────────────────────────────────────────────────────────────────
    // GET /api/alertas
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Retorna las alertas más recientes.
     * Query param opcional: ?limit=20
     *
     * Respuesta:
     * [
     *   { "id": 1, "mensaje": "Ruido elevado...", "nivel": "alto", "fecha": "..." },
     *   ...
     * ]
     */
    public function index(): ResponseInterface
    {
        $limit = (int)($this->request->getGet('limit') ?? 20);
        $limit = min($limit, 100); // máximo 100 por seguridad

        $alertas = $this->alertaModel->getAlertasRecientes($limit);

        return $this->responder(200, $alertas);
    }

    // ──────────────────────────────────────────────────────────────────────
    // GET /api/alertas/resumen
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Devuelve un resumen del conteo de alertas por nivel en las últimas 24h.
     * Útil para los indicadores del dashboard.
     *
     * Respuesta:
     * {
     *   "alto":  2,
     *   "medio": 3,
     *   "bajo":  5,
     *   "total": 10
     * }
     */
    public function resumen(): ResponseInterface
    {
        $conteos = $this->alertaModel->contarPorNivel();

        $resumen = ['alto' => 0, 'medio' => 0, 'bajo' => 0, 'total' => 0];

        foreach ($conteos as $fila) {
            $nivel = $fila['nivel'];
            if (array_key_exists($nivel, $resumen)) {
                $resumen[$nivel]   = (int)$fila['total'];
                $resumen['total'] += (int)$fila['total'];
            }
        }

        return $this->responder(200, $resumen);
    }

    // ──────────────────────────────────────────────────────────────────────
    // DELETE /api/alertas/limpiar
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Elimina alertas antiguas (más de 30 días).
     * Operación de mantenimiento para el administrador.
     */
    public function limpiar(): ResponseInterface
    {
        $dias        = (int)($this->request->getGet('dias') ?? 30);
        $eliminadas  = $this->alertaModel->limpiarAnteriores($dias);

        return $this->responder(200, [
            'status'    => 'ok',
            'mensaje'   => "Se eliminaron {$eliminadas} alertas con más de {$dias} días.",
            'eliminadas' => $eliminadas,
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────
    private function responder(int $codigo, mixed $data): ResponseInterface
    {
        return $this->response
            ->setStatusCode($codigo)
            ->setContentType('application/json')
            ->setJSON($data);
    }
}