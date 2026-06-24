<?= $this->extend('layouts/header') ?>

<?= $this->section('content') ?>

<!-- ========================================== -->
<!-- ACERCA DE                                  -->
<!-- ========================================== -->
<div class="section-hd">
    <h2><i class="bi bi-info-circle me-1"></i> Acerca de SueñoSmart IoT</h2>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="iot-card">
            <div class="iot-card-body">
                <div class="text-center mb-4">
                    <div class="brand-orb mx-auto" style="width:80px;height:80px;font-size:36px;">
                        <i class="bi bi-moon-stars-fill"></i>
                    </div>
                    <h3 class="mt-3 text-white">SueñoSmart IoT</h3>
                    <p class="text-secondary">Sistema Inteligente de Monitoreo del Sueño</p>
                </div>

                <hr class="border-secondary border-opacity-25">

                <h5 class="text-white">📖 Descripción del Proyecto</h5>
                <p class="text-secondary">
                    SueñoSmart IoT es un sistema basado en Internet de las Cosas (IoT) diseñado para 
                    monitorear las condiciones ambientales durante el descanso nocturno y evaluar la 
                    calidad del sueño.
                </p>

                <h5 class="text-white mt-4">🎯 Objetivos</h5>
                <ul class="text-secondary">
                    <li>Medir temperatura y humedad ambiental mediante sensores.</li>
                    <li>Detectar niveles de ruido durante la noche.</li>
                    <li>Registrar movimientos corporales nocturnos.</li>
                    <li>Calcular un Índice de Calidad del Sueño (ICS).</li>
                    <li>Almacenar los datos en una base de datos.</li>
                    <li>Mostrar información mediante un dashboard web.</li>
                    <li>Generar alertas y recomendaciones automáticas.</li>
                </ul>

                <h5 class="text-white mt-4">🛠️ Tecnologías Utilizadas</h5>
                <div class="row g-2">
                    <div class="col-md-6">
                        <div class="stat-box">
                            <div class="stat-lbl">Backend</div>
                            <div class="stat-val" style="font-size:16px;">PHP 8 + CodeIgniter 4</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="stat-box">
                            <div class="stat-lbl">Frontend</div>
                            <div class="stat-val" style="font-size:16px;">Bootstrap 5 + ECharts</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="stat-box">
                            <div class="stat-lbl">Base de Datos</div>
                            <div class="stat-val" style="font-size:16px;">MySQL</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="stat-box">
                            <div class="stat-lbl">Hardware</div>
                            <div class="stat-val" style="font-size:16px;">ESP32 + Sensores IoT</div>
                        </div>
                    </div>
                </div>

                <h5 class="text-white mt-4">📡 Sensores</h5>
                <div class="row g-2">
                    <div class="col-md-4">
                        <div class="stat-box" style="border-color:var(--red);">
                            <div class="stat-lbl">DHT11</div>
                            <div class="stat-val" style="font-size:14px;color:var(--red);">Temperatura / Humedad</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-box" style="border-color:var(--yellow);">
                            <div class="stat-lbl">KY-038</div>
                            <div class="stat-val" style="font-size:14px;color:var(--yellow);">Ruido</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-box" style="border-color:var(--green);">
                            <div class="stat-lbl">SW-420</div>
                            <div class="stat-val" style="font-size:14px;color:var(--green);">Movimiento</div>
                        </div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- REPOSITORIO EN GITHUB (DESTACADO)          -->
                <!-- ========================================== -->
                <div class="mt-4 p-3" style="background:var(--card-2);border-radius:var(--radius-sm);border:1px solid var(--border);">
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <div class="brand-orb" style="width:48px;height:48px;font-size:20px;background:linear-gradient(135deg,#333,#24292f);">
                            <i class="bi bi-github"></i>
                        </div>
                        <div>
                            <h6 class="text-white mb-0">Repositorio del proyecto</h6>
                            <a href="<?= $repo_url ?? '#' ?>" 
                               target="_blank" 
                               class="text-decoration-none" 
                               style="color:var(--accent-l);font-size:13px;"
                               id="repo-link">
                                <?= $repo_url ?? 'https://github.com/tu-usuario/sueno-smart-iot' ?>
                            </a>
                        </div>
                        <div class="d-flex gap-2 ms-auto">
                            <button class="btn btn-iot btn-iot-ghost btn-sm" 
                                    onclick="window.open('<?= $repo_url ?? '#' ?>', '_blank')">
                                <i class="bi bi-box-arrow-up-right"></i> Abrir
                            </button>
                            <button class="btn btn-iot btn-iot-ghost btn-sm" 
                                    onclick="copiarRepositorio()">
                                <i class="bi bi-clipboard"></i> Copiar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="iot-card">
            <div class="iot-card-head">
                <span class="iot-card-title">📋 Información del Proyecto</span>
            </div>
            <div class="iot-card-body">
                <div class="text-secondary small">
                    <p><strong>🏫 Institución:</strong></p>
                    <p><?= $institution ?? 'Tecnológico Nacional de México - ITS Mulegé' ?></p>

                    <hr class="border-secondary border-opacity-25">

                    <p><strong>📚 Materia:</strong></p>
                    <p>Programación para IoT</p>

                    <hr class="border-secondary border-opacity-25">

                    <p><strong>👩‍🎓 Autor:</strong></p>
                    <p><?= $author ?? 'Arianna Aguilar Martínez' ?></p>

                    <hr class="border-secondary border-opacity-25">

                    <p><strong>📅 Fecha:</strong></p>
                    <p><?= date('d/m/Y') ?></p>

                    <hr class="border-secondary border-opacity-25">

                    <p><strong>📦 Versión:</strong></p>
                    <p>v1.0.0</p>

                    <hr class="border-secondary border-opacity-25">

                    <p><strong>🔗 Repositorio:</strong></p>
                    <a href="<?= $repo_url ?? '#' ?>" 
                       target="_blank" 
                       class="text-decoration-none d-flex align-items-center gap-2" 
                       style="color:var(--accent-l);">
                        <i class="bi bi-github fs-5"></i>
                        <span><?= $repo_name ?? 'Ver en GitHub' ?></span>
                    </a>
                    <small class="text-secondary d-block mt-1">
                        <i class="bi bi-star me-1"></i>
                        <span id="repo-stars">⭐</span> 
                        <i class="bi bi-code-slash ms-2 me-1"></i>
                        <span id="repo-lang">PHP</span>
                    </small>
                </div>
            </div>
            <div class="iot-card-foot text-center">
                <i class="bi bi-github me-1"></i>
                <a href="<?= $repo_url ?? '#' ?>" 
                   target="_blank" 
                   class="text-decoration-none text-secondary">
                    Ver repositorio en GitHub
                </a>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- JAVASCRIPT                                 -->
<!-- ========================================== -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('ℹ️ Acerca de - Inicializado');

    // Función para copiar el enlace del repositorio
    window.copiarRepositorio = function() {
        const link = document.getElementById('repo-link')?.textContent || '';
        if (link && link !== '#') {
            navigator.clipboard.writeText(link).then(() => {
                showFlash('✅ Enlace del repositorio copiado al portapapeles.', 'success');
            }).catch(() => {
                // Fallback para navegadores antiguos
                const textarea = document.createElement('textarea');
                textarea.value = link;
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
                showFlash('✅ Enlace del repositorio copiado al portapapeles.', 'success');
            });
        }
    };
});

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
</script>

<?= $this->endSection() ?>