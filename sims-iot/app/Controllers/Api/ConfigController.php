<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Services\AlertaService;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * ConfigController
 * 
 * Permite al usuario configurar umbrales de alerta.
 * El ESP32 también consulta estos umbrales para ajustar su comportamiento.
 * 
 * GET  /api/config           → ESP32 obtiene umbrales actuales
 * POST /api/config/umbrales  → Usuario actualiza los umbrales
 */
class ConfigController extends BaseController
{
    protected AlertaService $alertaService;

    // Umbrales almacenados en sesión/archivo de configuración
    // En producción se pueden guardar en una tabla `configuracion` de MySQL
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

    // ──────────────────────────────────────────────────────────────────────
    // GET /api/config
    // ──────────────────────────────────────────────────────────────────────

    /**
     * El ESP32 llama a este endpoint al iniciar para recibir
     * los umbrales de alerta configurados por el usuario.
     *
     * Respuesta:
     * {
     *   "temperatura_max": 28,
     *   "temperatura_min": 16,
     *   "humedad_max":     70,
     *   "humedad_min":     30,
     *   "ruido_max":       40
     * }
     */
    public function index(): ResponseInterface
    {
        $umbrales = $this->getUmbralesActuales();

        return $this->responder(200, $umbrales);
    }

    // ──────────────────────────────────────────────────────────────────────
    // POST /api/config/umbrales
    // ──────────────────────────────────────────────────────────────────────

    /**
     * El usuario envía nuevos umbrales desde el dashboard.
     *
     * Body esperado:
     * {
     *   "temperatura_max": 26,
     *   "ruido_max": 35
     * }
     *
     * Solo se actualizan los campos enviados (PATCH semántico vía POST).
     */
    public function actualizarUmbrales(): ResponseInterface
    {
        $json = $this->request->getJSON(true);

        if (empty($json)) {
            return $this->responder(400, [
                'status'  => 'error',
                'mensaje' => 'No se recibieron datos JSON.',
            ]);
        }

        $camposPermitidos = [
            'temperatura_max', 'temperatura_min',
            'humedad_max',     'humedad_min',
            'ruido_max',
        ];

        $db      = \Config\Database::connect();
        $errores = [];

        foreach ($json as $campo => $valor) {
            if (!in_array($campo, $camposPermitidos)) {
                $errores[] = "Campo no permitido: {$campo}";
                continue;
            }

            if (!is_numeric($valor)) {
                $errores[] = "El campo {$campo} debe ser numérico.";
                continue;
            }

            // Upsert en la tabla `configuracion` (clave → valor)
            $existente = $db->table('configuracion')->where('clave', $campo)->get()->getRowArray();

            if ($existente) {
                $db->table('configuracion')->where('clave', $campo)->update(['valor' => $valor]);
            } else {
                $db->table('configuracion')->insert(['clave' => $campo, 'valor' => $valor]);
            }
        }

        if (!empty($errores)) {
            return $this->responder(422, [
                'status'  => 'error',
                'mensaje' => 'Algunos campos no se procesaron.',
                'errores' => $errores,
            ]);
        }

        return $this->responder(200, [
            'status'   => 'ok',
            'mensaje'  => 'Umbrales actualizados correctamente.',
            'umbrales' => $this->getUmbralesActuales(),
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Lee los umbrales desde la BD o usa los defaults si no existen.
     */
    private function getUmbralesActuales(): array
    {
        $db = \Config\Database::connect();

        // Verificar si la tabla existe antes de consultar
        try {
            $rows = $db->table('configuracion')->get()->getResultArray();
        } catch (\Throwable $e) {
            return $this->umbralesDefault;
        }

        if (empty($rows)) {
            return $this->umbralesDefault;
        }

        $umbrales = $this->umbralesDefault;
        foreach ($rows as $row) {
            $umbrales[$row['clave']] = (float)$row['valor'];
        }

        return $umbrales;
    }

    private function responder(int $codigo, mixed $data): ResponseInterface
    {
        return $this->response
            ->setStatusCode($codigo)
            ->setContentType('application/json')
            ->setJSON($data);
    }
}