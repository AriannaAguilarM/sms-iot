<!-- ========================================== -->
<!-- SIDEBAR                                    -->
<!-- ========================================== -->
<nav class="app-sidebar" id="appSidebar">
    <div class="sidebar-brand">
        <div class="brand-orb">
            <i class="bi bi-moon-stars-fill"></i>
        </div>
        <div class="sidebar-brand-name">
            <span class="b-title">SueñoSmart</span>
            <span class="b-sub">IoT Sleep Monitor</span>
        </div>
    </div>

    <div class="sidebar-nav">
        <div class="nav-section-label">Principal</div>
        <a href="<?= base_url('dashboard') ?>" class="sidebar-link <?= current_url() == base_url('dashboard') ? 'active' : '' ?>">
            <i class="bi bi-grid-1x2-fill"></i>
            <span class="nav-text">Dashboard</span>
        </a>
        <a href="<?= base_url('historial') ?>" class="sidebar-link <?= current_url() == base_url('historial') ? 'active' : '' ?>">
            <i class="bi bi-clock-history"></i>
            <span class="nav-text">Historial</span>
        </a>
        <a href="<?= base_url('estadisticas') ?>" class="sidebar-link <?= current_url() == base_url('estadisticas') ? 'active' : '' ?>">
            <i class="bi bi-bar-chart-fill"></i>
            <span class="nav-text">Estadísticas</span>
        </a>
        <a href="<?= base_url('alertas') ?>" class="sidebar-link <?= current_url() == base_url('alertas') ? 'active' : '' ?>">
            <i class="bi bi-bell-fill"></i>
            <span class="nav-text">Alertas</span>
        </a>

        <div class="nav-section-label mt-3">Sistema</div>
        <a href="<?= base_url('configuracion') ?>" class="sidebar-link <?= current_url() == base_url('configuracion') ? 'active' : '' ?>">
            <i class="bi bi-sliders2"></i>
            <span class="nav-text">Configuración</span>
        </a>
        <a href="<?= base_url('usuarios') ?>" class="sidebar-link <?= current_url() == base_url('usuarios') ? 'active' : '' ?>">
            <i class="bi bi-people-fill"></i>
            <span class="nav-text">Usuarios</span>
        </a>
        <a href="<?= base_url('acerca') ?>" class="sidebar-link <?= current_url() == base_url('acerca') ? 'active' : '' ?>">
            <i class="bi bi-info-circle"></i>
            <span class="nav-text">Acerca de</span>
        </a>
    </div>

    <div class="sidebar-foot">
        <div class="sys-pill">
            <span class="sys-dot"></span>
            <div class="sys-info">
                <span class="s-label">Sistema activo</span>
                <span class="s-sub">v1.0.0</span>
            </div>
        </div>
    </div>
</nav>