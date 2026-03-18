<?php
?><!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($title ?? 'Admin'); ?> - TJA Chiapas</title>
    <link rel="icon" type="image/svg+xml" href="<?php echo base_url('assets/img/favicon.svg'); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="<?php echo base_url('assets/css/main.css?v=12'); ?>" rel="stylesheet">
    <meta name="csrf-token" content="<?php echo htmlspecialchars($csrf ?? ''); ?>">
</head>
<body class="tja-body" data-base="<?php echo base_url(''); ?>">
    <div id="tja-loader" class="tja-loader d-none">
        <div class="tja-loader-box">
            <div class="spinner-border text-primary" role="status"></div>
            <div class="mt-2">Procesando...</div>
        </div>
    </div>
    <header class="tja-topbar">
        <div class="container d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <img src="<?php echo base_url('assets/img/logo-tja.png'); ?>" alt="TJA Chiapas" class="tja-logo tja-logo-sm">
                <div>
                    <div class="tja-title">Panel Administrativo</div>
                    <div class="tja-subtitle">Sistema de Verificacion</div>
                </div>
            </div>
            <nav class="tja-nav">
                <a href="<?php echo base_url('admin'); ?>"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
                <?php if (\app\Core\Auth::can('manage_courses') || \app\Core\Auth::can('view_courses')): ?>
                    <a href="<?php echo base_url('admin/courses'); ?>"><i class="fa-solid fa-school"></i> Cursos</a>
                <?php endif; ?>
                <?php if (\app\Core\Auth::can('manage_participants') || \app\Core\Auth::can('view_participants')): ?>
                    <a href="<?php echo base_url('admin/participants'); ?>"><i class="fa-solid fa-users"></i> Participantes</a>
                <?php endif; ?>
                <?php if (\app\Core\Auth::can('manage_certificates') || \app\Core\Auth::can('view_certificates')): ?>
                    <a href="<?php echo base_url('admin/certificates'); ?>"><i class="fa-solid fa-file-circle-check"></i> Constancias</a>
                <?php endif; ?>
                <?php if (\app\Core\Auth::can('view_audit')): ?>
                    <a href="<?php echo base_url('admin/audit'); ?>"><i class="fa-solid fa-clipboard-list"></i> Auditoria</a>
                <?php endif; ?>
                <?php if (\app\Core\Auth::can('manage_users')): ?>
                    <a href="<?php echo base_url('admin/users'); ?>"><i class="fa-solid fa-user-gear"></i> Usuarios</a>
                <?php endif; ?>
            </nav>
            <div class="tja-user">
                <i class="fa-solid fa-circle-user"></i>
                <span><?php echo htmlspecialchars(\app\Core\Auth::user()['name'] ?? 'Usuario'); ?></span>
                <a class="tja-logout" href="<?php echo base_url('admin/logout'); ?>"><i class="fa-solid fa-right-from-bracket"></i> Salir</a>
            </div>
        </div>
    </header>

    <main class="tja-main" data-page="<?php echo htmlspecialchars($title ?? ''); ?>">
        <?php echo $content; ?>
    </main>

    <footer class="tja-footer">
        <div class="container d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <img src="<?php echo base_url('assets/img/logo-tja.png'); ?>" alt="TJA Chiapas" class="tja-logo">
                <div>
                    <div class="tja-footer-title">TJA Chiapas</div>
                    <div class="tja-footer-subtitle">Uso interno del Tribunal</div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <img src="<?php echo base_url('assets/img/justicia-humanismo.png'); ?>" alt="Justicia con Humanismo" class="tja-logo">
                <div class="tja-footer-legend">Justicia con Humanismo</div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?php echo base_url('assets/js/admin.js?v=7'); ?>"></script>
</body>
</html>