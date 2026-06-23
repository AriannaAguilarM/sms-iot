<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Services\AlertaService;
use CodeIgniter\HTTP\ResponseInterface;

   
    class ConfigController extends BaseController
    {
        protected AlertaService $alertaService;

        // Umbrales almacenados en sesión/archivo de configuración
        private array $umbralesDefault = [
            'temperatura_max' => 28.0,
            'temperatura_min' => 16.0,
            'humedad_max'     => 70.0,
            'humedad_min'     => 30.0,
            'ruido_max'       => 40.0,
        ];

        public function __construct()
        {
            $this->alertaService = new AlertaService();
        }

        public function index(): ResponseInterface
        {
            $umbrales = $this->getUmbralesActuales();

            return $this->responder(200, $umbrales);
        }

        
        public function actualizarUmbrales(): ResponseInterface
    {
        $json = $this->request->getJSON(true);

        if (empty($json)) {
            return $this->responder(400, [
                'status' => 'error',
                'mensaje' => 'No se recibieron datos JSON.',
            ]);
        }

        $db = \Config\Database::connect();

            $data = [];

        if (isset($json['temp_min'])) {
            $data['temp_min'] = $json['temp_min'];
        }

        if (isset($json['temp_max'])) {
            $data['temp_max'] = $json['temp_max'];
        }

        if (isset($json['hum_min'])) {
            $data['hum_min'] = $json['hum_min'];
        }

        if (isset($json['hum_max'])) {
            $data['hum_max'] = $json['hum_max'];
        }

        if (isset($json['ruido_max'])) {
            $data['ruido_max'] = $json['ruido_max'];
        }
        if (isset($json['mov_max'])) {
            $data['mov_max'] = $json['mov_max'];
        }

            if (empty($data)) {
                return $this->responder(422, [
                    'status' => 'error',
                    'mensaje' => 'No hay campos válidos para actualizar.',
                ]);
            }

        $db->table('configuraciones')
            ->where('id', 1)
            ->update($data);

        return $this->responder(200, [
            'status' => 'ok',
            'mensaje' => 'Umbrales actualizados correctamente.',
        ]);
    }

    private function getUmbralesActuales(): array
    {
        $db = \Config\Database::connect();

        $row = $db->table('configuraciones')->where('id', 1)->get()->getRowArray();

        if (!$row) {
            return [
                'temperatura_minima' => 18,
                'temperatura_maxima' => 28,
                'humedad_minima' => 30,
                'humedad_maxima' => 70,
                'ruido_maximo' => 40,
                'movimiento_maximo' => 10,
            ];
        }

        return $row;
    }

        private function responder(int $codigo, mixed $data): ResponseInterface
        {
            return $this->response
                ->setStatusCode($codigo)
                ->setContentType('application/json')
                ->setJSON($data);
        }
    }