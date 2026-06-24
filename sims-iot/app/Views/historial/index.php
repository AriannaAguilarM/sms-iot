<?= $this->extend('layouts/header') ?>

<?= $this->section('content') ?>

<!-- ========================================== -->
<!-- CONTROLES DE FILTRO                        -->
<!-- ========================================== -->
<div class="section-hd">
    <h2><i class="bi bi-clock-history me-1"></i> Registro completo de lecturas</h2>
    <div class="d-flex gap-2 flex-wrap">
        <button class="btn btn-iot btn-iot-primary btn-sm" id="btn-refresh">
            <i class="bi bi-arrow-clockwise"></i> Actualizar
        </button>
        <button class="btn btn-iot btn-iot-success btn-sm" id="btn-export-csv">
            <i class="bi bi-file-earmark-spreadsheet"></i> Exportar CSV
        </button>
    </div>
</div>

<!-- Filtros -->
<div class="iot-card mb-4">
    <div class="iot-card-body">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label-iot">Fecha Inicio</label>
                <input type="date" class="form-control form-control-iot" id="fecha-inicio">
            </div>
            <div class="col-md-3">
                <label class="form-label-iot">Fecha Fin</label>
                <input type="date" class="form-control form-control-iot" id="fecha-fin">
            </div>
            <div class="col-md-2">
                <label class="form-label-iot">Límite</label>
                <select class="form-select form-select-iot form-control-iot" id="sel-limit">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50" selected>50</option>
                    <option value="100">100</option>
                    <option value="500">500</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label-iot">Calidad</label>
                <select class="form-select form-select-iot form-control-iot" id="sel-calidad">
                    <option value="todos">Todos</option>
                    <option value="excelente">Excelente (≥80)</option>
                    <option value="regular">Regular (60-79)</option>
                    <option value="deficiente">Deficiente (&lt;60)</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-iot btn-iot-primary w-100" id="btn-filtrar">
                    <i class="bi bi-funnel"></i> Filtrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Contador de registros -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <span class="text-secondary" id="total-lecturas">Total: 0 registros</span>
    <span class="text-secondary small" id="historial-update">Última actualización: --</span>
</div>

<!-- Tabla -->
<div class="iot-card">
    <div class="table-responsive">
        <table class="iot-table">
            <thead>
                <tr>
                    <th>Fecha/Hora</th>
                    <th>Temperatura</th>
                    <th>Humedad</th>
                    <th>Ruido</th>
                    <th>Movimiento</th>
                    <th>ICS</th>
                    <th>Calidad</th>
                </tr>
            </thead>
            <tbody id="historial-tbody">
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                        Cargando registros...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- ========================================== -->
<!-- JAVASCRIPT DEL HISTORIAL                   -->
<!-- ========================================== -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    'use strict';

    const API_BASE = document.querySelector('meta[name="api-base"]')?.content || '/api';
    let historialData = [];

    // ==========================================
    // FUNCIONES DE UTILIDAD
    // ==========================================
    function toNumber(value) {
        const num = parseFloat(value);
        return isNaN(num) ? 0 : num;
    }

    function fmtDate(str) {
        if (!str) return '—';
        return new Date(str).toLocaleString('es-MX', {
            day: '2-digit', month: 'short', year: 'numeric',
            hour: '2-digit', minute: '2-digit'
        });
    }

    function classifyICS(val) {
        const v = toNumber(val);
        if (v >= 80) return { label: 'Excelente', badge: 'success' };
        if (v >= 60) return { label: 'Regular', badge: 'warning' };
        return { label: 'Deficiente', badge: 'danger' };
    }

    // ==========================================
    // FETCH HISTORIAL
    // ==========================================
    async function fetchHistorial() {
        try {
            const limit = document.getElementById('sel-limit')?.value || 50;
            const fi = document.getElementById('fecha-inicio')?.value || '';
            const ff = document.getElementById('fecha-fin')?.value || '';
            const calidad = document.getElementById('sel-calidad')?.value || 'todos';

            let path = `/lecturas?limit=${limit}`;
            if (fi) path += `&fecha_inicio=${fi}`;
            if (ff) path += `&fecha_fin=${ff}`;

            const response = await fetch(`${API_BASE}${path}`);
            if (!response.ok) throw new Error('Error al obtener historial');
            
            let data = await response.json();
            historialData = data;

            // Filtrar por calidad
            if (calidad !== 'todos') {
                data = data.filter(r => {
                    const q = classifyICS(r.indice_sueno);
                    return q.label.toLowerCase() === calidad;
                });
            }

            renderTable(data);
            document.getElementById('total-lecturas').textContent = `Total: ${data.length} registros`;
            document.getElementById('historial-update').textContent = 
                'Última actualización: ' + new Date().toLocaleTimeString('es-MX');

        } catch (error) {
            console.error('[fetchHistorial]', error);
            document.getElementById('historial-tbody').innerHTML = `
                <tr><td colspan="7" class="text-center py-5 text-danger">
                    <i class="bi bi-exclamation-triangle fs-2 d-block mb-2"></i>
                    Error al cargar los datos
                </td></tr>
            `;
        }
    }

    // ==========================================
    // RENDER TABLA
    // ==========================================
    function renderTable(data) {
        const tbody = document.getElementById('historial-tbody');
        if (!tbody) return;

        if (!data || data.length === 0) {
            tbody.innerHTML = `
                <tr><td colspan="7" class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                    Sin registros disponibles
                </td></tr>
            `;
            return;
        }

        tbody.innerHTML = data.map(r => {
            const q = classifyICS(r.indice_sueno);
            const mov = toNumber(r.movimiento);
            return `<tr>
                <td class="td-mono">${fmtDate(r.fecha)}</td>
                <td class="td-mono text-danger">${toNumber(r.temperatura).toFixed(1)}°C</td>
                <td class="td-mono" style="color:var(--blue)">${toNumber(r.humedad).toFixed(1)}%</td>
                <td class="td-mono text-warning">${toNumber(r.ruido).toFixed(0)} dB</td>
                <td>${mov > 0 
                    ? '<span class="badge bg-warning text-dark">⚡ Sí</span>' 
                    : '<span class="badge bg-success">✅ No</span>'}</td>
                <td class="td-mono">${toNumber(r.indice_sueno).toFixed(0)}</td>
                <td><span class="badge bg-${q.badge}">${q.label}</span></td>
            </tr>`;
        }).join('');
    }

    // ==========================================
    // EXPORTAR CSV
    // ==========================================
    function exportCSV() {
        const data = historialData;
        if (!data || data.length === 0) {
            showFlash('No hay datos para exportar.', 'warning');
            return;
        }

        const header = ['Fecha', 'Temperatura', 'Humedad', 'Ruido', 'Movimiento', 'ICS', 'Calidad'];
        const rows = data.map(r => [
            r.fecha,
            toNumber(r.temperatura).toFixed(1),
            toNumber(r.humedad).toFixed(1),
            toNumber(r.ruido).toFixed(0),
            toNumber(r.movimiento) > 0 ? 'Sí' : 'No',
            toNumber(r.indice_sueno).toFixed(0),
            classifyICS(r.indice_sueno).label
        ]);

        const csv = [header, ...rows].map(r => r.join(',')).join('\n');
        const a = document.createElement('a');
        a.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csv);
        a.download = `historial-sueno-${new Date().toISOString().slice(0,10)}.csv`;
        a.click();
        showFlash('✅ CSV exportado correctamente.', 'success');
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
    document.getElementById('btn-filtrar')?.addEventListener('click', fetchHistorial);
    document.getElementById('btn-refresh')?.addEventListener('click', fetchHistorial);
    document.getElementById('btn-export-csv')?.addEventListener('click', exportCSV);
    document.getElementById('sel-limit')?.addEventListener('change', fetchHistorial);
    document.getElementById('sel-calidad')?.addEventListener('change', fetchHistorial);

    // Enter para filtrar
    document.querySelectorAll('#fecha-inicio, #fecha-fin').forEach(el => {
        el.addEventListener('keypress', e => {
            if (e.key === 'Enter') fetchHistorial();
        });
    });

    // ==========================================
    // INICIALIZACIÓN
    // ==========================================
    // Establecer fechas por defecto (últimos 7 días)
    const hoy = new Date();
    const hace7dias = new Date();
    hace7dias.setDate(hoy.getDate() - 7);

    const fi = document.getElementById('fecha-inicio');
    const ff = document.getElementById('fecha-fin');
    if (fi) fi.value = hace7dias.toISOString().slice(0, 10);
    if (ff) ff.value = hoy.toISOString().slice(0, 10);

    fetchHistorial();
    console.log('📊 Historial - Inicializado');
});
</script>

<?= $this->endSection() ?>