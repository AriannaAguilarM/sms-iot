<?php

namespace App\Models;

use CodeIgniter\Model;

class LecturaModel extends Model
{
    protected $table            = 'lecturas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'temperatura',
        'humedad',
        'ruido',
        'movimiento',
        'indice_sueno',
        'fecha',
    ];

    // ✅ TIMESTAMPS AUTOMÁTICOS
    protected $useTimestamps = true;
    protected $createdField  = 'fecha';
    protected $updatedField  = '';

    // ─── Reglas de validación ──────────────────────────────────────────────
    protected $validationRules = [
        'temperatura'  => 'required|numeric|greater_than[-50]|less_than[80]',
        'humedad'      => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
        'ruido'        => 'required|numeric|greater_than_equal_to[0]|less_than[200]',
        'movimiento'   => 'required|integer|greater_than_equal_to[0]|less_than_equal_to[100]',  // ✅ CAMBIADO
        'indice_sueno' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
    ];

    protected $validationMessages = [
        'temperatura' => [
            'required'     => 'La temperatura es obligatoria.',
            'numeric'      => 'La temperatura debe ser un número.',
            'greater_than' => 'La temperatura mínima permitida es -50°C.',
            'less_than'    => 'La temperatura máxima permitida es 80°C.',
        ],
        'humedad' => [
            'required'              => 'La humedad es obligatoria.',
            'numeric'               => 'La humedad debe ser un número.',
            'greater_than_equal_to' => 'La humedad no puede ser negativa.',
            'less_than_equal_to'    => 'La humedad no puede superar 100%.',
        ],
        'ruido' => [
            'required'              => 'El nivel de ruido es obligatorio.',
            'numeric'               => 'El ruido debe ser un número.',
            'greater_than_equal_to' => 'El ruido no puede ser negativo.',
        ],
        'movimiento' => [
            'required'              => 'El campo movimiento es obligatorio.',
            'integer'               => 'El movimiento debe ser un número entero.',
            'greater_than_equal_to' => 'El movimiento no puede ser negativo.',
            'less_than_equal_to'    => 'El movimiento no puede superar 100.',
        ],
        'indice_sueno' => [
            'required'              => 'El índice de sueño es obligatorio.',
            'numeric'               => 'El índice de sueño debe ser un número.',
            'greater_than_equal_to' => 'El índice de sueño mínimo es 0.',
            'less_than_equal_to'    => 'El índice de sueño máximo es 100.',
        ],
    ];

    // ─── CONSULTAS PERSONALIZADAS ──────────────────────────────────────────

    public function getPromediosGlobales(): array
    {
        return $this->select('
                ROUND(AVG(temperatura), 2)  AS temp_promedio,
                ROUND(AVG(humedad), 2)      AS humedad_promedio,
                ROUND(AVG(ruido), 2)        AS ruido_promedio,
                ROUND(AVG(indice_sueno), 2) AS indice_promedio,
                ROUND(MAX(temperatura), 2)  AS temp_max,
                ROUND(MIN(temperatura), 2)  AS temp_min,
                ROUND(MAX(ruido), 2)        AS ruido_max,
                ROUND(MIN(ruido), 2)        AS ruido_min,
                COUNT(*)                    AS total_lecturas
            ')
            ->first();
    }

    public function getEstadisticasPorDia(int $dias = 7): array
    {
        $db = \Config\Database::connect();

        $query = $db->query("
            SELECT
                DATE(fecha)                     AS dia,
                ROUND(AVG(temperatura), 2)      AS temp_promedio,
                ROUND(AVG(humedad), 2)          AS humedad_promedio,
                ROUND(AVG(ruido), 2)            AS ruido_promedio,
                ROUND(AVG(indice_sueno), 2)     AS indice_promedio,
                SUM(movimiento)                 AS total_movimientos,
                COUNT(*)                        AS total_lecturas
            FROM lecturas
            WHERE fecha >= DATE_SUB(NOW(), INTERVAL {$dias} DAY)
            GROUP BY DATE(fecha)
            ORDER BY dia DESC
        ");

        return $query->getResultArray();
    }

    public function getUltimaLectura(): ?array
    {
        return $this->orderBy('fecha', 'DESC')->first();
    }

    public function getLecturasFiltradas(?string $fechaInicio, ?string $fechaFin, int $limit = 100): array
    {
        $builder = $this->orderBy('fecha', 'DESC');

        if ($fechaInicio) {
            $builder->where('fecha >=', $fechaInicio . ' 00:00:00');
        }
        if ($fechaFin) {
            $builder->where('fecha <=', $fechaFin . ' 23:59:59');
        }

        return $builder->limit($limit)->findAll();
    }
}