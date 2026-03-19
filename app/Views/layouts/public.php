<?php
?><!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'Sistema de Verificacion'); ?> - TJA Chiapas</title>
    <link rel="icon" type="image/svg+xml" href="<?php echo base_url('assets/img/favicon.svg'); ?>">
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700,0..1&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            colors: {
              "primary": "#1b456f",
              "institutional-gold": "#C4AF6B",
              "background-light": "#f6f7f8",
              "background-dark": "#13191f",
              "accent-dark": "#111426",
              "neutral-bg": "#F3F6FB"
            },
            fontFamily: {
              "display": ["Public Sans", "sans-serif"]
            },
            borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
          },
        },
      }
    </script>
    <style>
      body { min-height: max(884px, 100dvh); }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100 min-h-screen flex flex-col">
    <!-- Header Superior Fijo -->
    <header class="fixed top-0 w-full z-50 bg-primary border-b border-primary/20 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center gap-4">
                    <div class="bg-white p-2 rounded-lg">
                        <img src="<?php echo base_url('assets/img/logo-tja.png'); ?>" alt="Logotipo Oficial TJAECH" class="h-10 w-auto">
                    </div>
                    <div class="hidden md:block text-white">
                        <p class="text-xs font-semibold uppercase tracking-wider opacity-80">Poder Judicial</p>
                        <p class="text-sm font-bold">Estado de Chiapas</p>
                    </div>
                </div>
                <nav class="flex items-center gap-6">
                    <a href="<?php echo base_url('/'); ?>" class="text-white hover:text-white/80 transition-colors" title="Inicio">
                        <span class="material-symbols-outlined">home</span>
                    </a>
                    <?php if (\app\Core\Auth::check()): ?>
                        <a href="<?php echo base_url('admin'); ?>" class="text-white hover:text-white/80 transition-colors" title="Admin">
                            <span class="material-symbols-outlined">admin_panel_settings</span>
                        </a>
                        <a href="<?php echo base_url('admin/logout'); ?>" class="text-white hover:text-white/80 transition-colors" title="Salir">
                            <span class="material-symbols-outlined">logout</span>
                        </a>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </header>

    <main class="flex-grow pt-20">
        <?php echo $content; ?>
    </main>

    <!-- Footer -->
    <footer class="bg-accent-dark text-white py-12 px-4">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-start md:items-center gap-10">
            <div class="space-y-4 max-w-sm">
                <!-- Attempt to filter to white for the dark footer if it's not a transparent png, but standard logo normally works -->
                <img src="<?php echo base_url('assets/img/logo-tja.png'); ?>" alt="Logo Footer TJAECH" class="h-12 w-auto opacity-90 grayscale brightness-200">
                <p class="text-slate-400 text-sm leading-relaxed">
                    Tribunal de Justicia Administrativa del Estado de Chiapas. 
                    Garantizando la legalidad y transparencia en la administración pública.
                </p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-12">
                <div>
                    <h4 class="font-bold text-lg mb-4 text-white">Soporte</h4>
                    <ul class="space-y-3 text-slate-400 text-sm">
                        <li><a href="<?php echo base_url('verificar'); ?>" class="hover:text-white transition-colors">Verificar Constancia</a></li>
                        <li><a href="<?php echo base_url('admin/login'); ?>" class="hover:text-white transition-colors">Acceso Administrativo</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-lg mb-4 text-white">Contacto</h4>
                    <ul class="space-y-4 text-slate-400 text-sm leading-relaxed">
                        <li class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-lg mt-0.5 text-institutional-gold">location_on</span>
                            <span>Boulevard Belisario Domínguez 1713, Colonia Xamaipak.<br>Tuxtla Gutiérrez, Chiapas.</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-lg text-institutional-gold">mail</span>
                            <a href="mailto:csocial@tjaech.gob.mx" class="hover:text-white hover:underline transition-all">csocial@tjaech.gob.mx</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="max-w-7xl mx-auto mt-12 pt-8 border-t border-white/10 text-center text-slate-500 text-xs">
            <p>© <?php echo date('Y'); ?> Tribunal de Justicia Administrativa del Estado de Chiapas. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>
