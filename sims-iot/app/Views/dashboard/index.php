<?= $this->extend('layouts/header') ?>

<?= $this->section('content') ?>

<!-- ========================================== -->
<!-- DASHBOARD - CARDS DE MÉTRICAS              -->
<!-- ========================================== -->

<div class="section-hd">
    <h2>Estado actual del sueño</h2>
    <span class="update-tag" id="last-update">Cargando...</span>
</div>

<div class="row g-3 mb-4">
    <!-- Temperatura -->
    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6">
        <div class="metric-card c-red">
            <div class="mc-header">
                <span class="mc-label"><i class="bi bi-thermometer-half me-1"></i> Temperatura</span>
                <span class="mc-icon i-red"><i class="bi bi-thermometer-half"></i></span>
            </div>
            <div class="mc-value">
                <span id="card-temp">--</span><span class="mc-unit">°C</span>
            </div>
            <span class="mc-badge" id="badge-temp">Cargando...</span>
        </div>
    </div>

    <!-- Humedad -->
    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6">
        <div class="metric-card c-blue">
            <div class="mc-header">
                <span class="mc-label"><i class="bi bi-droplet-half me-1"></i> Humedad</span>
                <span class="mc-icon i-blue"><i class="bi bi-droplet-half"></i></span>
            </div>
            <div class="mc-value">
                <span id="card-hum">--</span><span class="mc-unit">%</span>
            </div>
            <span class="mc-badge" id="badge-hum">Cargando...</span>
        </div>
    </div>

    <!-- Ruido -->
    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6">
        <div class="metric-card c-yellow">
            <div class="mc-header">
                <span class="mc-label"><i class="bi bi-volume-up me-1"></i> Ruido</span>
                <span class="mc-icon i-yellow"><i class="bi bi-volume-up"></i></span>
            </div>
            <div class="mc-value">
                <span id="card-ruido">--</span><span class="mc-unit">dB</span>
            </div>
            <span class="mc-badge" id="badge-ruido">Cargando...</span>
        </div>
    </div>

    <!-- Movimiento -->
    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6">
        <div class="metric-card c-green">
            <div class="mc-header">
                <span class="mc-label"><i class="bi bi-activity me-1"></i> Movimiento</span>
                <span class="mc-icon i-green"><i class="bi bi-activity"></i></span>
            </div>
            <div class="mc-value">
                <span id="card-mov">--</span>
            </div>
            <span class="mc-badge" id="badge-mov">Cargando...</span>
        </div>
    </div>

    <!-- ICS -->
    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6">
        <div class="metric-card c-purple">
            <div class="mc-header">
                <span class="mc-label"><i class="bi bi-stars me-1"></i> ICS</span>
                <span class="mc-icon i-purple"><i class="bi bi-stars"></i></span>
            </div>
            <div class="mc-value">
                <span id="card-ics">--</span>
            </div>
            <span class="ics-quality" id="ics-quality">Cargando...</span>
        </div>
    </div>

    <!-- Resumen / Calidad -->
    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6">
        <div class="metric-card c-sky" style="border-color:var(--accent);">
            <div class="mc-header">
                <span class="mc-label"><i class="bi bi-moon-fill me-1"></i> Calidad</span>
                <span class="mc-icon i-sky"><i class="bi bi-moon-fill"></i></span>
            </div>
            <div class="mc-value" style="font-size:22px;">
                <span id="card-calidad">--</span>
            </div>
            <span class="mc-badge" id="badge-calidad">Cargando...</span>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- GAUGES                                     -->
<!-- ========================================== -->
<div class="row g-3 mb-4">
    <div class="col-xl-4 col-lg-6">
        <div class="iot-card">
            <div class="iot-card-head">
                <span class="iot-card-title"><i class="bi bi-thermometer-half me-1"></i> Temperatura</span>
            </div>
            <div class="iot-card-body">
                <div id="gauge-temp" class="chart-wrap" style="height:180px;"></div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-lg-6">
        <div class="iot-card">
            <div class="iot-card-head">
                <span class="iot-card-title"><i class="bi bi-droplet-half me-1"></i> Humedad</span>
            </div>
            <div class="iot-card-body">
                <div id="gauge-hum" class="chart-wrap" style="height:180px;"></div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-lg-6">
        <div class="iot-card">
            <div class="iot-card-head">
                <span class="iot-card-title"><i class="bi bi-stars me-1"></i> ICS</span>
            </div>
            <div class="iot-card-body">
                <div id="gauge-ics" class="chart-wrap" style="height:180px;"></div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- RECOMENDACIONES INTELIGENTES              -->
<!-- ========================================== -->
<div class="section-hd mt-4">
    <h2><i class="bi bi-lightbulb me-1"></i> Recomendaciones inteligentes</h2>
</div>
<div class="row g-3" id="recommendations">
    <div class="col-12 text-center text-muted py-4">Cargando recomendaciones...</div>
</div>

<!-- ========================================== -->
<!-- GRÁFICAS DE HISTORIAL                      -->
<!-- ========================================== -->
<div class="section-hd mt-4">
    <h2><i class="bi bi-clock-history me-1"></i> Historial reciente</h2>
</div>
<div class="row g-3">
    <div class="col-lg-6">
        <div class="iot-card">
            <div class="iot-card-head">
                <span class="iot-card-title">Temperatura</span>
            </div>
            <div class="iot-card-body">
                <div id="chart-temp" class="chart-wrap" style="height:200px;"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="iot-card">
            <div class="iot-card-head">
                <span class="iot-card-title">Humedad</span>
            </div>
            <div class="iot-card-body">
                <div id="chart-hum" class="chart-wrap" style="height:200px;"></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mt-2">
    <div class="col-lg-6">
        <div class="iot-card">
            <div class="iot-card-head">
                <span class="iot-card-title">Ruido</span>
            </div>
            <div class="iot-card-body">
                <div id="chart-ruido" class="chart-wrap" style="height:200px;"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="iot-card">
            <div class="iot-card-head">
                <span class="iot-card-title">ICS</span>
            </div>
            <div class="iot-card-body">
                <div id="chart-ics" class="chart-wrap" style="height:200px;"></div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>