<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * AlertaModel
 * 
 * Gestiona las alertas generadas automáticamente por el sistema
 * cuando los valores de los sensores superan los umbrales definidos.
 */
class AlertaModel extends Model
{
    protected $table            = 'alertas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'mensaje',
        'nivel',   // 'bajo' | 'medio' | 'alto'
        'fecha',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'mensaje' => 'required|min_length[5]|max_length[255]',
        'nivel'   => 'required|in_list[bajo,medio,alto]',
    ];

    // ──────────────────────────────────────────────────────────────────────
    // CONSULTAS PERSONALIZADAS
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Obtener alertas recientes ordenadas por fecha descendente.
     */
    public function getAlertasRecientes(int $limit = 20): array
    {
        return $this->orderBy('fecha', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Eliminar alertas con más de N días de antigüedad.
     */
    public function limpiarAnteriores(int $dias = 30): int
    {
        $db = \Config\Database::connect();

        $db->query("DELETE FROM alertas WHERE fecha < DATE_SUB(NOW(), INTERVAL {$dias} DAY)");

        return $db->affectedRows();
    }

    /**
     * Contar alertas por nivel en las últimas 24 horas.
     */
    public function contarPorNivel(): array
    {
        $db = \Config\Database::connect();

        $query = $db->query("
            SELECT nivel, COUNT(*) AS total
            FROM alertas
            WHERE fecha >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
            GROUP BY nivel
        ");

        return $query->getResultArray();
    }
}