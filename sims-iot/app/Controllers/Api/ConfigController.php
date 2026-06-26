<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\ConfiguracionModel;

class ConfigController extends ResourceController
{
    protected $modelName = ConfiguracionModel::class;
    protected $format = 'json';

    /**
     * Obtener configuración
     * GET /api/config
     */
    public function index()
    {
        $config = $this->model->getUmbrales();
        return $this->respond($config);
    }

    /**
     * Guardar umbrales
     * POST /api/config/umbrales
     */
    public function umbrales()
    {
        $data = $this->request->getJSON(true);
        
        // ✅ Campos correctos según tu tabla
        $validFields = ['temp_min', 'temp_max', 'hum_min', 'hum_max', 'ruido_max', 'mov_max'];
        $filtered = [];
        
        foreach ($validFields as $field) {
            if (isset($data[$field]) && $data[$field] !== null && $data[$field] !== '') {
                $filtered[$field] = (float)$data[$field];
            }
        }
        
        if (empty($filtered)) {
            return $this->fail('No se enviaron datos válidos', 400);
        }

        $this->model->guardarUmbrales($filtered);
        
        return $this->respond([
            'status' => 'ok',
            'mensaje' => 'Configuración guardada correctamente',
            'data' => $this->model->getUmbrales()
        ]);
    }

    /**
     * Inicializar configuración por defecto
     * POST /api/config/inicializar
     */
    public function inicializar()
    {
        $result = $this->model->inicializar();
        return $this->respond([
            'status' => 'ok',
            'mensaje' => $result ? 'Configuración inicializada' : 'La configuración ya existe',
            'data' => $this->model->getUmbrales()
        ]);
    }
}