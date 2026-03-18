<?php
?><!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($title ?? 'Sistema de Verificacion'); ?> - TJA Chiapas</title>
    <link rel="icon" type="image/svg+xml" href="<?php echo base_url('assets/img/favicon.svg'); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="<?php echo base_url('assets/css/main.css?v=12'); ?>" rel="stylesheet">
</head>
<body class="tja-body">
    <header class="tja-topbar">
        <div class="container d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <img src="<?php echo base_url('assets/img/logo-tja.png'); ?>" alt="TJA Chiapas" class="tja-logo tja-logo-sm">
                <div>
                    <div class="tja-title">Tribunal de Justicia Administrativa</div>
                    <div class="tja-subtitle">Estado de Chiapas</div>
                </div>
            </div>
            <nav class="tja-nav">
                <a href="<?php echo base_url('/'); ?>"><i class="fa-solid fa-house"></i> Inicio</a>
                <a href="<?php echo base_url('verificar'); ?>"><i class="fa-solid fa-qrcode"></i> Verificar</a>
                <?php if (\app\Core\Auth::check()): ?>
                    <a href="<?php echo base_url('admin'); ?>"><i class="fa-solid fa-shield-halved"></i> Admin</a>
                    <a href="<?php echo base_url('admin/logout'); ?>"><i class="fa-solid fa-right-from-bracket"></i> Salir</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="tja-main">
        <?php echo $content; ?>
    </main>

    <footer class="tja-footer">
        <div class="container d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <img src="<?php echo base_url('assets/img/logo-tja.png'); ?>" alt="TJA Chiapas" class="tja-logo">
                <div>
                    <div class="tja-footer-title">TJA Chiapas</div>
                    <div class="tja-footer-subtitle">Sistema de Verificacion de Constancias</div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <img src="<?php echo base_url('assets/img/justicia-humanismo.png'); ?>" alt="Justicia con Humanismo" class="tja-logo">
                <div class="tja-footer-legend">Justicia con Humanismo</div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
