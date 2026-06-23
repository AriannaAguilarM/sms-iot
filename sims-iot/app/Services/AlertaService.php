<?php

namespace App\Services;

use App\Models\AlertaModel;

/**
 * AlertaService
 * 
 * Contiene la lógica de negocio para generar alertas automáticas
 * basadas en los datos recibidos del ESP32.
 * 
 * Umbrales por defecto (configurables):
 *   - Temperatura: 18°C – 28°C
 *   - Humedad:     30%  – 70%
 *   - Ruido:       max  40 dB
 *   - Movimiento:  0 = sin movimiento, 1 = detectado
 */
class AlertaService
{
    protected AlertaModel $alertaModel;

    // ─── Umbrales predeterminados ──────────────────────────────────────────
    private array $umbrales = [
        'temperatura_max' => 28.0,
        'temperatura_min' => 16.0,
        'humedad_max'     => 70.0,
        'humedad_min'     => 30.0,
        'ruido_max'       => 40.0,
    ];

    public function __construct()
    {
        $this->alertaModel = new AlertaModel();
    }

    // ──────────────────────────────────────────────────────────────────────
    // MÉTODO PRINCIPAL: Analiza una lectura y genera alertas si aplica
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Evalúa una lectura completa del ESP32 y persiste alertas si se
     * detectan condiciones fuera de rango.
     *
     * @param  array $lectura  Datos recibidos del ESP32
     * @return array           Lista de alertas generadas (puede ser vacía)
     */
    public function evaluarLectura(array $lectura): array
    {
        $alertasGeneradas = [];

        // ── Temperatura ───────────────────────────────────────────────────
        if ((float)$lectura['temperatura'] > $this->umbrales['temperatura_max']) {
            $alertasGeneradas[] = $this->registrarAlerta(
                "Temperatura alta: {$lectura['temperatura']}°C — El rango ideal para dormir es 18-22°C.",
                'alto'
            );
        } elseif ((float)$lectura['temperatura'] < $this->umbrales['temperatura_min']) {
            $alertasGeneradas[] = $this->registrarAlerta(
                "Temperatura baja: {$lectura['temperatura']}°C — El rango ideal para dormir es 18-22°C.",
                'medio'
            );
        }

        // ── Humedad ───────────────────────────────────────────────────────
        if ((float)$lectura['humedad'] > $this->umbrales['humedad_max']) {
            $alertasGeneradas[] = $this->registrarAlerta(
                "Humedad muy alta: {$lectura['humedad']}% — El rango óptimo es 40-60%. Considera ventilar el cuarto.",
                'medio'
            );
        } elseif ((float)$lectura['humedad'] < $this->umbrales['humedad_min']) {
            $alertasGeneradas[] = $this->registrarAlerta(
                "Humedad muy baja: {$lectura['humedad']}% — El rango óptimo es 40-60%. El ambiente puede estar muy seco.",
                'bajo'
            );
        }

        // ── Ruido ─────────────────────────────────────────────────────────
        if ((float)$lectura['ruido'] > $this->umbrales['ruido_max']) {
            $nivel = ($lectura['ruido'] > 60) ? 'alto' : 'medio';
            $alertasGeneradas[] = $this->registrarAlerta(
                "Ruido elevado detectado: {$lectura['ruido']} dB — Para un sueño reparador se recomienda < 30 dB.",
                $nivel
            );
        }

        // ── Movimiento ────────────────────────────────────────────────────
        if ((int)$lectura['movimiento'] === 1) {
            $alertasGeneradas[] = $this->registrarAlerta(
                'Movimiento nocturno detectado — Puede indicar sueño fragmentado o inquieto.',
                'bajo'
            );
        }

        // ── Índice de calidad bajo ────────────────────────────────────────
        if (isset($lectura['indice_sueno']) && (float)$lectura['indice_sueno'] < 50) {
            $nivel = ($lectura['indice_sueno'] < 30) ? 'alto' : 'medio';
            $alertasGeneradas[] = $this->registrarAlerta(
                "Índice de Calidad del Sueño bajo: {$lectura['indice_sueno']}/100 — Considera revisar las condiciones del cuarto.",
                $nivel
            );
        }

        return $alertasGeneradas;
    }

    // ──────────────────────────────────────────────────────────────────────
    // HELPERS
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Persiste una alerta en la base de datos y retorna el array guardado.
     */
    private function registrarAlerta(string $mensaje, string $nivel): array
    {
        $data = [
            'mensaje' => $mensaje,
            'nivel'   => $nivel,
            'fecha'   => date('Y-m-d H:i:s'),
        ];

        $this->alertaModel->insert($data);

        return $data;
    }

    /**
     * Actualizar umbrales en tiempo de ejecución (desde configuración del usuario).
     */
    public function setUmbrales(array $nuevos): void
    {
        foreach ($nuevos as $clave => $valor) {
            if (array_key_exists($clave, $this->umbrales)) {
                $this->umbrales[$clave] = (float)$valor;
            }
        }
    }

    /**
     * Retorna los umbrales actuales (para exponerlos al ESP32).
     */
    public function getUmbrales(): array
    {
        return $this->umbrales;
    }
}