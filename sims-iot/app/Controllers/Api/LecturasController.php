<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\LecturaModel;
use App\Services\AlertaService;
use CodeIgniter\HTTP\ResponseInterface;


class LecturasController extends BaseController
{
    protected LecturaModel  $lecturaModel;
    protected AlertaService $alertaService;

    public function __construct()
    {
        $this->lecturaModel  = new LecturaModel();
        $this->alertaService = new AlertaService();
    }

    // ──────────────────────────────────────────────────────────────────────
    // POST /api/lecturas
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Recibe los datos del ESP32 en formato JSON, los valida,
     * los guarda en la BD y genera alertas automáticamente.
     *
     * Body esperado:
     * {
     *   "temperatura":  24,
     *   "humedad":      55,
     *   "ruido":        35,
     *   "movimiento":   0,
     *   "indice_sueno": 87
     * }
     */
    public function store(): ResponseInterface
    {
        // Leer JSON del body
        $json = $this->request->getJSON(true);

        if (empty($json)) {
            return $this->responder(400, [
                'status'  => 'error',
                'mensaje' => 'No se recibieron datos JSON válidos.',
            ]);
        }

        // Construir el arreglo con timestamp automático
        $data = [
            'temperatura'  => $json['temperatura']  ?? null,
            'humedad'      => $json['humedad']      ?? null,
            'ruido'        => $json['ruido']        ?? null,
            'movimiento'   => $json['movimiento']   ?? null,
            'indice_sueno' => $json['indice_sueno'] ?? null,
            'fecha'        => date('Y-m-d H:i:s'),
        ];

        // Validar con las reglas del modelo
        if (!$this->lecturaModel->validate($data)) {
            return $this->responder(422, [
                'status'  => 'error',
                'mensaje' => 'Datos inválidos.',
                'errores' => $this->lecturaModel->errors(),
            ]);
        }

        // Guardar en la base de datos
        if (!$this->lecturaModel->insert($data)) {
            return $this->responder(500, [
                'status'  => 'error',
                'mensaje' => 'Error interno al guardar la lectura.',
            ]);
        }

        // Generar alertas automáticas según los umbrales
        $alertas = $this->alertaService->evaluarLectura($data);

        return $this->responder(201, [
            'status'        => 'ok',
            'mensaje'       => 'Lectura registrada',
            'alertas_total' => count($alertas),
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────
    // GET /api/lecturas
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Devuelve todas las lecturas. Soporta filtros opcionales por query string:
     *   ?limit=50
     *   ?fecha_inicio=2025-06-01
     *   ?fecha_fin=2025-06-30
     */
    public function index(): ResponseInterface
    {
        $limit      = (int)($this->request->getGet('limit')       ?? 50);
        $fechaInicio = $this->request->getGet('fecha_inicio') ?? null;
        $fechaFin    = $this->request->getGet('fecha_fin')    ?? null;

        $lecturas = $this->lecturaModel->getLecturasFiltradas($fechaInicio, $fechaFin, $limit);

        return $this->responder(200, $lecturas);
    }

    // ──────────────────────────────────────────────────────────────────────
    // GET /api/lecturas/ultima
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Devuelve la lectura más reciente (usada por el dashboard en tiempo real).
     */
    public function ultima(): ResponseInterface
    {
        $ultima = $this->lecturaModel->getUltimaLectura();

        if (!$ultima) {
            return $this->responder(404, [
                'status'  => 'error',
                'mensaje' => 'No hay lecturas disponibles aún.',
            ]);
        }

        return $this->responder(200, $ultima);
    }

    // ──────────────────────────────────────────────────────────────────────
    // Helper interno
    // ──────────────────────────────────────────────────────────────────────

    private function responder(int $codigo, mixed $data): ResponseInterface
    {
        return $this->response
            ->setStatusCode($codigo)
            ->setContentType('application/json')
            ->setJSON($data);
    }
}