<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AlertasSeeder extends Seeder
{
    public function run()
    {
        // Verificar si ya hay alertas
        $existen = $this->db->table('alertas')->countAll();
        
        if ($existen > 0) {
            echo "⚠️ Ya existen alertas en la base de datos.\n";
            echo "¿Deseas agregar más alertas de prueba? (y/n): ";
            $confirm = trim(fgets(STDIN));
            
            if (strtolower($confirm) !== 'y') {
                echo "❌ Operación cancelada.\n";
                return;
            }
        }

        $alertas = [
            [
                'mensaje' => '🌡️ Temperatura alta: 28.5°C (máximo: 26°C)',
                'nivel' => 'alto',
                'fecha' => date('Y-m-d H:i:s', strtotime('-1 hour'))
            ],
            [
                'mensaje' => '🔊 Ruido elevado: 55 dB (máximo: 40 dB)',
                'nivel' => 'alto',
                'fecha' => date('Y-m-d H:i:s', strtotime('-2 hours'))
            ],
            [
                'mensaje' => '💧 Humedad baja: 25% (mínimo: 30%)',
                'nivel' => 'medio',
                'fecha' => date('Y-m-d H:i:s', strtotime('-3 hours'))
            ],
            [
                'mensaje' => '⭐ Calidad de sueño baja: 45/100 (mínimo: 60)',
                'nivel' => 'medio',
                'fecha' => date('Y-m-d H:i:s', strtotime('-4 hours'))
            ],
            [
                'mensaje' => '🌀 Movimiento excesivo: 15 eventos (máximo: 10)',
                'nivel' => 'medio',
                'fecha' => date('Y-m-d H:i:s', strtotime('-5 hours'))
            ],
            [
                'mensaje' => '🌡️ Temperatura baja: 16.2°C (mínimo: 18°C)',
                'nivel' => 'medio',
                'fecha' => date('Y-m-d H:i:s', strtotime('-6 hours'))
            ],
            [
                'mensaje' => '💧 Humedad alta: 75% (máximo: 70%)',
                'nivel' => 'bajo',
                'fecha' => date('Y-m-d H:i:s', strtotime('-7 hours'))
            ],
            [
                'mensaje' => '🔊 Ruido elevado: 42 dB (máximo: 40 dB)',
                'nivel' => 'bajo',
                'fecha' => date('Y-m-d H:i:s', strtotime('-8 hours'))
            ],
        ];

        $this->db->table('alertas')->insertBatch($alertas);
        echo "✅ " . count($alertas) . " alertas de prueba insertadas correctamente.\n";
        echo "📊 Total de alertas en la base de datos: " . $this->db->table('alertas')->countAll() . "\n";
    }
}