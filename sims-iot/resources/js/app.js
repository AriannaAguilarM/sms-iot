/**
 * SueñoSmart IoT — app.js
 * Sistema Inteligente de Monitoreo del Sueño
 *
 * Vite entry point. Importa Bootstrap y ECharts desde npm.
 * Para CDN (sin Vite build), eliminar los imports y usar CDN en la vista.
 */

// ── Imports (Vite / npm) ────────────────────────────────────────
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap-icons/font/bootstrap-icons.css';
import '../css/app.css';
import * as bootstrap from 'bootstrap';
import * as echarts from 'echarts';

// Exponer al scope global para acceso desde vistas PHP
window.bootstrap = bootstrap;
window.echarts   = echarts;

// ── API Configuration ───────────────────────────────────────────
const API_BASE = (() => {
  const meta = document.querySelector('meta[name="api-base"]');
  return meta ? meta.content.replace(/\/$/, '') : '/api';
})();

const UPDATE_MS   = 5000;   // Actualización tiempo real: 5 segundos
const HISTORY_MS  = 30000;  // Actualización histórico:  30 segundos

// ── ECharts instances ───────────────────────────────────────────
const CI = {}; // Chart Instances

// ── Color palette ───────────────────────────────────────────────
const C = {
  accent:  '#7c3aed',
  blue:    '#3b82f6',
  sky:     '#38bdf8',
  green:   '#10b981',
  yellow:  '#f59e0b',
  red:     '#ef4444',
  purple:  '#a78bfa',
  orange:  '#f97316',
  text:    '#94a3b8',
  muted:   '#4b5563',
  border:  '#2a3552',
  card:    '#1a2236',
  card2:   '#1f2a42',
};

// ── DOM Helpers ──────────────────────────────────────────────────
const $  = id => document.getElementById(id);
const $$ = sel => document.querySelectorAll(sel);
const setText = (id, v) => { const e = $(id); if (e) e.textContent = v; };
const setHTML = (id, v) => { const e = $(id); if (e) e.innerHTML  = v; };

// ── API fetch wrapper ────────────────────────────────────────────
async function apiFetch(path, opts = {}) {
  const url = `${API_BASE}${path}`;
  try {
    const res = await fetch(url, {
      ...opts,
      headers: { 'Content-Type': 'application/json', ...(opts.headers || {}) },
    });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    return await res.json();
  } catch (err) {
    console.error(`[API] ${path}:`, err.message);
    setSystemStatus(false);
    throw err;
  }
}

// ── ICS classification ───────────────────────────────────────────
function classifyICS(val) {
  const v = +val || 0;
  if (v >= 80) return { label:'Excelente', cls:'excellent', color:C.green,  badge:'success',       mb:'mb-green'  };
  if (v >= 60) return { label:'Regular',   cls:'regular',   color:C.yellow, badge:'warning',       mb:'mb-yellow' };
  return              { label:'Deficiente', cls:'bad',       color:C.red,    badge:'danger',        mb:'mb-red'    };
}

// ── Date formatters ──────────────────────────────────────────────
function fmtDate(str) {
  if (!str) return '—';
  return new Date(str).toLocaleString('es-MX', {
    day:'2-digit', month:'short', year:'numeric',
    hour:'2-digit', minute:'2-digit',
  });
}

function fmtTime(str) {
  if (!str) return new Date().toLocaleTimeString('es-MX');
  return new Date(str).toLocaleTimeString('es-MX', { hour:'2-digit', minute:'2-digit', second:'2-digit' });
}

// ── System status indicator ──────────────────────────────────────
function setSystemStatus(online) {
  const badge = document.querySelector('.conn-badge');
  const dot   = document.querySelector('.conn-dot');
  const sLbl  = document.querySelector('.s-label');
  const sDot  = document.querySelector('.sys-dot');

  if (badge) badge.className = 'conn-badge' + (online ? '' : ' offline');
  if (badge) badge.querySelector('span:last-child').textContent = online ? 'Conectado' : 'Sin conexión';
  if (sDot)  sDot.className = 'sys-dot' + (online ? '' : ' offline');
  if (sLbl)  sLbl.textContent = online ? 'Sistema activo' : 'Sin conexión';
}

// ─────────────────────────────────────────────────────────────────
//  1. GAUGE CHARTS
// ─────────────────────────────────────────────────────────────────
function makeGaugeOpts({ label, min, max, value, unit, color, ranges }) {
  return {
    backgroundColor: 'transparent',
    series: [{
      type: 'gauge',
      startAngle: 215, endAngle: -35,
      min, max, radius: '88%',
      progress: {
        show: true, width: 15,
        itemStyle: { color, shadowColor: color, shadowBlur: 12 },
      },
      pointer: { show: false },
      axisLine: {
        lineStyle: {
          width: 15,
          color: ranges || [[1, '#2a3552']],
        },
      },
      splitLine: { show: false },
      axisTick: { show: false },
      axisLabel: {
        distance: 22, color: C.text, fontSize: 10,
        formatter: v => (v === min || v === max || v === Math.round((max+min)/2)) ? v : '',
      },
      detail: {
        valueAnimation: true, offsetCenter: ['0%', '20%'],
        formatter: `{value}${unit}`,
        color: '#e2e8f0', fontFamily: 'Space Grotesk, sans-serif',
        fontSize: 26, fontWeight: 700,
      },
      title: {
        offsetCenter: ['0%', '50%'], color: C.text,
        fontFamily: 'Inter, sans-serif', fontSize: 12,
      },
      data: [{ value, name: label }],
    }],
  };
}

// 2. CONFIGURACIÓN DE GAUGES ─────────────────────────────────────
function initGauges() {
  // Gauge Temperatura
  if ($('gauge-temp')) {
    CI['gauge-temp'] = echarts.init($('gauge-temp'), null, { renderer: 'canvas' });
    CI['gauge-temp'].setOption(makeGaugeOpts({
      label: 'Temperatura', min: 0, max: 50, value: 0, unit: '°C', color: C.red,
      ranges: [[0.36, C.blue], [0.52, C.green], [0.72, C.yellow], [1, C.red]],
    }));
  }

  // Gauge Humedad
  if ($('gauge-hum')) {
    CI['gauge-hum'] = echarts.init($('gauge-hum'), null, { renderer: 'canvas' });
    CI['gauge-hum'].setOption(makeGaugeOpts({
      label: 'Humedad', min: 0, max: 100, value: 0, unit: '%', color: C.blue,
      ranges: [[0.3, C.yellow], [0.65, C.green], [1, C.yellow]],
    }));
  }

  // Gauge ICS
  if ($('gauge-ics')) {
    CI['gauge-ics'] = echarts.init($('gauge-ics'), null, { renderer: 'canvas' });
    CI['gauge-ics'].setOption(makeGaugeOpts({
      label: 'ICS', min: 0, max: 100, value: 0, unit: '/100', color: C.accent,
      ranges: [[0.59, C.red], [0.79, C.yellow], [1, C.green]],
    }));
  }
}

function updateGauges(d) {
  if (CI['gauge-temp']) CI['gauge-temp'].setOption({ series:[{ data:[{ value:+(d.temperatura||0), name:'Temperatura' }] }] });
  if (CI['gauge-hum'])  CI['gauge-hum'].setOption({ series:[{ data:[{ value:+(d.humedad||0),       name:'Humedad' }] }] });
  if (CI['gauge-ics'])  CI['gauge-ics'].setOption({ series:[{ data:[{ value:+(d.indice_sueno||0),  name:'ICS' }] }] });
}

// ─────────────────────────────────────────────────────────────────
//  3. LINE CHARTS (Histórico)
// ─────────────────────────────────────────────────────────────────
function makeLineOpts({ label, color, min = null, max = null }) {
  return {
    backgroundColor: 'transparent',
    grid: { left:40, right:20, top:20, bottom:30, containLabel:false },
    tooltip: {
      trigger:'axis',
      backgroundColor: C.card, borderColor: C.border,
      textStyle: { color:'#e2e8f0', fontSize:12 },
      formatter: p => `<b>${p[0].axisValueLabel}</b><br>${label}: <b>${p[0].value}</b>`,
    },
    xAxis: {
      type:'category', data:[],
      axisLine:{ lineStyle:{ color:C.border } },
      axisTick:{ show:false },
      axisLabel:{ color:C.muted, fontSize:9 },
    },
    yAxis: {
      type:'value',
      ...(min !== null && { min }), ...(max !== null && { max }),
      splitLine:{ lineStyle:{ color:C.border, type:'dashed' } },
      axisLine:{ show:false }, axisTick:{ show:false },
      axisLabel:{ color:C.muted, fontSize:9 },
    },
    series:[{
      name:label, type:'line', data:[], smooth:true, symbol:'none',
      lineStyle:{ color, width:2.5 },
      areaStyle:{ color: new echarts.graphic.LinearGradient(0,0,0,1,[
        { offset:0, color: color+'50' }, { offset:1, color: color+'00' },
      ]) },
    }],
  };
}

// 4. CONFIGURACIÓN DE LÍNEAS ──────────────────────────────────────
function initLineCharts() {
  const cfgs = [
    { id:'chart-temp',  label:'Temperatura (°C)', color:C.red    },
    { id:'chart-hum',   label:'Humedad (%)',       color:C.blue   },
    { id:'chart-ruido', label:'Ruido (dB)',        color:C.purple },
    { id:'chart-ics',   label:'ICS',              color:C.green  },
  ];

  cfgs.forEach(cfg => {
    if ($(cfg.id)) {
      CI[cfg.id] = echarts.init($(cfg.id), null, { renderer:'canvas' });
      CI[cfg.id].setOption(makeLineOpts(cfg));
    }
  });
}

function updateCharts(readings) {
  const rev   = [...readings].reverse().slice(0, 50);
  const times = rev.map(r => fmtTime(r.fecha));
  const keyMap = {
    'chart-temp':'temperatura','chart-hum':'humedad',
    'chart-ruido':'ruido','chart-ics':'indice_sueno'
  };

  Object.entries(keyMap).forEach(([id, key]) => {
    if (!CI[id]) return;
    CI[id].setOption({
      xAxis:  { data: times },
      series: [{ data: rev.map(r => +(r[key]) || 0) }],
    });
  });
}

// ─────────────────────────────────────────────────────────────────
//  BARRA: Últimas lecturas comparadas
// ─────────────────────────────────────────────────────────────────
function initBarChart() {
  if (!$('chart-bars')) return;
  CI['chart-bars'] = echarts.init($('chart-bars'), null, { renderer:'canvas' });
  CI['chart-bars'].setOption({
    backgroundColor:'transparent',
    legend:{ top:0, textStyle:{ color:C.text, fontSize:11 }, itemWidth:12, itemHeight:8 },
    grid:{ left:'3%', right:'3%', top:36, bottom:30, containLabel:true },
    tooltip:{
      trigger:'axis', backgroundColor:C.card, borderColor:C.border,
      textStyle:{ color:'#e2e8f0' },
    },
    xAxis:{
      type:'category', data:[],
      axisLine:{ lineStyle:{ color:C.border } },
      axisTick:{ show:false },
      axisLabel:{ color:C.muted, fontSize:9 },
    },
    yAxis:{
      type:'value',
      splitLine:{ lineStyle:{ color:C.border, type:'dashed' } },
      axisLine:{ show:false }, axisTick:{ show:false },
      axisLabel:{ color:C.muted, fontSize:10 },
    },
    series:[
      { name:'Temperatura', type:'bar', data:[], itemStyle:{ color:C.red,    borderRadius:[4,4,0,0] }, barGap:'10%' },
      { name:'Humedad',     type:'bar', data:[], itemStyle:{ color:C.blue,   borderRadius:[4,4,0,0] } },
      { name:'Ruido (dB)',  type:'bar', data:[], itemStyle:{ color:C.purple, borderRadius:[4,4,0,0] } },
    ],
  });
}

// 9. BARRAS DE ÚLTIMOS REGISTROS ──────────────────────────────────
function updateBarChart(readings) {
  if (!CI['chart-bars']) return;
  const last = [...readings].reverse().slice(0, 10);
  CI['chart-bars'].setOption({
    xAxis: { data: last.map(r => fmtTime(r.fecha)) },
    series:[
      { name:'Temperatura', data: last.map(r => +(r.temperatura)||0) },
      { name:'Humedad',     data: last.map(r => +(r.humedad)||0) },
      { name:'Ruido (dB)',  data: last.map(r => +(r.ruido)||0) },
    ],
  });
}

// ─────────────────────────────────────────────────────────────────
//  10. ESTADÍSTICAS SEMANALES
// ─────────────────────────────────────────────────────────────────
function initWeeklyChart() {
  if (!$('chart-weekly')) return;
  CI['chart-weekly'] = echarts.init($('chart-weekly'), null, { renderer:'canvas' });
}

function updateWeeklyChart(data) {
  if (!CI['chart-weekly'] || !data?.length) return;
  const dias = data.map(r => r.dia ? r.dia.slice(5) : '');

  CI['chart-weekly'].setOption({
    backgroundColor:'transparent',
    legend:{ top:0, textStyle:{ color:C.text, fontSize:11 }, itemWidth:12, itemHeight:8 },
    grid:{ left:'3%', right:'3%', top:40, bottom:30, containLabel:true },
    tooltip:{
      trigger:'axis', backgroundColor:C.card, borderColor:C.border,
      textStyle:{ color:'#e2e8f0' },
    },
    xAxis:{
      type:'category', data: dias,
      axisLine:{ lineStyle:{ color:C.border } },
      axisTick:{ show:false },
      axisLabel:{ color:C.text, fontSize:11 },
    },
    yAxis:{
      type:'value',
      splitLine:{ lineStyle:{ color:C.border, type:'dashed' } },
      axisLine:{ show:false }, axisTick:{ show:false },
      axisLabel:{ color:C.muted, fontSize:10 },
    },
    series:[
      { name:'Temp. promedio', type:'bar', data:data.map(r=>+(r.temp_promedio)||0), itemStyle:{ color:C.red,   borderRadius:[4,4,0,0] } },
      { name:'Humedad prom.',  type:'bar', data:data.map(r=>+(r.humedad_promedio)||0), itemStyle:{ color:C.blue,  borderRadius:[4,4,0,0] } },
      { name:'ICS promedio',   type:'line', data:data.map(r=>+(r.indice_promedio)||0),
        smooth:true, symbol:'circle', symbolSize:7,
        lineStyle:{ color:C.green, width:2.5 }, itemStyle:{ color:C.green } },
    ],
  });
}

// ── Resize handler ───────────────────────────────────────────────
function handleResize() {
  Object.values(CI).forEach(c => c?.resize?.());
}

// ─────────────────────────────────────────────────────────────────
//  5. FETCH ÚLTIMO REGISTRO
// ─────────────────────────────────────────────────────────────────
async function fetchUltimo() {
  try {
    const d = await apiFetch('/lecturas/ultima');
    setSystemStatus(true);

    // Cards de métricas
    setText('card-temp',  (+(d.temperatura)||0).toFixed(1));
    setText('card-hum',   (+(d.humedad)||0).toFixed(1));
    setText('card-ruido', (+(d.ruido)||0).toFixed(0));
    setText('card-mov',   +d.movimiento ? 'Detectado' : 'Estable');
    setText('card-ics',   (+(d.indice_sueno)||0).toFixed(0));

    // Badges y colores dinámicos
    const temp = +(d.temperatura)||0;
    setCardStatus('badge-temp',
      temp > 26 ? { text:'🔴 Alta', cls:'mb-red' }
    : temp < 17 ? { text:'🔵 Baja', cls:'mb-blue' }
    :             { text:'✅ Ideal', cls:'mb-green' }
    );

    const hum = +(d.humedad)||0;
    setCardStatus('badge-hum',
      hum > 65 || hum < 35 ? { text:'⚠️ Fuera', cls:'mb-yellow' }
    :                         { text:'✅ Ideal',  cls:'mb-green'  }
    );

    const ruido = +(d.ruido)||0;
    setCardStatus('badge-ruido',
      ruido > 40 ? { text:'🔊 Alto',   cls:'mb-red' }
    : ruido > 30 ? { text:'⚡ Medio',  cls:'mb-yellow' }
    :              { text:'🔇 Bajo',   cls:'mb-green' }
    );

    setCardStatus('badge-mov',
      +d.movimiento ? { text:'⚡ Detectado', cls:'mb-yellow' }
    :                 { text:'✅ Estable',   cls:'mb-green'  }
    );

    const q = classifyICS(d.indice_sueno);
    setCardStatus('badge-ics', { text: q.label, cls: q.mb });

    const qEl = $('ics-quality');
    if (qEl) { qEl.textContent = q.label; qEl.className = `ics-quality ${q.cls}`; }

    // Timestamp
    setText('last-update', 'Actualizado: ' + fmtTime(d.fecha));

    // Gauges
    updateGauges(d);

    // Recomendaciones
    updateRecommendations(d);

  } catch { /* status already set to offline */ }
}

function setCardStatus(id, { text, cls }) {
  const el = $(id);
  if (!el) return;
  el.textContent = text;
  el.className = `mc-badge ${cls}`;
}

// ─────────────────────────────────────────────────────────────────
//  6. FETCH HISTORIAL
// ─────────────────────────────────────────────────────────────────
async function fetchHistorial() {
  try {
    const limit = $('sel-limit')?.value || 50;
    const fi    = $('fecha-inicio')?.value || '';
    const ff    = $('fecha-fin')?.value    || '';

    let path = `/lecturas?limit=${limit}`;
    if (fi) path += `&fecha_inicio=${fi}`;
    if (ff) path += `&fecha_fin=${ff}`;

    const data = await apiFetch(path);

    // Actualiza tabla en página historial
    renderHistorialTable(data);

    // Actualiza gráficas de líneas
    updateCharts(data);

    // Actualiza barras
    updateBarChart(data);

    setText('total-lecturas', data.length);

    window._historialData = data; // para export CSV

  } catch (err) { console.error('[fetchHistorial]', err); }
}

function renderHistorialTable(data) {
  const tbody = $('historial-tbody');
  if (!tbody) return;

  if (!data?.length) {
    tbody.innerHTML = `<tr><td colspan="7" class="text-center py-5 text-muted">
      <i class="bi bi-inbox fs-2 d-block mb-2"></i>Sin registros disponibles</td></tr>`;
    return;
  }

  tbody.innerHTML = data.slice(0, 100).map(r => {
    const q = classifyICS(r.indice_sueno);
    return `<tr>
      <td class="td-mono">${fmtDate(r.fecha)}</td>
      <td class="td-mono text-danger">${(+(r.temperatura)||0).toFixed(1)}°C</td>
      <td class="td-mono" style="color:var(--blue)">${(+(r.humedad)||0).toFixed(1)}%</td>
      <td class="td-mono text-warning">${(+(r.ruido)||0).toFixed(0)} dB</td>
      <td>${+r.movimiento
        ? '<span class="badge bg-warning text-dark">⚡ Sí</span>'
        : '<span class="badge bg-success">✅ No</span>'}</td>
      <td class="td-mono">${(+(r.indice_sueno)||0).toFixed(0)}</td>
      <td><span class="badge bg-${q.badge}">${q.label}</span></td>
    </tr>`;
  }).join('');
}

// Export CSV
function exportCSV() {
  const data = window._historialData || [];
  if (!data.length) { showFlash('No hay datos para exportar.', 'warning'); return; }

  const header = ['Fecha','Temperatura','Humedad','Ruido','Movimiento','ICS','Calidad'];
  const rows   = data.map(r => [
    r.fecha, r.temperatura, r.humedad, r.ruido,
    +r.movimiento ? 'Sí' : 'No', r.indice_sueno,
    classifyICS(r.indice_sueno).label,
  ]);

  const csv = [header, ...rows].map(r => r.join(',')).join('\n');
  const a   = document.createElement('a');
  a.href    = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csv);
  a.download = `sueno-smart-${new Date().toISOString().slice(0,10)}.csv`;
  a.click();
  showFlash('✅ CSV exportado correctamente.', 'success');
}

// ─────────────────────────────────────────────────────────────────
//  7. FETCH ALERTAS
// ─────────────────────────────────────────────────────────────────
async function fetchAlertas() {
  try {
    const [alertas, resumen] = await Promise.all([
      apiFetch('/alertas?limit=100'),
      apiFetch('/alertas/resumen'),
    ]);

    renderAlertas(alertas);

    // Contadores
    setText('cnt-alto',  resumen.alto  || 0);
    setText('cnt-medio', resumen.medio || 0);
    setText('cnt-bajo',  resumen.bajo  || 0);

  } catch (err) { console.error('[fetchAlertas]', err); }
}

function renderAlertas(data, filtro = 'todos') {
  const el = $('alertas-list');
  if (!el) return;

  const filtered = filtro === 'todos' ? data : data.filter(a => a.nivel === filtro);

  if (!filtered?.length) {
    el.innerHTML = `<div class="text-center py-5 text-muted">
      <i class="bi bi-check-circle fs-2 d-block mb-2" style="color:var(--green)"></i>
      <strong>Sin alertas en este nivel</strong></div>`;
    return;
  }

  const icons = { alto:'🚨', medio:'⚠️', bajo:'ℹ️' };
  el.innerHTML = filtered.map(a => `
    <div class="alert-item nivel-${a.nivel}">
      <span class="ai-icon">${icons[a.nivel] || '📢'}</span>
      <div class="ai-body">
        <p class="ai-msg">${a.mensaje}</p>
        <span class="ai-meta">${fmtDate(a.fecha)}</span>
      </div>
      <span class="badge bg-${a.nivel==='alto'?'danger':a.nivel==='medio'?'warning text-dark':'info'} ai-badge">
        ${a.nivel.toUpperCase()}
      </span>
    </div>`
  ).join('');

  window._alertasData = data;
}

// ─────────────────────────────────────────────────────────────────
//  8. FETCH ESTADÍSTICAS
// ─────────────────────────────────────────────────────────────────
async function fetchEstadisticas() {
  try {
    const dias = $('sel-dias')?.value || 7;
    const [prom, semana, hoy] = await Promise.all([
      apiFetch('/promedios'),
      apiFetch(`/estadisticas/semana?dias=${dias}`),
      apiFetch('/estadisticas/hoy'),
    ]);

    // Stat boxes
    setText('stat-temp',   (+(prom.temp_promedio)||0).toFixed(1) + '°C');
    setText('stat-hum',    (+(prom.humedad_promedio)||0).toFixed(1) + '%');
    setText('stat-ruido',  (+(prom.ruido_promedio)||0).toFixed(0) + ' dB');
    setText('stat-ics',    (+(prom.indice_promedio)||0).toFixed(0));
    setText('stat-tmax',   (+(prom.temp_max)||0).toFixed(1));
    setText('stat-rmax',   (+(prom.ruido_max)||0).toFixed(0));
    setText('stat-total',  prom.total_lecturas || 0);

    // Hoy
    setText('hoy-ics',    (+(hoy.indice_promedio)||0).toFixed(0));
    setText('hoy-total',  hoy.total_lecturas || 0);
    setText('hoy-movs',   hoy.total_movimientos || 0);

    const qHoy = classifyICS(hoy.indice_promedio);
    setText('hoy-calidad', qHoy.label);

    // Gráfica semanal
    updateWeeklyChart(semana);

  } catch (err) { console.error('[fetchEstadisticas]', err); }
}

// ─────────────────────────────────────────────────────────────────
//  RECOMENDACIONES INTELIGENTES
// ─────────────────────────────────────────────────────────────────
function updateRecommendations(d) {
  const el = $('recommendations');
  if (!el) return;

  const temp  = +(d.temperatura)||0;
  const hum   = +(d.humedad)||0;
  const ruido = +(d.ruido)||0;
  const ics   = +(d.indice_sueno)||0;
  const mov   = +(d.movimiento)||0;

  const recos = [];

  if (temp > 26)
    recos.push({ icon:'🌡️', title:'Temperatura elevada', text:'La temperatura supera los 26°C. Considera ventilar el cuarto o bajar el termostato para mejorar el descanso.', type:'reco-warning' });
  else if (temp < 17)
    recos.push({ icon:'❄️', title:'Temperatura baja', text:'La temperatura está por debajo de 17°C. Agrega ropa de cama o ajusta la calefacción para un sueño óptimo.', type:'reco-info' });
  else
    recos.push({ icon:'✅', title:'Temperatura ideal', text:'La temperatura está en el rango óptimo para dormir (18–22°C). ¡Excelente!', type:'reco-success' });

  if (hum > 65)
    recos.push({ icon:'💧', title:'Humedad fuera de rango', text:'Humedad por encima del 65%. Un deshumidificador puede mejorar las condiciones del cuarto.', type:'reco-warning' });
  else if (hum < 35)
    recos.push({ icon:'🏜️', title:'Ambiente seco', text:'La humedad está por debajo del 35%. Considera un humidificador para evitar sequedad.', type:'reco-warning' });
  else
    recos.push({ icon:'✅', title:'Humedad óptima', text:'La humedad se encuentra en el rango ideal (40–60%). Condiciones perfectas para descansar.', type:'reco-success' });

  if (ruido > 40)
    recos.push({ icon:'🔊', title:'Exceso de ruido', text:`Ruido detectado: ${ruido} dB. El ruido nocturno dificulta el sueño profundo. Considera tapones o una máquina de ruido blanco.`, type:'reco-danger' });
  else
    recos.push({ icon:'🔇', title:'Nivel de ruido aceptable', text:'El ambiente está suficientemente silencioso para un sueño reparador.', type:'reco-success' });

  if (mov)
    recos.push({ icon:'🌀', title:'Movimiento nocturno detectado', text:'Se registraron movimientos durante el descanso. Puede indicar sueño inquieto o interrupciones.', type:'reco-warning' });

  if (ics < 60)
    recos.push({ icon:'⚠️', title:'Calidad del sueño deficiente', text:`El ICS actual es ${ics}/100. Revisa las condiciones ambientales y considera mejorar tu rutina de sueño.`, type:'reco-danger' });
  else if (ics >= 80)
    recos.push({ icon:'🌙', title:'Excelente calidad de sueño', text:`ICS de ${ics}/100. ¡Las condiciones de descanso son óptimas! Mantén esta rutina.`, type:'reco-success' });

  el.innerHTML = recos.map(r => `
    <div class="col-lg-4 col-md-6 mb-3">
      <div class="reco-card ${r.type}">
        <span class="reco-icon">${r.icon}</span>
        <div class="reco-body">
          <p class="reco-title">${r.title}</p>
          <p class="reco-text">${r.text}</p>
        </div>
      </div>
    </div>`
  ).join('');
}

// ── Real-time clock ──────────────────────────────────────────────
function updateDatetime() {
  const el = $('headerDatetime');
  if (!el) return;
  el.textContent = new Date().toLocaleString('es-MX', {
    weekday:'long', year:'numeric', month:'long', day:'numeric',
    hour:'2-digit', minute:'2-digit', second:'2-digit',
  });
}

// ── Config form ──────────────────────────────────────────────────
async function initConfigForm() {
  const form = $('config-form');
  if (!form) return;

  // Load current values
  try {
    const cfg = await apiFetch('/config');
    const map = { 'cfg-temp-max':'temperatura_max','cfg-temp-min':'temperatura_min','cfg-hum-max':'humedad_max','cfg-hum-min':'humedad_min','cfg-ruido-max':'ruido_max' };
    Object.entries(map).forEach(([id, key]) => {
      const e = $(id); if (e) e.value = cfg[key] ?? '';
    });
  } catch {}

  form.addEventListener('submit', async e => {
    e.preventDefault();
    const btn = form.querySelector('[type="submit"]');
    btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Guardando…';

    try {
      const payload = {};
      [['cfg-temp-max','temperatura_max'],['cfg-temp-min','temperatura_min'],
       ['cfg-hum-max','humedad_max'],['cfg-hum-min','humedad_min'],['cfg-ruido-max','ruido_max']].forEach(([id,key]) => {
        const e = $(id); if (e) payload[key] = +e.value;
      });

      const res = await fetch(`${API_BASE}/config/umbrales`, {
        method:'POST', headers:{ 'Content-Type':'application/json' }, body:JSON.stringify(payload),
      });

      if (res.ok) showFlash('✅ Configuración guardada correctamente.', 'success');
      else throw new Error();
    } catch { showFlash('❌ Error al guardar la configuración.', 'danger'); }
    finally { btn.disabled = false; btn.innerHTML = '<i class="bi bi-check-lg me-2"></i>Guardar cambios'; }
  });
}

// ── Flash messages ───────────────────────────────────────────────
function showFlash(msg, type = 'info') {
  let c = $('flash-messages');
  if (!c) { c = document.createElement('div'); c.id = 'flash-messages'; document.body.appendChild(c); }
  const a = document.createElement('div');
  a.className = `alert alert-${type} alert-dismissible fade show shadow`;
  a.innerHTML = `${msg}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
  c.appendChild(a);
  setTimeout(() => { try { bootstrap.Alert.getOrCreateInstance(a).close(); } catch{ a.remove(); } }, 4000);
}

// ── Sidebar toggle ───────────────────────────────────────────────
function initSidebar() {
  const btn  = $('sidebarToggle');
  const side = $('appSidebar');
  if (!btn || !side) return;

  btn.addEventListener('click', () => {
    if (window.innerWidth < 1024) {
      side.classList.toggle('mobile-open');
    } else {
      side.classList.toggle('collapsed');
      const wrapper = document.querySelector('.main-wrapper');
      if (wrapper) wrapper.style.marginLeft = side.classList.contains('collapsed') ? '70px' : 'var(--sidebar-w)';
    }
  });

  // Close on outside click (mobile)
  document.addEventListener('click', e => {
    if (window.innerWidth < 1024 && !side.contains(e.target) && !btn.contains(e.target)) {
      side.classList.remove('mobile-open');
    }
  });
}

// ── Filter for historial ─────────────────────────────────────────
function initHistorialFilters() {
  const btnFilter = $('btn-filter');
  if (btnFilter) btnFilter.addEventListener('click', fetchHistorial);

  const btnCSV = $('btn-export-csv');
  if (btnCSV) btnCSV.addEventListener('click', exportCSV);

  const selLimit = $('sel-limit');
  if (selLimit) selLimit.addEventListener('change', fetchHistorial);
}

// ── Alert filters ────────────────────────────────────────────────
function initAlertFilters() {
  const sel = $('sel-nivel');
  if (!sel) return;
  sel.addEventListener('change', () => {
    const data = window._alertasData || [];
    renderAlertas(data, sel.value);
  });

  const btnLimpiar = $('btn-limpiar-alertas');
  if (btnLimpiar) {
    btnLimpiar.addEventListener('click', async () => {
      if (!confirm('¿Eliminar alertas antiguas (>30 días)?')) return;
      try {
        await fetch(`${API_BASE}/alertas/limpiar`, { method:'DELETE' });
        showFlash('Alertas antiguas eliminadas.', 'success');
        fetchAlertas();
      } catch { showFlash('Error al limpiar.', 'danger'); }
    });
  }
}

// ─────────────────────────────────────────────────────────────────
//  8. ACTUALIZACIÓN AUTOMÁTICA (cada 5 segundos)
// ─────────────────────────────────────────────────────────────────
function startAutoUpdate() {
  fetchUltimo();
  fetchAlertas();

  setInterval(fetchUltimo, UPDATE_MS);

  // Historial y estadísticas cada 30 segundos
  setInterval(() => {
    fetchHistorial();
    fetchEstadisticas();
  }, HISTORY_MS);
}

// ─────────────────────────────────────────────────────────────────
//  9. RESIZE DE GRÁFICAS
// ─────────────────────────────────────────────────────────────────
function handleResize() {
  Object.values(CI).forEach(c => c?.resize?.());
}

// ─────────────────────────────────────────────────────────────────
//  MAIN — DOMContentLoaded
// ─────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {

  // 1. Inicialización de gráficas
  initGauges();

  // 2. Configuración de gauges  (ya hecha dentro de initGauges)

  // 3. Configuración de barras
  initBarChart();

  // 4. Configuración de líneas
  initLineCharts();

  // 5. Configuración semanal
  initWeeklyChart();

  // 6. Sidebar
  initSidebar();

  // 7. Clock
  updateDatetime();
  setInterval(updateDatetime, 1000);

  // 8. Config form
  initConfigForm();

  // 9. Filters
  initHistorialFilters();
  initAlertFilters();

  // Estadísticas selector de días
  const selDias = $('sel-dias');
  if (selDias) selDias.addEventListener('change', fetchEstadisticas);

  // 10. Initial fetches
  fetchHistorial();
  fetchEstadisticas();

  // 11. Actualización automática cada 5 segundos
  startAutoUpdate();

  // 12. Resize de gráficas
  window.addEventListener('resize', handleResize);
});