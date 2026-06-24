<?= $this->extend('layouts/header') ?>

<?= $this->section('content') ?>

<!-- ========================================== -->
<!-- CONTROLES                                  -->
<!-- ========================================== -->
<div class="section-hd">
    <h2><i class="bi bi-bell-fill me-1"></i> Alertas y notificaciones</h2>
    <div class="d-flex gap-2 flex-wrap">
        <select class="form-select form-select-iot form-control-iot" id="sel-nivel" style="width:auto;">
            <option value="todos">Todos los niveles</option>
            <option value="alto">🔴 Alto</option>
            <option value="medio">🟡 Medio</option>
            <option value="bajo">🔵 Bajo</option>
        </select>
        <button class="btn btn-iot btn-iot-primary btn-sm" id="btn-refresh-alerts">
            <i class="bi bi-arrow-clockwise"></i> Actualizar
        </button>
        <button class="btn btn-iot btn-iot-danger btn-sm" id="btn-limpiar-alertas">
            <i class="bi bi-trash"></i> Limpiar antiguas
        </button>
    </div>
</div>

<!-- ========================================== -->
<!-- CONTADORES DE ALERTAS                      -->
<!-- ========================================== -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="alert-counter ac-alto">
            <div class="ac-num" id="cnt-alto">0</div>
            <div class="ac-label">🔴 Alertas Altas</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="alert-counter ac-medio">
            <div class="ac-num" id="cnt-medio">0</div>
            <div class="ac-label">🟡 Alertas Medias</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="alert-counter ac-bajo">
            <div class="ac-num" id="cnt-bajo">0</div>
            <div class="ac-label">🔵 Alertas Bajas</div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- LISTA DE ALERTAS                           -->
<!-- ========================================== -->
<div class="iot-card">
    <div class="iot-card-head">
        <span class="iot-card-title"><i class="bi bi-list me-1"></i> Historial de alertas</span>
        <span class="text-secondary small" id="alertas-update">Última actualización: --</span>
    </div>
    <div class="iot-card-body">
        <div id="alertas-list">
            <div class="text-center py-5 text-muted">
                <i class="bi bi-check-circle fs-2 d-block mb-2" style="color:var(--green);"></i>
                <strong>Cargando alertas...</strong>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- JAVASCRIPT DE ALERTAS                      -->
<!-- ========================================== -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    'use strict';

    const API_BASE = document.querySelector('meta[name="api-base"]')?.content || '/api';
    let alertasData = [];

    // ==========================================
    // FUNCIONES DE UTILIDAD
    // ==========================================
    function fmtDate(str) {
        if (!str) return '—';
        return new Date(str).toLocaleString('es-MX', {
            day: '2-digit', month: 'short', year: 'numeric',
            hour: '2-digit', minute: '2-digit'
        });
    }

    const icons = { alto: '🚨', medio: '⚠️', bajo: 'ℹ️' };
    const colors = { alto: 'danger', medio: 'warning text-dark', bajo: 'info' };

    // ==========================================
    // FETCH ALERTAS
    // ==========================================
    async function fetchAlertas() {
        try {
            const [alertas, resumen] = await Promise.all([
                fetch(`${API_BASE}/alertas?limit=100`).then(r => r.json()),
                fetch(`${API_BASE}/alertas/resumen`).then(r => r.json())
            ]);

            alertasData = alertas;

            // Contadores
            document.getElementById('cnt-alto').textContent = resumen.alto || 0;
            document.getElementById('cnt-medio').textContent = resumen.medio || 0;
            document.getElementById('cnt-bajo').textContent = resumen.bajo || 0;

            document.getElementById('alertas-update').textContent = 
                'Última actualización: ' + new Date().toLocaleTimeString('es-MX');

            // Renderizar lista
            const filtro = document.getElementById('sel-nivel')?.value || 'todos';
            renderAlertas(alertas, filtro);

        } catch (error) {
            console.error('[fetchAlertas]', error);
            document.getElementById('alertas-list').innerHTML = `
                <div class="text-center py-5 text-danger">
                    <i class="bi bi-exclamation-triangle fs-2 d-block mb-2"></i>
                    Error al cargar las alertas
                </div>
            `;
        }
    }

    // ==========================================
    // RENDER ALERTAS
    // ==========================================
    function renderAlertas(data, filtro = 'todos') {
        const el = document.getElementById('alertas-list');
        if (!el) return;

        const filtered = filtro === 'todos' 
            ? data 
            : data.filter(a => a.nivel === filtro);

        if (!filtered || filtered.length === 0) {
            el.innerHTML = `
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-check-circle fs-2 d-block mb-2" style="color:var(--green);"></i>
                    <strong>Sin alertas en este nivel</strong>
                </div>
            `;
            return;
        }

        el.innerHTML = filtered.map(a => `
            <div class="alert-item nivel-${a.nivel}">
                <span class="ai-icon">${icons[a.nivel] || '📢'}</span>
                <div class="ai-body">
                    <p class="ai-msg">${a.mensaje}</p>
                    <span class="ai-meta">${fmtDate(a.fecha)}</span>
                </div>
                <span class="badge bg-${colors[a.nivel]} ai-badge">
                    ${a.nivel.toUpperCase()}
                </span>
            </div>
        `).join('');
    }

    // ==========================================
    // LIMPIAR ALERTAS ANTIGUAS
    // ==========================================
    async function limpiarAlertas() {
        if (!confirm('¿Eliminar alertas antiguas (>30 días)?')) return;

        try {
            const response = await fetch(`${API_BASE}/alertas/limpiar`, { 
                method: 'DELETE' 
            });
            
            if (response.ok) {
                showFlash('✅ Alertas antiguas eliminadas.', 'success');
                fetchAlertas();
            } else {
                throw new Error('Error al limpiar');
            }
        } catch (error) {
            console.error('[limpiarAlertas]', error);
            showFlash('❌ Error al limpiar las alertas.', 'danger');
        }
    }

    // ==========================================
    // FLASH MESSAGES
    // ==========================================
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
    // EVENTOS
    // ==========================================
    document.getElementById('sel-nivel')?.addEventListener('change', function() {
        renderAlertas(alertasData, this.value);
    });

    document.getElementById('btn-refresh-alerts')?.addEventListener('click', fetchAlertas);
    document.getElementById('btn-limpiar-alertas')?.addEventListener('click', limpiarAlertas);

    // ==========================================
    // INICIALIZACIÓN
    // ==========================================
    fetchAlertas();
    setInterval(fetchAlertas, 30000); // Actualizar cada 30 segundos

    console.log('🔔 Alertas - Inicializado');
});
</script>

<?= $this->endSection() ?>