<?php

namespace App\Models;

use CodeIgniter\Model;

class ConfiguracionModel extends Model
{
    protected $table            = 'configuracion';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['clave', 'valor', 'descripcion'];

    /**
     * Obtener todos los umbrales
     */
    public function getUmbrales()
    {
        $defaults = [
            'temperatura_min' => 18,
            'temperatura_max' => 26,
            'humedad_min' => 30,
            'humedad_max' => 70,
            'ruido_max' => 40,
            'indice_sueno_min' => 60,
        ];

        $result = $this->findAll();
        $umbrales = [];

        foreach ($result as $row) {
            $umbrales[$row['clave']] = (float)$row['valor'];
        }

        // Asegurar que todos los valores existan
        foreach ($defaults as $key => $value) {
            if (!isset($umbrales[$key])) {
                $umbrales[$key] = $value;
            }
        }

        return $umbrales;
    }

    /**
     * Guardar umbrales
     */
    public function guardarUmbrales($data)
    {
        foreach ($data as $clave => $valor) {
            $existing = $this->where('clave', $clave)->first();
            
            if ($existing) {
                $this->update($existing['id'], ['valor' => (string)$valor]);
            } else {
                $this->insert([
                    'clave' => $clave,
                    'valor' => (string)$valor,
                    'descripcion' => $this->getDescripcion($clave)
                ]);
            }
        }
        return true;
    }

    private function getDescripcion($clave)
    {
        $descriptions = [
            'temperatura_min' => 'Temperatura mínima recomendada',
            'temperatura_max' => 'Temperatura máxima recomendada',
            'humedad_min' => 'Humedad mínima recomendada',
            'humedad_max' => 'Humedad máxima recomendada',
            'ruido_max' => 'Ruido máximo permitido',
            'indice_sueno_min' => 'ICS mínimo aceptable',
        ];
        return $descriptions[$clave] ?? $clave;
    }

    /**
     * Inicializar configuración por defecto
     */
    public function inicializar()
    {
        $defaults = [
            ['clave' => 'temperatura_min', 'valor' => '18', 'descripcion' => 'Temperatura mínima recomendada'],
            ['clave' => 'temperatura_max', 'valor' => '26', 'descripcion' => 'Temperatura máxima recomendada'],
            ['clave' => 'humedad_min', 'valor' => '30', 'descripcion' => 'Humedad mínima recomendada'],
            ['clave' => 'humedad_max', 'valor' => '70', 'descripcion' => 'Humedad máxima recomendada'],
            ['clave' => 'ruido_max', 'valor' => '40', 'descripcion' => 'Ruido máximo permitido'],
            ['clave' => 'indice_sueno_min', 'valor' => '60', 'descripcion' => 'ICS mínimo aceptable'],
        ];

        foreach ($defaults as $item) {
            $exists = $this->where('clave', $item['clave'])->first();
            if (!$exists) {
                $this->insert($item);
            }
        }
    }
}