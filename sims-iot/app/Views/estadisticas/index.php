<?= $this->extend('layouts/header') ?>

<?= $this->section('content') ?>

<!-- ========================================== -->
<!-- CONTROLES                                  -->
<!-- ========================================== -->
<div class="section-hd">
    <h2><i class="bi bi-bar-chart-fill me-1"></i> Análisis detallado del sueño</h2>
    <div class="d-flex gap-2 flex-wrap">
        <select class="form-select form-select-iot form-control-iot" id="sel-dias" style="width:auto;">
            <option value="7">7 días</option>
            <option value="14">14 días</option>
            <option value="30" selected>30 días</option>
            <option value="90">90 días</option>
        </select>
        <button class="btn btn-iot btn-iot-primary btn-sm" id="btn-refresh-stats">
            <i class="bi bi-arrow-clockwise"></i> Actualizar
        </button>
    </div>
</div>

<!-- ========================================== -->
<!-- STATS BOXES                                -->
<!-- ========================================== -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="stat-box">
            <div class="stat-val" id="stat-temp">--°C</div>
            <div class="stat-lbl">Temperatura promedio</div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-box">
            <div class="stat-val" id="stat-hum">--%</div>
            <div class="stat-lbl">Humedad promedio</div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-box">
            <div class="stat-val" id="stat-ruido">-- dB</div>
            <div class="stat-lbl">Ruido promedio</div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-box">
            <div class="stat-val" id="stat-ics">--</div>
            <div class="stat-lbl">ICS promedio</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="stat-box" style="border-color:var(--red);">
            <div class="stat-val" style="color:var(--red);" id="stat-tmax">--°C</div>
            <div class="stat-lbl">Temperatura máxima</div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-box" style="border-color:var(--yellow);">
            <div class="stat-val" style="color:var(--yellow);" id="stat-rmax">-- dB</div>
            <div class="stat-lbl">Ruido máximo</div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-box" style="border-color:var(--green);">
            <div class="stat-val" style="color:var(--green);" id="stat-total">--</div>
            <div class="stat-lbl">Total de lecturas</div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-box" style="border-color:var(--accent);">
            <div class="stat-val" style="color:var(--accent-l);" id="stat-mov">--</div>
            <div class="stat-lbl">Eventos de movimiento</div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- GRÁFICA SEMANAL                            -->
<!-- ========================================== -->
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="iot-card">
            <div class="iot-card-head">
                <span class="iot-card-title"><i class="bi bi-calendar-week me-1"></i> Evolución semanal</span>
            </div>
            <div class="iot-card-body">
                <div id="chart-weekly" style="width:100%; height:300px;"></div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- RESUMEN DEL DÍA DE HOY                     -->
<!-- ========================================== -->
<div class="section-hd mt-3">
    <h2><i class="bi bi-calendar-day me-1"></i> Resumen de hoy</h2>
</div>
<div class="row g-3">
    <div class="col-md-3 col-sm-6">
        <div class="stat-box">
            <div class="stat-val" id="hoy-ics">--</div>
            <div class="stat-lbl">ICS promedio hoy</div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-box" style="border-color:var(--green);">
            <div class="stat-val" style="color:var(--green);" id="hoy-total">--</div>
            <div class="stat-lbl">Lecturas hoy</div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-box" style="border-color:var(--yellow);">
            <div class="stat-val" style="color:var(--yellow);" id="hoy-movs">--</div>
            <div class="stat-lbl">Movimientos detectados</div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-box" style="border-color:var(--accent);">
            <div class="stat-val" style="color:var(--accent-l); font-size:24px;" id="hoy-calidad">--</div>
            <div class="stat-lbl">Calidad del sueño</div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- JAVASCRIPT DE ESTADÍSTICAS                 -->
<!-- ========================================== -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    'use strict';

    const API_BASE = document.querySelector('meta[name="api-base"]')?.content || '/api';
    let weeklyChart = null;

    // ==========================================
    // FUNCIONES DE UTILIDAD
    // ==========================================
    function toNumber(value) {
        const num = parseFloat(value);
        return isNaN(num) ? 0 : num;
    }

    function classifyICS(val) {
        const v = toNumber(val);
        if (v >= 80) return 'Excelente';
        if (v >= 60) return 'Regular';
        return 'Deficiente';
    }

    function getColorForICS(val) {
        const v = toNumber(val);
        if (v >= 80) return '#10b981';
        if (v >= 60) return '#f59e0b';
        return '#ef4444';
    }

    // ==========================================
    // FETCH ESTADÍSTICAS
    // ==========================================
    async function fetchEstadisticas() {
        try {
            const dias = document.getElementById('sel-dias')?.value || 30;

            const [prom, semana, hoy] = await Promise.all([
                fetch(`${API_BASE}/promedios`).then(r => r.json()),
                fetch(`${API_BASE}/estadisticas/semana?dias=${dias}`).then(r => r.json()),
                fetch(`${API_BASE}/estadisticas/hoy`).then(r => r.json())
            ]);

            // Stats boxes
            document.getElementById('stat-temp').textContent = toNumber(prom.temp_promedio).toFixed(1) + '°C';
            document.getElementById('stat-hum').textContent = toNumber(prom.humedad_promedio).toFixed(1) + '%';
            document.getElementById('stat-ruido').textContent = toNumber(prom.ruido_promedio).toFixed(0) + ' dB';
            document.getElementById('stat-ics').textContent = toNumber(prom.indice_promedio).toFixed(0);
            document.getElementById('stat-tmax').textContent = toNumber(prom.temp_max).toFixed(1) + '°C';
            document.getElementById('stat-rmax').textContent = toNumber(prom.ruido_max).toFixed(0) + ' dB';
            document.getElementById('stat-total').textContent = prom.total_lecturas || 0;
            document.getElementById('stat-mov').textContent = prom.total_movimientos || 0;

            // Hoy
            document.getElementById('hoy-ics').textContent = toNumber(hoy.indice_promedio).toFixed(0);
            document.getElementById('hoy-total').textContent = hoy.total_lecturas || 0;
            document.getElementById('hoy-movs').textContent = hoy.total_movimientos || 0;
            document.getElementById('hoy-calidad').textContent = classifyICS(hoy.indice_promedio);
            document.getElementById('hoy-calidad').style.color = getColorForICS(hoy.indice_promedio);

            // Gráfica semanal
            updateWeeklyChart(semana);

        } catch (error) {
            console.error('[fetchEstadisticas]', error);
        }
    }

    // ==========================================
    // GRÁFICA SEMANAL
    // ==========================================
    function initWeeklyChart() {
        const el = document.getElementById('chart-weekly');
        if (!el) return;

        weeklyChart = echarts.init(el);
        weeklyChart.setOption({
            backgroundColor: 'transparent',
            legend: {
                top: 0,
                textStyle: { color: '#94a3b8', fontSize: 11 },
                itemWidth: 12,
                itemHeight: 8
            },
            grid: { left: '3%', right: '3%', top: 40, bottom: 30, containLabel: true },
            tooltip: {
                trigger: 'axis',
                backgroundColor: '#1a2236',
                borderColor: '#2a3552',
                textStyle: { color: '#e2e8f0' }
            },
            xAxis: {
                type: 'category',
                data: [],
                axisLine: { lineStyle: { color: '#2a3552' } },
                axisTick: { show: false },
                axisLabel: { color: '#94a3b8', fontSize: 11 }
            },
            yAxis: {
                type: 'value',
                splitLine: { lineStyle: { color: '#2a3552', type: 'dashed' } },
                axisLine: { show: false },
                axisTick: { show: false },
                axisLabel: { color: '#94a3b8', fontSize: 10 }
            },
            series: [
                {
                    name: 'Temp. promedio',
                    type: 'bar',
                    data: [],
                    itemStyle: { color: '#ef4444', borderRadius: [4, 4, 0, 0] }
                },
                {
                    name: 'Humedad prom.',
                    type: 'bar',
                    data: [],
                    itemStyle: { color: '#3b82f6', borderRadius: [4, 4, 0, 0] }
                },
                {
                    name: 'ICS promedio',
                    type: 'line',
                    data: [],
                    smooth: true,
                    symbol: 'circle',
                    symbolSize: 7,
                    lineStyle: { color: '#10b981', width: 2.5 },
                    itemStyle: { color: '#10b981' }
                }
            ]
        });

        window.addEventListener('resize', () => {
            if (weeklyChart) weeklyChart.resize();
        });
    }

    function updateWeeklyChart(data) {
        if (!weeklyChart || !data || data.length === 0) return;

        const labels = data.map(r => {
            const d = new Date(r.dia);
            return d.toLocaleDateString('es-MX', { weekday: 'short', day: 'numeric' });
        });

        weeklyChart.setOption({
            xAxis: { data: labels },
            series: [
                { name: 'Temp. promedio', data: data.map(r => toNumber(r.temp_promedio)) },
                { name: 'Humedad prom.', data: data.map(r => toNumber(r.humedad_promedio)) },
                { name: 'ICS promedio', data: data.map(r => toNumber(r.indice_promedio)) }
            ]
        });
    }

    // ==========================================
    // INICIALIZACIÓN
    // ==========================================
    initWeeklyChart();
    fetchEstadisticas();

    document.getElementById('sel-dias')?.addEventListener('change', fetchEstadisticas);
    document.getElementById('btn-refresh-stats')?.addEventListener('click', fetchEstadisticas);

    console.log('📊 Estadísticas - Inicializado');
});
</script>

<?= $this->endSection() ?>