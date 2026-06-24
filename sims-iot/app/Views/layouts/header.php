<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'SueñoSmart IoT' ?></title>

    <!-- Meta para la API -->
    <meta name="api-base" content="<?= base_url('api') ?>">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- ECharts -->
    <script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>

    <!-- Estilos personalizados -->
    <style>
        /* ================================================================
           SueñoSmart IoT — Estilos
           Tema: Nocturno / Salud / Tecnología
           ================================================================ */

        /* ── Variables ─────────────────────────────────────────────── */
        :root {
            --bg:        #0b0f1e;
            --surface:   #111827;
            --card:      #1a2236;
            --card-2:    #1f2a42;
            --border:    #2a3552;
            --border-2:  rgba(255,255,255,0.06);
            --accent:    #7c3aed;
            --accent-l:  #8b5cf6;
            --blue:      #3b82f6;
            --sky:       #38bdf8;
            --green:     #10b981;
            --yellow:    #f59e0b;
            --red:       #ef4444;
            --purple:    #a78bfa;
            --orange:    #f97316;
            --glow-accent:  rgba(124,58,237,0.2);
            --glow-green:   rgba(16,185,129,0.15);
            --glow-yellow:  rgba(245,158,11,0.15);
            --glow-red:     rgba(239,68,68,0.15);
            --glow-blue:    rgba(59,130,246,0.15);
            --glow-sky:     rgba(56,189,248,0.12);
            --text:      #e2e8f0;
            --text-2:    #94a3b8;
            --text-3:    #4b5563;
            --sidebar-w:  260px;
            --header-h:   64px;
            --radius:     14px;
            --radius-sm:  9px;
            --transition: all 0.22s cubic-bezier(0.4,0,0.2,1);
        }

        /* ── Reset ──────────────────────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            background-color: var(--bg);
            color: var(--text);
            font-size: 14px;
            line-height: 1.6;
            overflow-x: hidden;
            background-image:
                radial-gradient(ellipse at 15% 30%, rgba(124,58,237,0.07) 0%, transparent 55%),
                radial-gradient(ellipse at 85% 70%, rgba(56,189,248,0.05) 0%, transparent 55%),
                linear-gradient(rgba(59,130,246,0.025) 1px, transparent 1px),
                linear-gradient(90deg, rgba(59,130,246,0.025) 1px, transparent 1px);
            background-size: 100% 100%, 100% 100%, 50px 50px, 50px 50px;
            background-attachment: fixed;
        }

        /* ── App layout ─────────────────────────────────────────────── */
        .app-body       { display: flex; min-height: 100vh; }
        .main-wrapper   { display: flex; flex-direction: column; flex: 1; min-height: 100vh; margin-left: var(--sidebar-w); transition: margin .3s ease; }

        /* ── Sidebar ────────────────────────────────────────────────── */
        .app-sidebar {
            width: var(--sidebar-w);
            background: var(--surface);
            border-right: 1px solid var(--border);
            position: fixed; top: 0; left: 0; height: 100vh;
            display: flex; flex-direction: column;
            z-index: 300; overflow: hidden;
            transition: width .3s ease, transform .3s ease;
        }

        .app-sidebar.collapsed { width: 70px; }
        .app-sidebar.collapsed ~ .main-wrapper { margin-left: 70px; }
        .app-sidebar.collapsed .nav-text { display: none; }
        .app-sidebar.collapsed .sidebar-brand-name { display: none; }
        .app-sidebar.collapsed .nav-section-label { display: none; }

        .sidebar-brand {
            height: var(--header-h);
            padding: 0 18px;
            display: flex; align-items: center; gap: 12px;
            border-bottom: 1px solid var(--border);
            text-decoration: none;
            flex-shrink: 0;
        }

        .brand-orb {
            width: 40px; height: 40px; flex-shrink: 0;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-l) 100%);
            display: flex; align-items: center; justify-content: center;
            font-size: 20px;
            box-shadow: 0 0 20px var(--glow-accent);
        }

        .sidebar-brand-name {
            display: flex; flex-direction: column;
            overflow: hidden; white-space: nowrap;
        }

        .sidebar-brand-name .b-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 14px; font-weight: 700;
            color: var(--text);
            letter-spacing: -0.01em;
        }

        .sidebar-brand-name .b-sub {
            font-size: 10px; color: var(--text-3);
            letter-spacing: 0.08em; text-transform: uppercase;
        }

        .sidebar-nav {
            flex: 1; overflow-y: auto; padding: 12px;
            scrollbar-width: none;
        }

        .sidebar-nav::-webkit-scrollbar { display: none; }

        .nav-section-label {
            font-size: 9px; font-weight: 700;
            letter-spacing: 0.14em; text-transform: uppercase;
            color: var(--text-3); padding: 10px 8px 4px;
            white-space: nowrap;
        }

        .nav-section-label:first-child { padding-top: 4px; }

        .sidebar-link {
            display: flex; align-items: center; gap: 11px;
            padding: 9px 12px; border-radius: var(--radius-sm);
            color: var(--text-2); text-decoration: none;
            font-size: 13px; font-weight: 500;
            margin-bottom: 2px; position: relative;
            transition: var(--transition); white-space: nowrap;
        }

        .sidebar-link:hover {
            background: var(--card-2); color: var(--text);
        }

        .sidebar-link.active {
            background: var(--glow-accent);
            color: var(--accent-l);
            border: 1px solid rgba(124,58,237,0.2);
        }

        .sidebar-link.active::after {
            content: '';
            position: absolute; left: 0; top: 20%; bottom: 20%;
            width: 3px; background: var(--accent);
            border-radius: 0 2px 2px 0;
        }

        .sidebar-link i { font-size: 17px; width: 20px; text-align: center; flex-shrink: 0; }

        .sidebar-foot {
            padding: 14px 18px;
            border-top: 1px solid var(--border);
            flex-shrink: 0;
        }

        .sys-pill {
            display: flex; align-items: center; gap: 8px;
            background: var(--card); border-radius: 8px;
            padding: 8px 12px;
        }

        .sys-dot {
            width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0;
            background: var(--green); box-shadow: 0 0 8px var(--green);
            animation: pulse 2.5s ease-in-out infinite;
        }

        .sys-dot.offline { background: var(--red); box-shadow: 0 0 8px var(--red); animation: none; }

        .sys-info { display: flex; flex-direction: column; }
        .sys-info .s-label { font-size: 10px; font-weight: 600; color: var(--text); }
        .sys-info .s-sub   { font-size: 10px; color: var(--text-3); }

        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.35} }

        /* ── Header ──────────────────────────────────────────────────── */
        .app-header {
            height: var(--header-h);
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 0 24px;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 200;
            flex-shrink: 0;
        }

        .header-left { display: flex; align-items: center; gap: 14px; }

        .sidebar-toggle {
            background: var(--card); border: 1px solid var(--border);
            color: var(--text-2); border-radius: 8px;
            width: 36px; height: 36px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; font-size: 18px; transition: var(--transition);
        }

        .sidebar-toggle:hover { color: var(--text); border-color: var(--accent); }

        .page-heading h1 {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 16px; font-weight: 700;
            color: var(--text); margin: 0;
        }

        .page-heading .page-sub {
            font-size: 11px; color: var(--text-3); margin: 0;
        }

        .header-right { display: flex; align-items: center; gap: 14px; }

        .header-datetime {
            font-size: 12px; color: var(--text-2);
            text-align: right; display: none;
        }

        @media (min-width: 1024px) { .header-datetime { display: block; } }

        .conn-badge {
            display: inline-flex; align-items: center; gap: 6px;
            font-size: 11px; font-weight: 600;
            padding: 5px 12px; border-radius: 20px;
            background: var(--glow-green); color: var(--green);
            border: 1px solid rgba(16,185,129,.2);
            white-space: nowrap;
        }

        .conn-badge.offline { background: var(--glow-red); color: var(--red); border-color: rgba(239,68,68,.2); }

        .conn-dot {
            width: 6px; height: 6px; border-radius: 50%;
            background: currentColor; animation: pulse 2s ease-in-out infinite;
        }

        /* ── Page main ──────────────────────────────────────────────── */
        .page-main { flex: 1; padding: 24px; }

        /* ── Footer ─────────────────────────────────────────────────── */
        .app-footer {
            background: var(--surface); border-top: 1px solid var(--border);
            padding: 12px 24px;
            display: flex; align-items: center; justify-content: space-between;
            font-size: 11px; color: var(--text-3); flex-shrink: 0;
            flex-wrap: wrap; gap: 4px;
        }

        .footer-brand { color: var(--accent-l); font-weight: 600; }

        /* ── Section header ──────────────────────────────────────────── */
        .section-hd {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 16px;
        }

        .section-hd h2 {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 13px; font-weight: 700;
            color: var(--text-2); letter-spacing: 0.06em;
            text-transform: uppercase; margin: 0;
            display: flex; align-items: center; gap: 8px;
        }

        .section-hd h2::before {
            content: '';
            display: inline-block; width: 3px; height: 16px;
            background: var(--accent); border-radius: 2px;
        }

        /* ── Metric cards ────────────────────────────────────────────── */
        .metric-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 18px 20px;
            position: relative; overflow: hidden;
            transition: var(--transition); height: 100%;
        }

        .metric-card::before {
            content: '';
            position: absolute; top: 0; left: 0; right: 0; height: 2px;
            background: var(--accent); transition: background .3s;
        }

        .metric-card.c-green::before  { background: var(--green); }
        .metric-card.c-yellow::before { background: var(--yellow); }
        .metric-card.c-red::before    { background: var(--red); }
        .metric-card.c-blue::before   { background: var(--blue); }
        .metric-card.c-purple::before { background: var(--accent); }
        .metric-card.c-sky::before    { background: var(--sky); }

        .metric-card:hover {
            border-color: rgba(124,58,237,0.35);
            box-shadow: 0 0 30px var(--glow-accent);
            transform: translateY(-2px);
        }

        .mc-header {
            display: flex; justify-content: space-between; align-items: flex-start;
            margin-bottom: 12px;
        }

        .mc-label {
            font-size: 11px; font-weight: 700;
            letter-spacing: 0.07em; text-transform: uppercase;
            color: var(--text-3);
        }

        .mc-icon {
            width: 36px; height: 36px; border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; flex-shrink: 0;
        }

        .mc-icon.i-red    { background: var(--glow-red); }
        .mc-icon.i-blue   { background: var(--glow-blue); }
        .mc-icon.i-yellow { background: var(--glow-yellow); }
        .mc-icon.i-green  { background: var(--glow-green); }
        .mc-icon.i-purple { background: var(--glow-accent); }
        .mc-icon.i-sky    { background: var(--glow-sky); }

        .mc-value {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 32px; font-weight: 700;
            color: var(--text); line-height: 1; margin-bottom: 8px;
        }

        .mc-value .mc-unit {
            font-size: 14px; font-weight: 400; color: var(--text-3);
            margin-left: 2px; font-family: 'Inter', sans-serif;
        }

        .mc-badge {
            display: inline-flex; align-items: center; gap: 4px;
            font-size: 10px; font-weight: 700;
            padding: 3px 9px; border-radius: 20px; letter-spacing: 0.04em;
        }

        .mb-green  { background: var(--glow-green);  color: var(--green);  border: 1px solid rgba(16,185,129,.2); }
        .mb-yellow { background: var(--glow-yellow); color: var(--yellow); border: 1px solid rgba(245,158,11,.2); }
        .mb-red    { background: var(--glow-red);    color: var(--red);    border: 1px solid rgba(239,68,68,.2); }
        .mb-blue   { background: var(--glow-blue);   color: var(--blue);   border: 1px solid rgba(59,130,246,.2); }
        .mb-purple { background: var(--glow-accent); color: var(--accent-l); border: 1px solid rgba(124,58,237,.2); }
        .mb-sky    { background: var(--glow-sky);    color: var(--sky);    border: 1px solid rgba(56,189,248,.2); }

        /* ── Generic card ────────────────────────────────────────────── */
        .iot-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
        }

        .iot-card-head {
            padding: 14px 18px 12px;
            border-bottom: 1px solid var(--border-2);
            display: flex; align-items: center; justify-content: space-between;
        }

        .iot-card-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 12px; font-weight: 700;
            color: var(--text-2); text-transform: uppercase;
            letter-spacing: 0.06em; margin: 0;
            display: flex; align-items: center; gap: 6px;
        }

        .iot-card-body  { padding: 18px; }

        /* ── ICS quality label ───────────────────────────────────────── */
        .ics-quality {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 14px; font-weight: 700; transition: color .3s;
        }

        .ics-quality.excellent { color: var(--green); }
        .ics-quality.regular   { color: var(--yellow); }
        .ics-quality.bad       { color: var(--red); }

        /* ── Update tag ─────────────────────────────────────────────── */
        .update-tag {
            font-size: 11px; color: var(--text-3);
            display: flex; align-items: center; gap: 5px;
        }

        /* ── Responsive ──────────────────────────────────────────────── */
        @media (max-width: 1024px) {
            :root { --sidebar-w: 0px; }
            .app-sidebar { transform: translateX(-260px); }
            .app-sidebar.mobile-open { transform: translateX(0); width: 260px; }
            .main-wrapper { margin-left: 0 !important; }
        }

        /* ── Scrollbar ───────────────────────────────────────────────── */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: var(--bg); }
        ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--text-3); }

        /* ── Recomendaciones ────────────────────────────────────────── */
        .reco-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 14px 16px;
            display: flex; align-items: flex-start; gap: 12px;
            height: 100%;
            transition: var(--transition);
        }

        .reco-card:hover { border-color: rgba(124,58,237,0.3); transform: translateY(-1px); }

        .reco-card.reco-success { border-left: 3px solid var(--green); }
        .reco-card.reco-warning { border-left: 3px solid var(--yellow); }
        .reco-card.reco-danger  { border-left: 3px solid var(--red); }
        .reco-card.reco-info    { border-left: 3px solid var(--sky); }

        .reco-icon { font-size: 22px; flex-shrink: 0; margin-top: 2px; }
        .reco-body { flex: 1; }
        .reco-title { font-size: 12px; font-weight: 700; color: var(--text); margin-bottom: 3px; }
        .reco-text  { font-size: 12px; color: var(--text-2); line-height: 1.5; margin: 0; }

        /* ── Flash messages ──────────────────────────────────────────── */
        #flash-messages { position: fixed; top: 76px; right: 20px; z-index: 9999; max-width: 380px; }
        #flash-messages .alert { border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 13px; background: var(--card); color: var(--text); }

        .chart-wrap { width: 100%; }
    </style>
</head>
<body>

<!-- ========================================== -->
<!-- APP BODY                                   -->
<!-- ========================================== -->
<div class="app-body">

    <!-- ========================================== -->
    <!-- SIDEBAR                                   -->
    <!-- ========================================== -->
    <?= view('partials/sidebar') ?>

    <!-- ========================================== -->
    <!-- MAIN WRAPPER                              -->
    <!-- ========================================== -->
    <div class="main-wrapper">

        <!-- ========================================== -->
        <!-- HEADER                                    -->
        <!-- ========================================== -->
        <header class="app-header">
            <div class="header-left">
                <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
                    <i class="bi bi-list"></i>
                </button>
                <div class="page-heading">
                    <h1><?= $page_title ?? 'Dashboard' ?></h1>
                    <p class="page-sub"><?= $page_sub ?? 'Monitor de sueño en tiempo real' ?></p>
                </div>
            </div>
            <div class="header-right">
                <div class="header-datetime" id="headerDatetime"></div>
                <div class="conn-badge">
                    <span class="conn-dot"></span>
                    <span>Conectado</span>
                </div>
            </div>
        </header>

        <!-- ========================================== -->
        <!-- MAIN CONTENT                              -->
        <!-- ========================================== -->
        <main class="page-main">
            <?= $this->renderSection('content') ?>
        </main>

        <!-- ========================================== -->
        <!-- FOOTER                                    -->
        <!-- ========================================== -->
        <?= view('partials/footer') ?>

    </div>
</div>

<!-- ========================================== -->
<!-- FLASH MESSAGES                            -->
<!-- ========================================== -->
<div id="flash-messages"></div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- ========================================== -->
<!-- JAVASCRIPT PRINCIPAL                       -->
<!-- ========================================== -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    'use strict';

    console.log('🚀 SueñoSmart IoT - Inicializando...');

    // ==========================================
    // CONFIGURACIÓN
    // ==========================================
    const API_BASE = document.querySelector('meta[name="api-base"]')?.content || '/api';
    const UPDATE_MS = 5000;

    // ==========================================
    // DOM HELPERS
    // ==========================================
    const $ = id => document.getElementById(id);
    const setText = (id, v) => { const e = $(id); if (e) e.textContent = v; };

    // ==========================================
    // FUNCIONES DE UTILIDAD
    // ==========================================
    function toNumber(value) {
        const num = parseFloat(value);
        return isNaN(num) ? 0 : num;
    }

    function fmtTime(str) {
        if (!str) return new Date().toLocaleTimeString('es-MX');
        return new Date(str).toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    }

    function classifyICS(val) {
        const v = toNumber(val);
        if (v >= 80) return { label: 'Excelente', cls: 'excellent', badge: 'mb-green' };
        if (v >= 60) return { label: 'Regular', cls: 'regular', badge: 'mb-yellow' };
        return { label: 'Deficiente', cls: 'bad', badge: 'mb-red' };
    }

    function setCardStatus(id, text, cls) {
        const el = $(id);
        if (el) { el.textContent = text; el.className = 'mc-badge ' + cls; }
    }

    // ==========================================
    // API FETCH
    // ==========================================
    async function apiFetch(path) {
        try {
            const res = await fetch(`${API_BASE}${path}`);
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            return await res.json();
        } catch (err) {
            console.error('[API]', err.message);
            setSystemStatus(false);
            throw err;
        }
    }

    function setSystemStatus(online) {
        const badge = document.querySelector('.conn-badge');
        const dot = document.querySelector('.conn-dot');
        const sDot = document.querySelector('.sys-dot');
        const sLbl = document.querySelector('.s-label');

        if (badge) {
            badge.className = 'conn-badge' + (online ? '' : ' offline');
            badge.querySelector('span:last-child').textContent = online ? 'Conectado' : 'Sin conexión';
        }
        if (sDot) sDot.className = 'sys-dot' + (online ? '' : ' offline');
        if (sLbl) sLbl.textContent = online ? 'Sistema activo' : 'Sin conexión';
    }

    // ==========================================
    // ECHARTS INSTANCES
    // ==========================================
    const CI = {};

    // ==========================================
    // INICIALIZAR GAUGES
    // ==========================================
    function initGauges() {
        // Gauge Temperatura
        if ($('gauge-temp')) {
            CI['gauge-temp'] = echarts.init($('gauge-temp'));
            CI['gauge-temp'].setOption({
                backgroundColor: 'transparent',
                series: [{
                    type: 'gauge',
                    startAngle: 215, endAngle: -35,
                    min: 0, max: 50, radius: '88%',
                    progress: { show: true, width: 15, itemStyle: { color: '#ef4444', shadowColor: '#ef4444', shadowBlur: 12 } },
                    pointer: { show: false },
                    axisLine: { lineStyle: { width: 15, color: [[0.36, '#3b82f6'], [0.52, '#10b981'], [0.72, '#f59e0b'], [1, '#ef4444']] } },
                    splitLine: { show: false },
                    axisTick: { show: false },
                    axisLabel: { distance: 22, color: '#94a3b8', fontSize: 10 },
                    detail: { valueAnimation: true, offsetCenter: ['0%', '20%'], formatter: '{value}°C', color: '#e2e8f0', fontFamily: 'Space Grotesk, sans-serif', fontSize: 26, fontWeight: 700 },
                    title: { offsetCenter: ['0%', '50%'], color: '#94a3b8', fontFamily: 'Inter, sans-serif', fontSize: 12 },
                    data: [{ value: 0, name: 'Temperatura' }]
                }]
            });
        }

        // Gauge Humedad
        if ($('gauge-hum')) {
            CI['gauge-hum'] = echarts.init($('gauge-hum'));
            CI['gauge-hum'].setOption({
                backgroundColor: 'transparent',
                series: [{
                    type: 'gauge',
                    startAngle: 215, endAngle: -35,
                    min: 0, max: 100, radius: '88%',
                    progress: { show: true, width: 15, itemStyle: { color: '#3b82f6', shadowColor: '#3b82f6', shadowBlur: 12 } },
                    pointer: { show: false },
                    axisLine: { lineStyle: { width: 15, color: [[0.3, '#f59e0b'], [0.65, '#10b981'], [1, '#f59e0b']] } },
                    splitLine: { show: false },
                    axisTick: { show: false },
                    axisLabel: { distance: 22, color: '#94a3b8', fontSize: 10 },
                    detail: { valueAnimation: true, offsetCenter: ['0%', '20%'], formatter: '{value}%', color: '#e2e8f0', fontFamily: 'Space Grotesk, sans-serif', fontSize: 26, fontWeight: 700 },
                    title: { offsetCenter: ['0%', '50%'], color: '#94a3b8', fontFamily: 'Inter, sans-serif', fontSize: 12 },
                    data: [{ value: 0, name: 'Humedad' }]
                }]
            });
        }

        // Gauge ICS
        if ($('gauge-ics')) {
            CI['gauge-ics'] = echarts.init($('gauge-ics'));
            CI['gauge-ics'].setOption({
                backgroundColor: 'transparent',
                series: [{
                    type: 'gauge',
                    startAngle: 215, endAngle: -35,
                    min: 0, max: 100, radius: '88%',
                    progress: { show: true, width: 15, itemStyle: { color: '#7c3aed', shadowColor: '#7c3aed', shadowBlur: 12 } },
                    pointer: { show: false },
                    axisLine: { lineStyle: { width: 15, color: [[0.59, '#ef4444'], [0.79, '#f59e0b'], [1, '#10b981']] } },
                    splitLine: { show: false },
                    axisTick: { show: false },
                    axisLabel: { distance: 22, color: '#94a3b8', fontSize: 10 },
                    detail: { valueAnimation: true, offsetCenter: ['0%', '20%'], formatter: '{value}/100', color: '#e2e8f0', fontFamily: 'Space Grotesk, sans-serif', fontSize: 26, fontWeight: 700 },
                    title: { offsetCenter: ['0%', '50%'], color: '#94a3b8', fontFamily: 'Inter, sans-serif', fontSize: 12 },
                    data: [{ value: 0, name: 'ICS' }]
                }]
            });
        }
    }

    function updateGauges(d) {
        if (CI['gauge-temp']) CI['gauge-temp'].setOption({ series: [{ data: [{ value: toNumber(d.temperatura), name: 'Temperatura' }] }] });
        if (CI['gauge-hum']) CI['gauge-hum'].setOption({ series: [{ data: [{ value: toNumber(d.humedad), name: 'Humedad' }] }] });
        if (CI['gauge-ics']) CI['gauge-ics'].setOption({ series: [{ data: [{ value: toNumber(d.indice_sueno), name: 'ICS' }] }] });
    }

    // ==========================================
    // INICIALIZAR GRÁFICAS DE LÍNEAS
    // ==========================================
    function initLineCharts() {
        const cfgs = [
            { id: 'chart-temp', label: 'Temperatura (°C)', color: '#ef4444' },
            { id: 'chart-hum', label: 'Humedad (%)', color: '#3b82f6' },
            { id: 'chart-ruido', label: 'Ruido (dB)', color: '#a78bfa' },
            { id: 'chart-ics', label: 'ICS', color: '#10b981' }
        ];

        cfgs.forEach(cfg => {
            if ($(cfg.id)) {
                CI[cfg.id] = echarts.init($(cfg.id));
                CI[cfg.id].setOption({
                    backgroundColor: 'transparent',
                    grid: { left: 40, right: 20, top: 20, bottom: 30, containLabel: false },
                    tooltip: { trigger: 'axis', backgroundColor: '#1a2236', borderColor: '#2a3552', textStyle: { color: '#e2e8f0', fontSize: 12 } },
                    xAxis: { type: 'category', data: [], axisLine: { lineStyle: { color: '#2a3552' } }, axisTick: { show: false }, axisLabel: { color: '#4b5563', fontSize: 9 } },
                    yAxis: { type: 'value', splitLine: { lineStyle: { color: '#2a3552', type: 'dashed' } }, axisLine: { show: false }, axisTick: { show: false }, axisLabel: { color: '#4b5563', fontSize: 9 } },
                    series: [{
                        name: cfg.label,
                        type: 'line',
                        data: [],
                        smooth: true,
                        symbol: 'none',
                        lineStyle: { color: cfg.color, width: 2.5 },
                        areaStyle: {
                            color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                                { offset: 0, color: cfg.color + '50' },
                                { offset: 1, color: cfg.color + '00' }
                            ])
                        }
                    }]
                });
            }
        });
    }

    function updateCharts(readings) {
        const rev = [...readings].reverse().slice(0, 30);
        const times = rev.map(r => fmtTime(r.fecha));
        const keyMap = {
            'chart-temp': 'temperatura',
            'chart-hum': 'humedad',
            'chart-ruido': 'ruido',
            'chart-ics': 'indice_sueno'
        };

        Object.entries(keyMap).forEach(([id, key]) => {
            if (!CI[id]) return;
            CI[id].setOption({
                xAxis: { data: times },
                series: [{ data: rev.map(r => toNumber(r[key])) }]
            });
        });
    }

    // ==========================================
    // RECOMENDACIONES
    // ==========================================
    function updateRecommendations(d) {
        const el = $('recommendations');
        if (!el) return;

        const temp = toNumber(d.temperatura);
        const hum = toNumber(d.humedad);
        const ruido = toNumber(d.ruido);
        const ics = toNumber(d.indice_sueno);
        const mov = toNumber(d.movimiento);

        const recos = [];

        if (temp > 26) recos.push({ icon: '🌡️', title: 'Temperatura elevada', text: 'La temperatura supera los 26°C. Considera ventilar el cuarto o bajar el termostato.', type: 'reco-warning' });
        else if (temp < 17) recos.push({ icon: '❄️', title: 'Temperatura baja', text: 'La temperatura está por debajo de 17°C. Agrega ropa de cama o ajusta la calefacción.', type: 'reco-info' });
        else recos.push({ icon: '✅', title: 'Temperatura ideal', text: 'La temperatura está en el rango óptimo para dormir (18–22°C). ¡Excelente!', type: 'reco-success' });

        if (hum > 65) recos.push({ icon: '💧', title: 'Humedad fuera de rango', text: 'Humedad por encima del 65%. Un deshumidificador puede mejorar las condiciones.', type: 'reco-warning' });
        else if (hum < 35) recos.push({ icon: '🏜️', title: 'Ambiente seco', text: 'La humedad está por debajo del 35%. Considera un humidificador.', type: 'reco-warning' });
        else recos.push({ icon: '✅', title: 'Humedad óptima', text: 'La humedad se encuentra en el rango ideal (40–60%).', type: 'reco-success' });

        if (ruido > 40) recos.push({ icon: '🔊', title: 'Exceso de ruido', text: `Ruido detectado: ${ruido} dB. Considera tapones o una máquina de ruido blanco.`, type: 'reco-danger' });
        else recos.push({ icon: '🔇', title: 'Nivel de ruido aceptable', text: 'El ambiente está suficientemente silencioso para un sueño reparador.', type: 'reco-success' });

        if (mov > 0) recos.push({ icon: '🌀', title: 'Movimiento nocturno detectado', text: 'Se registraron movimientos durante el descanso. Puede indicar sueño inquieto.', type: 'reco-warning' });

        if (ics < 60) recos.push({ icon: '⚠️', title: 'Calidad del sueño deficiente', text: `El ICS actual es ${ics}/100. Revisa las condiciones ambientales.`, type: 'reco-danger' });
        else if (ics >= 80) recos.push({ icon: '🌙', title: 'Excelente calidad de sueño', text: `ICS de ${ics}/100. ¡Condiciones óptimas!`, type: 'reco-success' });

        el.innerHTML = recos.map(r => `
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="reco-card ${r.type}">
                    <span class="reco-icon">${r.icon}</span>
                    <div class="reco-body">
                        <p class="reco-title">${r.title}</p>
                        <p class="reco-text">${r.text}</p>
                    </div>
                </div>
            </div>
        `).join('');
    }

    // ==========================================
    // FETCH ÚLTIMO REGISTRO
    // ==========================================
    async function fetchUltimo() {
        try {
            const d = await apiFetch('/lecturas/ultima');
            setSystemStatus(true);

            // Cards
            setText('card-temp', toNumber(d.temperatura).toFixed(1));
            setText('card-hum', toNumber(d.humedad).toFixed(1));
            setText('card-ruido', toNumber(d.ruido).toFixed(0));
            setText('card-mov', toNumber(d.movimiento) > 0 ? 'Detectado' : 'Estable');
            setText('card-ics', toNumber(d.indice_sueno).toFixed(0));

            // Badges
            const temp = toNumber(d.temperatura);
            setCardStatus('badge-temp',
                temp > 26 ? '🔴 Alta' : temp < 17 ? '🔵 Baja' : '✅ Ideal',
                temp > 26 ? 'mb-red' : temp < 17 ? 'mb-blue' : 'mb-green'
            );

            const hum = toNumber(d.humedad);
            setCardStatus('badge-hum',
                hum > 65 || hum < 35 ? '⚠️ Fuera' : '✅ Ideal',
                hum > 65 || hum < 35 ? 'mb-yellow' : 'mb-green'
            );

            const ruido = toNumber(d.ruido);
            setCardStatus('badge-ruido',
                ruido > 40 ? '🔊 Alto' : ruido > 30 ? '⚡ Medio' : '🔇 Bajo',
                ruido > 40 ? 'mb-red' : ruido > 30 ? 'mb-yellow' : 'mb-green'
            );

            setCardStatus('badge-mov',
                toNumber(d.movimiento) > 0 ? '⚡ Detectado' : '✅ Estable',
                toNumber(d.movimiento) > 0 ? 'mb-yellow' : 'mb-green'
            );

            const q = classifyICS(d.indice_sueno);
            setCardStatus('badge-ics', q.label, q.badge);

            const qEl = $('ics-quality');
            if (qEl) { qEl.textContent = q.label; qEl.className = 'ics-quality ' + q.cls; }

            // Calidad general
            setCardStatus('badge-calidad',
                q.label,
                q.badge
            );

            // Update timestamp
            setText('last-update', 'Actualizado: ' + fmtTime(d.fecha));

            // Gauges
            updateGauges(d);

            // Recomendaciones
            updateRecommendations(d);

        } catch (err) {
            console.error('[fetchUltimo]', err);
        }
    }

    // ==========================================
    // FETCH HISTORIAL
    // ==========================================
    async function fetchHistorial() {
        try {
            const data = await apiFetch('/lecturas?limit=30');
            updateCharts(data);
        } catch (err) {
            console.error('[fetchHistorial]', err);
        }
    }

    // ==========================================
    // FLASH MESSAGES
    // ==========================================
    function showFlash(msg, type = 'info') {
        let c = $('flash-messages');
        if (!c) { c = document.createElement('div'); c.id = 'flash-messages'; document.body.appendChild(c); }
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
    // RELOJ EN TIEMPO REAL
    // ==========================================
    function updateDatetime() {
        const el = $('headerDatetime');
        if (!el) return;
        el.textContent = new Date().toLocaleString('es-MX', {
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric',
            hour: '2-digit', minute: '2-digit', second: '2-digit'
        });
    }

    // ==========================================
    // SIDEBAR TOGGLE
    // ==========================================
    function initSidebar() {
        const btn = $('sidebarToggle');
        const side = $('appSidebar');
        if (!btn || !side) return;

        btn.addEventListener('click', () => {
            if (window.innerWidth < 1024) {
                side.classList.toggle('mobile-open');
            } else {
                side.classList.toggle('collapsed');
                const wrapper = document.querySelector('.main-wrapper');
                if (wrapper) {
                    wrapper.style.marginLeft = side.classList.contains('collapsed') ? '70px' : 'var(--sidebar-w)';
                }
            }
        });

        document.addEventListener('click', e => {
            if (window.innerWidth < 1024 && !side.contains(e.target) && !btn.contains(e.target)) {
                side.classList.remove('mobile-open');
            }
        });
    }

    // ==========================================
    // RESIZE DE GRÁFICAS
    // ==========================================
    function handleResize() {
        Object.values(CI).forEach(c => c?.resize?.());
    }

    // ==========================================
    // INICIALIZACIÓN
    // ==========================================
    initGauges();
    initLineCharts();
    initSidebar();

    updateDatetime();
    setInterval(updateDatetime, 1000);

    // Carga inicial
    fetchUltimo();
    fetchHistorial();

    // Actualización automática
    setInterval(fetchUltimo, UPDATE_MS);
    setInterval(fetchHistorial, 30000);

    window.addEventListener('resize', handleResize);

    // Exponer funciones para depuración
    window.showFlash = showFlash;

    console.log('✅ SueñoSmart IoT - Inicializado correctamente');
});
</script>

</body>
</html>