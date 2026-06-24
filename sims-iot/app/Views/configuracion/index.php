<?= $this->extend('layouts/header') ?>

<?= $this->section('content') ?>

<!-- ========================================== -->
<!-- CONFIGURACIÓN                              -->
<!-- ========================================== -->
<div class="section-hd">
    <h2><i class="bi bi-sliders2 me-1"></i> Configuración del sistema</h2>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="form-section">
            <div class="form-section-title">
                <i class="bi bi-thermometer-half"></i> Umbrales de alerta
            </div>

            <form id="config-form">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label-iot">Temperatura mínima</label>
                        <input type="number" step="0.5" class="form-control form-control-iot" 
                               id="temp_min" placeholder="Ej: 18">
                        <small class="text-secondary">°C - Por debajo de este valor se genera alerta</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-iot">Temperatura máxima</label>
                        <input type="number" step="0.5" class="form-control form-control-iot" 
                               id="temp_max" placeholder="Ej: 26">
                        <small class="text-secondary">°C - Por encima de este valor se genera alerta</small>
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-md-6">
                        <label class="form-label-iot">Humedad mínima</label>
                        <input type="number" step="1" class="form-control form-control-iot" 
                               id="hum_min" placeholder="Ej: 30">
                        <small class="text-secondary">% - Por debajo de este valor se genera alerta</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-iot">Humedad máxima</label>
                        <input type="number" step="1" class="form-control form-control-iot" 
                               id="hum_max" placeholder="Ej: 70">
                        <small class="text-secondary">% - Por encima de este valor se genera alerta</small>
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-md-6">
                        <label class="form-label-iot">Ruido máximo</label>
                        <input type="number" step="1" class="form-control form-control-iot" 
                               id="ruido_max" placeholder="Ej: 40">
                        <small class="text-secondary">dB - Por encima de este valor se genera alerta</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-iot">Movimiento máximo</label>
                        <input type="number" step="1" class="form-control form-control-iot" 
                               id="mov_max" placeholder="Ej: 10">
                        <small class="text-secondary">eventos - Por encima de este valor se genera alerta</small>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-iot btn-iot-primary">
                        <i class="bi bi-check-lg me-2"></i>Guardar configuración
                    </button>
                    <button type="reset" class="btn btn-iot btn-iot-ghost ms-2">
                        <i class="bi bi-arrow-counterclockwise"></i> Restablecer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="form-section">
            <div class="form-section-title">
                <i class="bi bi-info-circle"></i> Información
            </div>
            <div class="text-secondary small">
                <p><strong>Umbrales recomendados:</strong></p>
                <ul class="list-unstyled">
                    <li>🌡️ Temperatura: <span id="info-temp">18°C - 26°C</span></li>
                    <li>💧 Humedad: <span id="info-hum">30% - 70%</span></li>
                    <li>🔊 Ruido: <span id="info-ruido">&lt; 40 dB</span></li>
                    <li>🌀 Movimiento: <span id="info-mov">&lt; 10 eventos</span></li>
                    <li>⭐ ICS: <span id="info-ics">≥ 60/100</span></li>
                </ul>
                <hr class="border-secondary border-opacity-25">
                <p class="mb-0">
                    <i class="bi bi-clock me-1"></i>
                    Última actualización: <span id="config-update">--</span>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- JAVASCRIPT DE CONFIGURACIÓN                -->
<!-- ========================================== -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    'use strict';

    const API_BASE = document.querySelector('meta[name="api-base"]')?.content || '/api';

    // ==========================================
    // CARGAR CONFIGURACIÓN
    // ==========================================
    async function loadConfig() {
        try {
            const response = await fetch(`${API_BASE}/config`);
            if (!response.ok) throw new Error('Error al cargar configuración');
            
            const cfg = await response.json();
            
            // Mapear campos directamente
            document.getElementById('temp_min').value = cfg.temp_min || '';
            document.getElementById('temp_max').value = cfg.temp_max || '';
            document.getElementById('hum_min').value = cfg.hum_min || '';
            document.getElementById('hum_max').value = cfg.hum_max || '';
            document.getElementById('ruido_max').value = cfg.ruido_max || '';
            document.getElementById('mov_max').value = cfg.mov_max || '';
            
            // Actualizar información
            document.getElementById('info-temp').textContent = `${cfg.temp_min}°C - ${cfg.temp_max}°C`;
            document.getElementById('info-hum').textContent = `${cfg.hum_min}% - ${cfg.hum_max}%`;
            document.getElementById('info-ruido').textContent = `< ${cfg.ruido_max} dB`;
            document.getElementById('info-mov').textContent = `< ${cfg.mov_max} eventos`;
            
            document.getElementById('config-update').textContent = 
                new Date().toLocaleString('es-MX');

        } catch (error) {
            console.error('[loadConfig]', error);
            showFlash('❌ Error al cargar la configuración.', 'danger');
        }
    }

    // ==========================================
    // GUARDAR CONFIGURACIÓN
    // ==========================================
    document.getElementById('config-form')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const btn = this.querySelector('[type="submit"]');
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Guardando...';

        try {
            const payload = {
                temp_min: parseFloat(document.getElementById('temp_min').value) || null,
                temp_max: parseFloat(document.getElementById('temp_max').value) || null,
                hum_min: parseFloat(document.getElementById('hum_min').value) || null,
                hum_max: parseFloat(document.getElementById('hum_max').value) || null,
                ruido_max: parseFloat(document.getElementById('ruido_max').value) || null,
                mov_max: parseInt(document.getElementById('mov_max').value) || null
            };

            const response = await fetch(`${API_BASE}/config/umbrales`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            if (response.ok) {
                showFlash('✅ Configuración guardada correctamente.', 'success');
                loadConfig();
            } else {
                throw new Error('Error al guardar');
            }

        } catch (error) {
            console.error('[saveConfig]', error);
            showFlash('❌ Error al guardar la configuración.', 'danger');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
    });

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
    // INICIALIZACIÓN
    // ==========================================
    loadConfig();
    console.log('⚙️ Configuración - Inicializado');
});
</script>

<?= $this->endSection() ?>