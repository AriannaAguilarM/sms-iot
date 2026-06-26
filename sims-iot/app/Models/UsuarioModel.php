<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table            = 'usuarios';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    
    protected $allowedFields    = [
        'nombre',
        'correo',
        'password'
    ];

    protected $useTimestamps = false;

    // ✅ REGLAS DE VALIDACIÓN CORREGIDAS
    protected $validationRules = [
        'nombre'   => 'required|min_length[3]|max_length[100]',
        'correo'   => 'required|valid_email',
        'password' => 'permit_empty|min_length[6]',
    ];

    // ✅ VALIDACIÓN PERSONALIZADA PARA CORREO ÚNICO
    protected $validationMessages = [
        'nombre' => [
            'required'    => 'El nombre es obligatorio.',
            'min_length'  => 'El nombre debe tener al menos 3 caracteres.',
            'max_length'  => 'El nombre no puede tener más de 100 caracteres.',
        ],
        'correo' => [
            'required'    => 'El correo electrónico es obligatorio.',
            'valid_email' => 'Debes ingresar un correo electrónico válido.',
        ],
        'password' => [
            'min_length' => 'La contraseña debe tener al menos 6 caracteres.',
        ],
    ];

    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password']) && !empty($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['data']['password']);
        }
        return $data;
    }

    // ✅ VALIDACIÓN PERSONALIZADA PARA CORREO ÚNICO
    public function validarCorreoUnico($correo, $id = null)
    {
        $builder = $this->builder();
        $builder->where('correo', $correo);
        
        if ($id) {
            $builder->where('id !=', $id);
        }
        
        return $builder->countAllResults() === 0;
    }
}