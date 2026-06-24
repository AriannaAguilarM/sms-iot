<?php

namespace App\Models;

use CodeIgniter\Model;

class AlertaModel extends Model
{
    protected $table            = 'alertas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['mensaje', 'nivel', 'fecha'];

    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';

    /**
     * Obtener alertas con filtros
     */
    public function getAlertas($limit = 100, $nivel = null, $desde = null, $hasta = null)
    {
        $builder = $this->builder();
        $builder->orderBy('fecha', 'DESC');
        
        if ($nivel && $nivel !== 'todos') {
            $builder->where('nivel', $nivel);
        }
        
        if ($desde) {
            $builder->where('fecha >=', $desde);
        }
        
        if ($hasta) {
            $builder->where('fecha <=', $hasta);
        }
        
        if ($limit) {
            $builder->limit($limit);
        }
        
        return $builder->get()->getResultArray();
    }

    public function getResumen()
    {
        $builder = $this->builder();
        $builder->select('nivel, COUNT(*) as total');
        $builder->where('fecha >=', date('Y-m-d H:i:s', strtotime('-30 days')));
        $builder->groupBy('nivel');
        $result = $builder->get()->getResultArray();
        
        $resumen = ['alto' => 0, 'medio' => 0, 'bajo' => 0];
        foreach ($result as $row) {
            if (isset($resumen[$row['nivel']])) {
                $resumen[$row['nivel']] = (int)$row['total'];
            }
        }
        return $resumen;
    }

    public function limpiarAntiguas()
    {
        return $this->where('fecha <', date('Y-m-d H:i:s', strtotime('-30 days')))->delete();
    }

    
    public function existeAlertaSimilar($tipo, $valor)
    {
        $builder = $this->builder();
        $builder->where('mensaje LIKE', "%{$tipo}%");
        $builder->where('fecha >=', date('Y-m-d H:i:s', strtotime('-2 hours')));
        return $builder->countAllResults() > 0;
    }
}