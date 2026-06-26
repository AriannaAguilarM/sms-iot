<?= $this->extend('layouts/header') ?>

<?= $this->section('content') ?>

<!-- ========================================== -->
<!-- GESTIÓN DE USUARIOS                        -->
<!-- ========================================== -->
<div class="section-hd">
    <h2><i class="bi bi-people-fill me-1"></i> Gestión de Usuarios</h2>
    <button class="btn btn-iot btn-iot-primary btn-sm" id="btn-nuevo-usuario">
        <i class="bi bi-person-plus"></i> Nuevo Usuario
    </button>
</div>

<!-- ========================================== -->
<!-- TABLA DE USUARIOS                          -->
<!-- ========================================== -->
<div class="iot-card">
    <div class="table-responsive">
        <table class="iot-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Fecha de registro</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="usuarios-tbody">
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                        Cargando usuarios...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL: NUEVO/EDITAR USUARIO               -->
<!-- ========================================== -->
<div class="modal fade" id="modalUsuario" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white" id="modalUsuarioTitle">
                    <i class="bi bi-person-plus me-2"></i>Nuevo Usuario
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="form-usuario">
                    <input type="hidden" id="usuario-id">
                    
                    <div class="mb-3">
                        <label class="form-label-iot">Nombre completo</label>
                        <input type="text" class="form-control form-control-iot" id="usuario-nombre" placeholder="Ej: Juan Pérez" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label-iot">Correo electrónico</label>
                        <input type="email" class="form-control form-control-iot" id="usuario-email" placeholder="ejemplo@correo.com" required>
                    </div>
                    
                    <div class="mb-3" id="password-field">
                        <label class="form-label-iot">Contraseña</label>
                        <input type="password" class="form-control form-control-iot" id="usuario-password" placeholder="Mínimo 6 caracteres">
                        <small class="text-secondary">Mínimo 6 caracteres</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-iot btn-iot-ghost" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-iot btn-iot-primary" id="btn-guardar-usuario">
                    <i class="bi bi-check-lg me-2"></i>Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- JAVASCRIPT DE USUARIOS                     -->
<!-- ========================================== -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    'use strict';

    const API_BASE = document.querySelector('meta[name="api-base"]')?.content || '/api';
    let modalUsuario = null;

    // ==========================================
    // FUNCIONES DE UTILIDAD
    // ==========================================
    function fmtDate(str) {
        if (!str) return '—';
        return new Date(str).toLocaleDateString('es-MX', {
            day: '2-digit', month: 'short', year: 'numeric'
        });
    }

    function showFlash(msg, type = 'info') {
        let c = document.getElementById('flash-messages');
        if (!c) {
            c = document.createElement('div');
            c.id = 'flash-messages';
            document.body.appendChild(c);
        }
        const a = document.createElement('div');
        a.className = `alert alert-${type} alert-dismissible fade show shadow`;
        a.innerHTML = `${msg}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
        c.appendChild(a);
        setTimeout(() => {
            try {
                const alert = bootstrap.Alert.getOrCreateInstance(a);
                alert.close();
            } catch { a.remove(); }
        }, 4000);
    }

    // ==========================================
    // FETCH USUARIOS
    // ==========================================
    async function fetchUsuarios() {
        try {
            const response = await fetch('/api/usuarios/listar');
            if (!response.ok) throw new Error('Error al obtener usuarios');
            
            const data = await response.json();
            renderUsuarios(data);

        } catch (error) {
            console.error('[fetchUsuarios]', error);
            showFlash('❌ Error al cargar los usuarios.', 'danger');
            document.getElementById('usuarios-tbody').innerHTML = `
                <tr><td colspan="5" class="text-center py-5 text-danger">
                    <i class="bi bi-exclamation-triangle fs-2 d-block mb-2"></i>
                    Error al cargar usuarios
                </td></tr>
            `;
        }
    }

    // ==========================================
    // RENDER USUARIOS
    // ==========================================
    function renderUsuarios(data) {
        const tbody = document.getElementById('usuarios-tbody');
        if (!tbody) return;

        if (!data || data.length === 0) {
            tbody.innerHTML = `
                <tr><td colspan="5" class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                    No hay usuarios registrados
                </td></tr>
            `;
            return;
        }

        tbody.innerHTML = data.map(u => `
            <tr>
                <td>#${u.id}</td>
                <td><strong>${u.nombre}</strong></td>
                <td>${u.email}</td>
                <td class="text-secondary small">${fmtDate(u.created_at)}</td>
                <td>
                    <div class="d-flex gap-1">
                        <button class="table-action-btn btn-editar" 
                                data-id="${u.id}" 
                                data-nombre="${u.nombre}" 
                                data-email="${u.email}">
                            <i class="bi bi-pencil"></i> Editar
                        </button>
                        <button class="table-action-btn danger btn-eliminar" 
                                data-id="${u.id}" 
                                data-nombre="${u.nombre}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');

        // Eventos de editar
        document.querySelectorAll('.btn-editar').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('usuario-id').value = this.dataset.id;
                document.getElementById('usuario-nombre').value = this.dataset.nombre;
                document.getElementById('usuario-email').value = this.dataset.email;
                document.getElementById('usuario-password').value = '';
                document.getElementById('usuario-password').placeholder = 'Dejar en blanco para no cambiar';
                document.getElementById('password-field').querySelector('small').textContent = 'Dejar en blanco para no cambiar';
                document.getElementById('modalUsuarioTitle').innerHTML = '<i class="bi bi-pencil me-2"></i>Editar Usuario';
                modalUsuario.show();
            });
        });

        // Eventos de eliminar
        document.querySelectorAll('.btn-eliminar').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const nombre = this.dataset.nombre;
                if (confirm(`¿Eliminar al usuario "${nombre}"?`)) {
                    eliminarUsuario(id);
                }
            });
        });
    }

    // ==========================================
// GUARDAR USUARIO (CORREGIDO)
// ==========================================
async function guardarUsuario() {
    const id = document.getElementById('usuario-id').value;
    const nombre = document.getElementById('usuario-nombre').value;
    const email = document.getElementById('usuario-email').value;
    const password = document.getElementById('usuario-password').value;

    if (!nombre || !email) {
        showFlash('❌ Nombre y correo son obligatorios.', 'danger');
        return;
    }

    const btn = document.getElementById('btn-guardar-usuario');
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Guardando...';

    try {
        const payload = { 
            nombre: nombre.trim(),
            correo: email.trim()
        };
        
        // Solo enviar contraseña si se proporcionó
        if (password && password.length > 0) {
            payload.password = password;
        }

        let url, method;
        if (id) {
            url = `/api/usuarios/editar/${id}`;
            method = 'PUT';
        } else {
            url = '/api/usuarios/crear';
            method = 'POST';
        }

        // ✅ CORRECCIÓN: Enviar explícitamente el método y headers
        const response = await fetch(url, {
            method: method,
            headers: { 
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(payload)
        });

        const result = await response.json();

        if (response.ok) {
            showFlash(id ? '✅ Usuario actualizado correctamente.' : '✅ Usuario creado correctamente.', 'success');
            modalUsuario.hide();
            fetchUsuarios();
        } else {
            // Mostrar mensaje de error del servidor
            let errorMsg = 'Error al guardar el usuario.';
            if (result.messages) {
                if (typeof result.messages === 'string') {
                    errorMsg = result.messages;
                } else if (typeof result.messages === 'object') {
                    errorMsg = Object.values(result.messages).flat().join(', ');
                }
            } else if (result.error) {
                errorMsg = result.error;
            }
            showFlash(`❌ ${errorMsg}`, 'danger');
        }

    } catch (error) {
        console.error('[guardarUsuario]', error);
        showFlash('❌ Error al guardar el usuario. Verifica la conexión.', 'danger');
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
    }
}

    // ==========================================
    // ELIMINAR USUARIO
    // ==========================================
    async function eliminarUsuario(id) {
        try {
            const response = await fetch(`/api/usuarios/eliminar/${id}`, {
                method: 'DELETE'
            });

            const result = await response.json();

            if (response.ok) {
                showFlash('✅ Usuario eliminado correctamente.', 'success');
                fetchUsuarios();
            } else {
                showFlash(`❌ ${result.mensaje || 'Error al eliminar el usuario.'}`, 'danger');
            }

        } catch (error) {
            console.error('[eliminarUsuario]', error);
            showFlash('❌ Error al eliminar el usuario.', 'danger');
        }
    }

    // ==========================================
    // EVENTOS
    // ==========================================
    const modalElement = document.getElementById('modalUsuario');
    if (modalElement) {
        modalUsuario = new bootstrap.Modal(modalElement);
    }

    document.getElementById('btn-nuevo-usuario')?.addEventListener('click', function() {
        document.getElementById('usuario-id').value = '';
        document.getElementById('usuario-nombre').value = '';
        document.getElementById('usuario-email').value = '';
        document.getElementById('usuario-password').value = '';
        document.getElementById('usuario-password').placeholder = 'Mínimo 6 caracteres';
        document.getElementById('password-field').querySelector('small').textContent = 'Mínimo 6 caracteres';
        document.getElementById('modalUsuarioTitle').innerHTML = '<i class="bi bi-person-plus me-2"></i>Nuevo Usuario';
        modalUsuario.show();
    });

    document.getElementById('btn-guardar-usuario')?.addEventListener('click', guardarUsuario);

    document.getElementById('form-usuario')?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            guardarUsuario();
        }
    });

    // ==========================================
    // INICIALIZACIÓN
    // ==========================================
    fetchUsuarios();
    console.log('👥 Usuarios - Inicializado');
});
</script>

<?= $this->endSection() ?>