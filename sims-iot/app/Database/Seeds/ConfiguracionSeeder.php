<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ConfiguracionSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'temp_min' => 18,
            'temp_max' => 26,
            'hum_min' => 30,
            'hum_max' => 70,
            'ruido_max' => 40,
            'mov_max' => 10,
        ];

        // Verificar si ya existe configuración
        $existe = $this->db->table('configuraciones')->countAll() > 0;
        
        if ($existe) {
            // Actualizar en lugar de insertar
            $this->db->table('configuraciones')->update($data, ['id' => 1]);
            echo "✅ Configuración actualizada correctamente.\n";
        } else {
            // Insertar nueva configuración
            $this->db->table('configuraciones')->insert($data);
            echo "✅ Configuración insertada correctamente.\n";
        }
        
        echo "📋 Valores:\n";
        echo "   🌡️ Temperatura: {$data['temp_min']}°C - {$data['temp_max']}°C\n";
        echo "   💧 Humedad: {$data['hum_min']}% - {$data['hum_max']}%\n";
        echo "   🔊 Ruido máximo: {$data['ruido_max']} dB\n";
        echo "   🌀 Movimiento máximo: {$data['mov_max']} eventos\n";
    }
}