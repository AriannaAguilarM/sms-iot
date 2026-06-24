<?php

namespace App\Models;

use CodeIgniter\Model;

class ConfiguracionModel extends Model
{
    protected $table            = 'configuraciones';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'temp_min', 'temp_max', 'hum_min', 'hum_max', 
        'ruido_max', 'mov_max'
    ];

    protected $useTimestamps = false;

    /**
     * Obtener todos los umbrales (siempre debe haber un registro)
     */
    public function getUmbrales()
    {
        $config = $this->first();
        
        if (!$config) {
            // Si no existe, crear configuración por defecto
            $defaults = [
                'temp_min' => 18,
                'temp_max' => 26,
                'hum_min' => 30,
                'hum_max' => 70,
                'ruido_max' => 40,
                'mov_max' => 10,
            ];
            $this->insert($defaults);
            return $defaults;
        }
        
        return $config;
    }

    /**
     * Guardar umbrales (siempre actualizar el registro #1)
     */
    public function guardarUmbrales($data)
    {
        $config = $this->first();
        
        if ($config) {
            return $this->update($config['id'], $data);
        } else {
            return $this->insert($data);
        }
    }

    /**
     * Inicializar configuración por defecto
     */
    public function inicializar()
    {
        $config = $this->first();
        
        if (!$config) {
            $defaults = [
                'temp_min' => 18,
                'temp_max' => 26,
                'hum_min' => 30,
                'hum_max' => 70,
                'ruido_max' => 40,
                'mov_max' => 10,
            ];
            $this->insert($defaults);
            return true;
        }
        return false;
    }
}