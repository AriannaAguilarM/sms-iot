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
                               id="cfg-temp-min" placeholder="Ej: 18">
                        <small class="text-secondary">°C - Por debajo de este valor se genera alerta</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-iot">Temperatura máxima</label>
                        <input type="number" step="0.5" class="form-control form-control-iot" 
                               id="cfg-temp-max" placeholder="Ej: 26">
                        <small class="text-secondary">°C - Por encima de este valor se genera alerta</small>
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-md-6">
                        <label class="form-label-iot">Humedad mínima</label>
                        <input type="number" step="1" class="form-control form-control-iot" 
                               id="cfg-hum-min" placeholder="Ej: 30">
                        <small class="text-secondary">% - Por debajo de este valor se genera alerta</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-iot">Humedad máxima</label>
                        <input type="number" step="1" class="form-control form-control-iot" 
                               id="cfg-hum-max" placeholder="Ej: 70">
                        <small class="text-secondary">% - Por encima de este valor se genera alerta</small>
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-md-6">
                        <label class="form-label-iot">Ruido máximo</label>
                        <input type="number" step="1" class="form-control form-control-iot" 
                               id="cfg-ruido-max" placeholder="Ej: 40">
                        <small class="text-secondary">dB - Por encima de este valor se genera alerta</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-iot">ICS mínimo</label>
                        <input type="number" step="1" class="form-control form-control-iot" 
                               id="cfg-ics-min" placeholder="Ej: 60">
                        <small class="text-secondary">/100 - Por debajo de este valor se genera alerta</small>
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
                    <li>🌡️ Temperatura: 18°C - 26°C</li>
                    <li>💧 Humedad: 30% - 70%</li>
                    <li>🔊 Ruido: &lt; 40 dB</li>
                    <li>⭐ ICS: ≥ 60</li>
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
            
            document.getElementById('cfg-temp-min').value = cfg.temperatura_min || '';
            document.getElementById('cfg-temp-max').value = cfg.temperatura_max || '';
            document.getElementById('cfg-hum-min').value = cfg.humedad_min || '';
            document.getElementById('cfg-hum-max').value = cfg.humedad_max || '';
            document.getElementById('cfg-ruido-max').value = cfg.ruido_max || '';
            document.getElementById('cfg-ics-min').value = cfg.indice_sueno_min || '';
            
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
                temperatura_min: parseFloat(document.getElementById('cfg-temp-min').value) || null,
                temperatura_max: parseFloat(document.getElementById('cfg-temp-max').value) || null,
                humedad_min: parseFloat(document.getElementById('cfg-hum-min').value) || null,
                humedad_max: parseFloat(document.getElementById('cfg-hum-max').value) || null,
                ruido_max: parseFloat(document.getElementById('cfg-ruido-max').value) || null,
                indice_sueno_min: parseFloat(document.getElementById('cfg-ics-min').value) || null
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