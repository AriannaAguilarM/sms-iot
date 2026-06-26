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
        try {
            $usuarios = $this->model->findAll();
            
            $data = array_map(function($u) {
                return [
                    'id'         => $u['id'],
                    'nombre'     => $u['nombre'],
                    'email'      => $u['correo'],
                    'created_at' => date('Y-m-d H:i:s')
                ];
            }, $usuarios);
            
            return $this->respond($data);
        } catch (\Exception $e) {
            return $this->fail('Error al listar usuarios: ' . $e->getMessage(), 500);
        }
    }

    public function crear()
    {
        try {
            $data = $this->request->getJSON(true);
            
            if (empty($data['nombre']) || empty($data['correo']) || empty($data['password'])) {
                return $this->fail('Nombre, correo y contraseña son obligatorios', 400);
            }
            
            // ✅ Verificar si el correo ya existe
            if (!$this->model->validarCorreoUnico($data['correo'])) {
                return $this->fail('Este correo ya está registrado.', 400);
            }
            
            if (!$this->model->insert($data)) {
                return $this->fail($this->model->errors(), 400);
            }
            
            return $this->respond([
                'status' => 'ok',
                'mensaje' => 'Usuario creado correctamente',
                'id' => $this->model->getInsertID()
            ]);
        } catch (\Exception $e) {
            return $this->fail('Error al crear usuario: ' . $e->getMessage(), 500);
        }
    }

    public function editar($id = null)
    {
        try {
            if (!$id) {
                return $this->fail('ID de usuario requerido', 400);
            }
            
            $data = $this->request->getJSON(true);
            
            // Verificar que el usuario existe
            $usuario = $this->model->find($id);
            if (!$usuario) {
                return $this->fail('Usuario no encontrado', 404);
            }
            
            // ✅ Verificar si el correo ya existe (excluyendo el usuario actual)
            if (isset($data['correo']) && !empty($data['correo'])) {
                // Si el correo no cambió, no validar
                if ($data['correo'] !== $usuario['correo']) {
                    if (!$this->model->validarCorreoUnico($data['correo'], $id)) {
                        return $this->fail('Este correo ya está registrado por otro usuario.', 400);
                    }
                }
            }
            
            // Si no se envía contraseña, eliminarla del array
            if (empty($data['password'])) {
                unset($data['password']);
            }
            
            if (!$this->model->update($id, $data)) {
                return $this->fail($this->model->errors(), 400);
            }
            
            return $this->respond([
                'status' => 'ok',
                'mensaje' => 'Usuario actualizado correctamente'
            ]);
        } catch (\Exception $e) {
            return $this->fail('Error al editar usuario: ' . $e->getMessage(), 500);
        }
    }

    public function eliminar($id = null)
    {
        try {
            if (!$id) {
                return $this->fail('ID de usuario requerido', 400);
            }
            
            $usuario = $this->model->find($id);
            if (!$usuario) {
                return $this->fail('Usuario no encontrado', 404);
            }
            
            if ($id == 1) {
                return $this->fail('No se puede eliminar al administrador principal', 400);
            }
            
            if ($this->model->delete($id)) {
                return $this->respond([
                    'status' => 'ok',
                    'mensaje' => 'Usuario eliminado correctamente'
                ]);
            }
            
            return $this->fail('Error al eliminar el usuario', 400);
        } catch (\Exception $e) {
            return $this->fail('Error al eliminar usuario: ' . $e->getMessage(), 500);
        }
    }
}   