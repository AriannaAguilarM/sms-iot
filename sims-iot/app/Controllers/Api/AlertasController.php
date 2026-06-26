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

    
    public function index()
    {
        $limit = $this->request->getGet('limit') ?? 100;
        $nivel = $this->request->getGet('nivel');
        
        $alertas = $this->alertaService->getAlertasRecientes($limit, $nivel);
        return $this->respond($alertas);
    }

    
    public function resumen()
    {
        $resumen = $this->alertaService->getResumen();
        return $this->respond($resumen);
    }

    
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