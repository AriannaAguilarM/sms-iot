<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\UsuarioModel;

class UsuariosController extends ResourceController
{
    protected $modelName = UsuarioModel::class;
    protected $format = 'json';

    public function listar()
    {
        $usuarios = $this->model->findAll();
        return $this->respond($usuarios);
    }

    public function crear()
    {
        $data = $this->request->getJSON(true);
        
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        if ($this->model->save($data)) {
            return $this->respond(['status' => 'ok', 'mensaje' => 'Usuario creado']);
        }
        
        return $this->fail($this->model->errors());
    }

    public function editar($id = null)
    {
        $data = $this->request->getJSON(true);
        
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['password']);
        }
        
        if ($this->model->update($id, $data)) {
            return $this->respond(['status' => 'ok', 'mensaje' => 'Usuario actualizado']);
        }
        
        return $this->fail($this->model->errors());
    }

    public function eliminar($id = null)
    {
        if ($this->model->delete($id)) {
            return $this->respond(['status' => 'ok', 'mensaje' => 'Usuario eliminado']);
        }
        
        return $this->fail('Error al eliminar');
    }
}