<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\LecturaModel;
use App\Services\AlertaService;

class LecturasController extends ResourceController
{
    protected $modelName = LecturaModel::class;
    protected $format = 'json';
    protected $alertaService;

    public function __construct()
    {
        $this->alertaService = new AlertaService();
    }

    public function store()
    {
        $data = $this->request->getJSON(true);

        if (empty($data['temperatura'])) {
            return $this->fail('La temperatura es requerida', 400);
        }

        if (!$this->model->insert($data)) {
            return $this->fail($this->model->errors(), 400);
        }

        $lecturaId = $this->model->getInsertID();
        $lectura = $this->model->find($lecturaId);

        $alertasGeneradas = $this->alertaService->verificarLectura($lectura);
        $alertasCount = count(array_filter($alertasGeneradas));

        return $this->respond([
            'status' => 'ok',
            'mensaje' => 'Lectura registrada',
            'id' => $lecturaId,
            'alertas_generadas' => $alertasCount
        ]);
    }

    
    public function ultima()
    {
        $lectura = $this->model->orderBy('id', 'DESC')->first();
        
        if (!$lectura) {
            return $this->failNotFound('No hay lecturas disponibles aún.');
        }

        return $this->respond($lectura);
    }

    
    public function index()
    {
        $limit = $this->request->getGet('limit') ?? 50;
        $fechaInicio = $this->request->getGet('fecha_inicio');
        $fechaFin = $this->request->getGet('fecha_fin');

        $builder = $this->model->builder();
        $builder->orderBy('id', 'DESC');
        
        if ($limit) {
            $builder->limit((int)$limit);
        }

        if ($fechaInicio) {
            $builder->where('fecha >=', $fechaInicio . ' 00:00:00');
        }

        if ($fechaFin) {
            $builder->where('fecha <=', $fechaFin . ' 23:59:59');
        }

        $result = $builder->get()->getResultArray();
        return $this->respond($result);
    }
}