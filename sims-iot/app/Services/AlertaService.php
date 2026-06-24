<?php

namespace App\Services;

use App\Models\AlertaModel;
use App\Models\ConfiguracionModel;

class AlertaService
{
    protected $alertaModel;
    protected $configuracionModel;

    public function __construct()
    {
        $this->alertaModel = new AlertaModel();
        $this->configuracionModel = new ConfiguracionModel();
    }

    /**
     * Verificar una lectura y generar alertas si es necesario
     */
    public function verificarLectura($lectura)
    {
        $alertasGeneradas = [];
        $config = $this->configuracionModel->getUmbrales();

        // Extraer valores
        $temp = (float)$lectura['temperatura'];
        $hum = (float)$lectura['humedad'];
        $ruido = (float)$lectura['ruido'];
        $ics = (float)$lectura['indice_sueno'];
        $movimiento = (int)$lectura['movimiento'];

        // 1. Verificar Temperatura (temp_min, temp_max)
        if ($temp < $config['temp_min']) {
            $alertasGeneradas[] = $this->crearAlerta(
                "🌡️ Temperatura baja: {$temp}°C (mínimo: {$config['temp_min']}°C)",
                'medio'
            );
        }
        if ($temp > $config['temp_max']) {
            $alertasGeneradas[] = $this->crearAlerta(
                "🌡️ Temperatura alta: {$temp}°C (máximo: {$config['temp_max']}°C)",
                'alto'
            );
        }

        // 2. Verificar Humedad (hum_min, hum_max)
        if ($hum < $config['hum_min']) {
            $alertasGeneradas[] = $this->crearAlerta(
                "💧 Humedad baja: {$hum}% (mínimo: {$config['hum_min']}%)",
                'medio'
            );
        }
        if ($hum > $config['hum_max']) {
            $alertasGeneradas[] = $this->crearAlerta(
                "💧 Humedad alta: {$hum}% (máximo: {$config['hum_max']}%)",
                'medio'
            );
        }

        // 3. Verificar Ruido (ruido_max)
        if ($ruido > $config['ruido_max']) {
            $nivel = $ruido > $config['ruido_max'] * 1.5 ? 'alto' : 'medio';
            $alertasGeneradas[] = $this->crearAlerta(
                "🔊 Ruido elevado: {$ruido} dB (máximo: {$config['ruido_max']} dB)",
                $nivel
            );
        }

        // 4. Verificar Movimiento (mov_max)
        if ($movimiento > $config['mov_max']) {
            $nivel = $movimiento > $config['mov_max'] * 2 ? 'alto' : 'medio';
            $alertasGeneradas[] = $this->crearAlerta(
                "🌀 Movimiento excesivo: {$movimiento} eventos (máximo: {$config['mov_max']})",
                $nivel
            );
        }

        // 5. Verificar ICS (mínimo fijo en 60)
        if ($ics < 60) {
            $nivel = $ics < 30 ? 'alto' : 'medio';
            $alertasGeneradas[] = $this->crearAlerta(
                "⭐ Calidad de sueño baja: {$ics}/100 (mínimo recomendado: 60/100)",
                $nivel
            );
        }

        return array_filter($alertasGeneradas);
    }

    /**
     * Crear una alerta individual
     */
    protected function crearAlerta($mensaje, $nivel)
    {
        // Verificar si ya existe una alerta similar en las últimas 2 horas
        $existe = $this->alertaModel
            ->where('mensaje', $mensaje)
            ->where('fecha >=', date('Y-m-d H:i:s', strtotime('-2 hours')))
            ->first();

        if ($existe) {
            return null; // No duplicar alertas
        }

        $data = [
            'mensaje' => $mensaje,
            'nivel'   => $nivel,
            'fecha'   => date('Y-m-d H:i:s'),
        ];

        $this->alertaModel->insert($data);
        return $data;
    }

    /**
     * Obtener alertas recientes
     */
    public function getAlertasRecientes($limit = 50, $nivel = null)
    {
        return $this->alertaModel->getAlertas($limit, $nivel);
    }

    /**
     * Obtener resumen de alertas
     */
    public function getResumen()
    {
        return $this->alertaModel->getResumen();
    }

    /**
     * Limpiar alertas antiguas
     */
    public function limpiarAntiguas()
    {
        return $this->alertaModel->limpiarAntiguas();
    }
}