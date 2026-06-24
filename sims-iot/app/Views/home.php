<?= $this->extend('layouts/header') ?>

<?= $this->section('content') ?>

<div class="row justify-content-center align-items-center" style="min-height: 70vh;">
    <div class="col-md-8 col-lg-6 text-center">
        <div class="mb-4">
            <i class="bi bi-moon-stars-fill display-1 text-primary"></i>
        </div>
        <h1 class="display-4 fw-bold text-white mb-3">Sistema de Monitoreo</h1>
        <p class="lead text-secondary mb-4">
            Bienvenido al sistema inteligente para la calidad del sueño.
            Monitorea temperatura, humedad, ruido y movimiento en tiempo real.
        </p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="<?= base_url('dashboard') ?>" class="btn btn-primary btn-lg px-5">
                <i class="bi bi-speedometer2 me-2"></i>Ir al Dashboard
            </a>
            <a href="<?= base_url('acerca') ?>" class="btn btn-outline-secondary btn-lg px-5">
                <i class="bi bi-info-circle me-2"></i>Más información
            </a>
        </div>
        <div class="mt-5">
            <span class="badge bg-dark text-secondary p-2">
                <i class="bi bi-activity me-1"></i> Sistema IoT v1.0.0
            </span>
        </div>
    </div>
</div>

<?= $this->endSection() ?>