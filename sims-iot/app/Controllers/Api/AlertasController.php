<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\AlertaModel;
use App\Services\AlertaService;

class AlertasController extends ResourceController
{
    protected $modelName = AlertaModel::class;
    protected $format = 'json';
    protected $alertaService;

    public function __construct()
    {
        $this->alertaService = new AlertaService();
    }

    /**
     * Obtener alertas
     * GET /api/alertas
     */
    public function index()
    {
        $limit = $this->request->getGet('limit') ?? 100;
        $nivel = $this->request->getGet('nivel');
        
        $alertas = $this->alertaService->getAlertasRecientes($limit, $nivel);
        return $this->respond($alertas);
    }

    /**
     * Obtener resumen de alertas
     * GET /api/alertas/resumen
     */
    public function resumen()
    {
        $resumen = $this->alertaService->getResumen();
        return $this->respond($resumen);
    }

    /**
     * Limpiar alertas antiguas
     * DELETE /api/alertas/limpiar
     */
    public function limpiar()
    {
        $result = $this->alertaService->limpiarAntiguas();
        return $this->respond([
            'status' => 'ok',
            'mensaje' => 'Alertas antiguas eliminadas',
            'eliminadas' => $result
        ]);
    }
}