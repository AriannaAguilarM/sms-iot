<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\LecturaModel;
use CodeIgniter\HTTP\ResponseInterface;


class EstadisticasController extends BaseController
{
    protected LecturaModel $lecturaModel;

    public function __construct()
    {
        $this->lecturaModel = new LecturaModel();
    }

    
    public function promedios(): ResponseInterface
    {
        $promedios = $this->lecturaModel->getPromediosGlobales();

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

    
    public function semana(): ResponseInterface
    {
        $dias = (int)($this->request->getGet('dias') ?? 7);

        $dias = min($dias, 30);

        $estadisticas = $this->lecturaModel->getEstadisticasPorDia($dias);

        return $this->responder(200, $estadisticas);
    }

    
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

        $resultado['calidad'] = $this->calificarIndice((float)$resultado['indice_promedio']);

        return $this->responder(200, $resultado);
    }

    
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