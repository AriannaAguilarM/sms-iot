<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class LecturasSeeder extends Seeder
{
    public function run()
    {
        // Datos simulados para una noche completa (8 horas)
        // Cada registro representa 5 minutos de datos
        $datos = [
            // Hora 1: 23:00 - 00:00 (Inicio del sueño)
            ['temperatura' => 22.5, 'humedad' => 45.0, 'ruido' => 25.0, 'movimiento' => 8, 'indice_sueno' => 55],
            ['temperatura' => 22.3, 'humedad' => 46.0, 'ruido' => 22.0, 'movimiento' => 5, 'indice_sueno' => 60],
            ['temperatura' => 22.1, 'humedad' => 47.0, 'ruido' => 20.0, 'movimiento' => 3, 'indice_sueno' => 65],
            ['temperatura' => 21.8, 'humedad' => 48.0, 'ruido' => 18.0, 'movimiento' => 2, 'indice_sueno' => 70],
            
            // Hora 2: 00:00 - 01:00 (Sueño profundo)
            ['temperatura' => 21.5, 'humedad' => 50.0, 'ruido' => 15.0, 'movimiento' => 1, 'indice_sueno' => 82],
            ['temperatura' => 21.2, 'humedad' => 52.0, 'ruido' => 12.0, 'movimiento' => 0, 'indice_sueno' => 88],
            ['temperatura' => 21.0, 'humedad' => 53.0, 'ruido' => 10.0, 'movimiento' => 0, 'indice_sueno' => 90],
            ['temperatura' => 20.8, 'humedad' => 54.0, 'ruido' => 8.0, 'movimiento' => 0, 'indice_sueno' => 92],
            ['temperatura' => 20.5, 'humedad' => 55.0, 'ruido' => 8.0, 'movimiento' => 0, 'indice_sueno' => 93],
            ['temperatura' => 20.3, 'humedad' => 56.0, 'ruido' => 7.0, 'movimiento' => 0, 'indice_sueno' => 94],
            ['temperatura' => 20.0, 'humedad' => 57.0, 'ruido' => 7.0, 'movimiento' => 0, 'indice_sueno' => 95],
            ['temperatura' => 20.0, 'humedad' => 58.0, 'ruido' => 6.0, 'movimiento' => 0, 'indice_sueno' => 95],
            
            // Hora 3: 01:00 - 02:00 (Sueño profundo)
            ['temperatura' => 19.8, 'humedad' => 59.0, 'ruido' => 6.0, 'movimiento' => 0, 'indice_sueno' => 96],
            ['temperatura' => 19.5, 'humedad' => 60.0, 'ruido' => 5.0, 'movimiento' => 0, 'indice_sueno' => 96],
            ['temperatura' => 19.3, 'humedad' => 60.0, 'ruido' => 5.0, 'movimiento' => 0, 'indice_sueno' => 97],
            ['temperatura' => 19.2, 'humedad' => 61.0, 'ruido' => 5.0, 'movimiento' => 0, 'indice_sueno' => 97],
            ['temperatura' => 19.0, 'humedad' => 61.0, 'ruido' => 4.0, 'movimiento' => 0, 'indice_sueno' => 98],
            ['temperatura' => 19.0, 'humedad' => 62.0, 'ruido' => 4.0, 'movimiento' => 0, 'indice_sueno' => 98],
            ['temperatura' => 19.0, 'humedad' => 62.0, 'ruido' => 4.0, 'movimiento' => 0, 'indice_sueno' => 98],
            ['temperatura' => 19.0, 'humedad' => 62.0, 'ruido' => 4.0, 'movimiento' => 0, 'indice_sueno' => 98],
            ['temperatura' => 19.0, 'humedad' => 63.0, 'ruido' => 4.0, 'movimiento' => 0, 'indice_sueno' => 98],
            ['temperatura' => 19.0, 'humedad' => 63.0, 'ruido' => 4.0, 'movimiento' => 0, 'indice_sueno' => 98],
            ['temperatura' => 19.0, 'humedad' => 63.0, 'ruido' => 4.0, 'movimiento' => 0, 'indice_sueno' => 98],
            ['temperatura' => 19.0, 'humedad' => 64.0, 'ruido' => 4.0, 'movimiento' => 0, 'indice_sueno' => 98],
            
            // Hora 4: 02:00 - 03:00 (Sueño profundo)
            ['temperatura' => 19.0, 'humedad' => 64.0, 'ruido' => 4.0, 'movimiento' => 0, 'indice_sueno' => 98],
            ['temperatura' => 19.0, 'humedad' => 64.0, 'ruido' => 4.0, 'movimiento' => 0, 'indice_sueno' => 98],
            ['temperatura' => 19.0, 'humedad' => 65.0, 'ruido' => 4.0, 'movimiento' => 0, 'indice_sueno' => 98],
            ['temperatura' => 19.0, 'humedad' => 65.0, 'ruido' => 4.0, 'movimiento' => 0, 'indice_sueno' => 98],
            ['temperatura' => 19.0, 'humedad' => 65.0, 'ruido' => 4.0, 'movimiento' => 1, 'indice_sueno' => 95],
            ['temperatura' => 19.0, 'humedad' => 65.0, 'ruido' => 4.0, 'movimiento' => 0, 'indice_sueno' => 96],
            ['temperatura' => 19.0, 'humedad' => 65.0, 'ruido' => 4.0, 'movimiento' => 0, 'indice_sueno' => 96],
            ['temperatura' => 19.0, 'humedad' => 66.0, 'ruido' => 4.0, 'movimiento' => 0, 'indice_sueno' => 96],
            ['temperatura' => 19.0, 'humedad' => 66.0, 'ruido' => 4.0, 'movimiento' => 0, 'indice_sueno' => 96],
            ['temperatura' => 19.0, 'humedad' => 66.0, 'ruido' => 4.0, 'movimiento' => 0, 'indice_sueno' => 96],
            ['temperatura' => 19.0, 'humedad' => 66.0, 'ruido' => 4.0, 'movimiento' => 0, 'indice_sueno' => 96],
            ['temperatura' => 19.0, 'humedad' => 66.0, 'ruido' => 4.0, 'movimiento' => 0, 'indice_sueno' => 96],
            
            // Hora 5: 03:00 - 04:00 (Sueño con movimiento)
            ['temperatura' => 19.0, 'humedad' => 66.0, 'ruido' => 5.0, 'movimiento' => 2, 'indice_sueno' => 90],
            ['temperatura' => 19.0, 'humedad' => 66.0, 'ruido' => 4.0, 'movimiento' => 1, 'indice_sueno' => 92],
            ['temperatura' => 19.0, 'humedad' => 67.0, 'ruido' => 4.0, 'movimiento' => 0, 'indice_sueno' => 93],
            ['temperatura' => 19.0, 'humedad' => 67.0, 'ruido' => 4.0, 'movimiento' => 0, 'indice_sueno' => 93],
            ['temperatura' => 19.0, 'humedad' => 67.0, 'ruido' => 5.0, 'movimiento' => 3, 'indice_sueno' => 85],
            ['temperatura' => 19.0, 'humedad' => 67.0, 'ruido' => 5.0, 'movimiento' => 2, 'indice_sueno' => 87],
            ['temperatura' => 19.0, 'humedad' => 67.0, 'ruido' => 4.0, 'movimiento' => 1, 'indice_sueno' => 90],
            ['temperatura' => 19.0, 'humedad' => 67.0, 'ruido' => 4.0, 'movimiento' => 0, 'indice_sueno' => 92],
            ['temperatura' => 19.0, 'humedad' => 68.0, 'ruido' => 4.0, 'movimiento' => 0, 'indice_sueno' => 92],
            ['temperatura' => 19.0, 'humedad' => 68.0, 'ruido' => 4.0, 'movimiento' => 0, 'indice_sueno' => 92],
            ['temperatura' => 19.0, 'humedad' => 68.0, 'ruido' => 4.0, 'movimiento' => 0, 'indice_sueno' => 92],
            ['temperatura' => 19.0, 'humedad' => 68.0, 'ruido' => 4.0, 'movimiento' => 0, 'indice_sueno' => 92],
            
            // Hora 6: 04:00 - 05:00 (Sueño ligero)
            ['temperatura' => 19.0, 'humedad' => 68.0, 'ruido' => 6.0, 'movimiento' => 4, 'indice_sueno' => 80],
            ['temperatura' => 19.0, 'humedad' => 68.0, 'ruido' => 5.0, 'movimiento' => 3, 'indice_sueno' => 82],
            ['temperatura' => 19.0, 'humedad' => 68.0, 'ruido' => 5.0, 'movimiento' => 2, 'indice_sueno' => 85],
            ['temperatura' => 19.0, 'humedad' => 69.0, 'ruido' => 5.0, 'movimiento' => 1, 'indice_sueno' => 88],
            ['temperatura' => 19.0, 'humedad' => 69.0, 'ruido' => 4.0, 'movimiento' => 0, 'indice_sueno' => 90],
            ['temperatura' => 19.0, 'humedad' => 69.0, 'ruido' => 4.0, 'movimiento' => 0, 'indice_sueno' => 90],
            ['temperatura' => 19.0, 'humedad' => 69.0, 'ruido' => 5.0, 'movimiento' => 2, 'indice_sueno' => 85],
            ['temperatura' => 19.0, 'humedad' => 69.0, 'ruido' => 5.0, 'movimiento' => 1, 'indice_sueno' => 88],
            ['temperatura' => 19.0, 'humedad' => 70.0, 'ruido' => 4.0, 'movimiento' => 0, 'indice_sueno' => 90],
            ['temperatura' => 19.0, 'humedad' => 70.0, 'ruido' => 4.0, 'movimiento' => 0, 'indice_sueno' => 90],
            ['temperatura' => 19.0, 'humedad' => 70.0, 'ruido' => 4.0, 'movimiento' => 0, 'indice_sueno' => 90],
            ['temperatura' => 19.0, 'humedad' => 70.0, 'ruido' => 4.0, 'movimiento' => 0, 'indice_sueno' => 90],
            
            // Hora 7: 05:00 - 06:00 (Despertando)
            ['temperatura' => 19.5, 'humedad' => 70.0, 'ruido' => 8.0, 'movimiento' => 5, 'indice_sueno' => 75],
            ['temperatura' => 20.0, 'humedad' => 70.0, 'ruido' => 10.0, 'movimiento' => 6, 'indice_sueno' => 70],
            ['temperatura' => 20.5, 'humedad' => 70.0, 'ruido' => 15.0, 'movimiento' => 8, 'indice_sueno' => 65],
            ['temperatura' => 21.0, 'humedad' => 70.0, 'ruido' => 20.0, 'movimiento' => 10, 'indice_sueno' => 60],
            ['temperatura' => 21.5, 'humedad' => 70.0, 'ruido' => 25.0, 'movimiento' => 12, 'indice_sueno' => 55],
            ['temperatura' => 22.0, 'humedad' => 70.0, 'ruido' => 30.0, 'movimiento' => 15, 'indice_sueno' => 50],
            ['temperatura' => 22.5, 'humedad' => 70.0, 'ruido' => 35.0, 'movimiento' => 18, 'indice_sueno' => 45],
            ['temperatura' => 23.0, 'humedad' => 70.0, 'ruido' => 40.0, 'movimiento' => 20, 'indice_sueno' => 40],
            ['temperatura' => 23.5, 'humedad' => 70.0, 'ruido' => 45.0, 'movimiento' => 22, 'indice_sueno' => 35],
            ['temperatura' => 24.0, 'humedad' => 70.0, 'ruido' => 50.0, 'movimiento' => 25, 'indice_sueno' => 30],
            ['temperatura' => 24.5, 'humedad' => 70.0, 'ruido' => 55.0, 'movimiento' => 28, 'indice_sueno' => 25],
            ['temperatura' => 25.0, 'humedad' => 70.0, 'ruido' => 60.0, 'movimiento' => 30, 'indice_sueno' => 20],
        ];

        $fechaBase = new \DateTime('2026-06-22 23:00:00');
        $db = \Config\Database::connect();

        foreach ($datos as $index => $dato) {
            // Agregar minutos (5 minutos por registro)
            $fecha = clone $fechaBase;
            $fecha->modify('+' . ($index * 5) . ' minutes');

            $db->table('lecturas')->insert([
                'temperatura'   => $dato['temperatura'],
                'humedad'       => $dato['humedad'],
                'ruido'         => $dato['ruido'],
                'movimiento'    => $dato['movimiento'],
                'indice_sueno'  => $dato['indice_sueno'],
                'fecha'         => $fecha->format('Y-m-d H:i:s'),
            ]);
        }

        // Agregar algunos registros de días anteriores para el historial
        $diasAnteriores = [
            '2026-06-21' => ['temp' => 22.0, 'hum' => 55, 'ruido' => 25, 'mov' => 8, 'ics' => 75],
            '2026-06-20' => ['temp' => 21.5, 'hum' => 58, 'ruido' => 20, 'mov' => 5, 'ics' => 82],
            '2026-06-19' => ['temp' => 23.0, 'hum' => 52, 'ruido' => 30, 'mov' => 12, 'ics' => 65],
            '2026-06-18' => ['temp' => 20.5, 'hum' => 60, 'ruido' => 15, 'mov' => 3, 'ics' => 88],
            '2026-06-17' => ['temp' => 22.5, 'hum' => 50, 'ruido' => 35, 'mov' => 15, 'ics' => 55],
        ];

        foreach ($diasAnteriores as $fecha => $d) {
            $db->table('lecturas')->insert([
                'temperatura'   => $d['temp'],
                'humedad'       => $d['hum'],
                'ruido'         => $d['ruido'],
                'movimiento'    => $d['mov'],
                'indice_sueno'  => $d['ics'],
                'fecha'         => $fecha . ' 02:00:00',
            ]);
        }

        echo "✅ Datos de prueba insertados correctamente.\n";
    }
}