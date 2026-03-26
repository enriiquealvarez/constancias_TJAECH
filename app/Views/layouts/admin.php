<?php
?><!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'Admin'); ?> - TJA Chiapas</title>
    <link rel="icon" type="image/svg+xml" href="<?php echo base_url('assets/img/favicon.svg'); ?>">
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link href="<?php echo base_url('assets/css/main.css?v=' . time()); ?>" rel="stylesheet">
    <meta name="csrf-token" content="<?php echo htmlspecialchars($csrf ?? ''); ?>">
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#111426",
                        "corporate-blue": "#1b456f",
                        "accent": "#C4AF6B",
                        "background-light": "#F3F6FB",
                        "background-dark": "#13191f",
                        "success": "#2D6A4F",
                    },
                    fontFamily: {
                        "display": ["Public Sans", "sans-serif"]
                    },
                    borderRadius: { "DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px" },
                },
            },
        }
    </script>
    <style>
        body { font-family: 'Public Sans', sans-serif; }
        .sidebar-active { border-left: 4px solid #C4AF6B; background-color: rgba(196, 175, 107, 0.1); }
        .sidebar-hidden { transform: translateX(-100%); transition: transform 0.3s ease-in-out; }
        .sidebar-visible { transform: translateX(0); transition: transform 0.3s ease-in-out; }
        @media (min-width: 1024px) {
            .sidebar-hidden { transform: translateX(0); }
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100 overflow-x-hidden" data-base="<?php echo base_url(''); ?>">
    <div id="tja-loader" class="fixed inset-0 bg-white/80 z-[100] hidden items-center justify-center">
        <div class="bg-white p-6 rounded-xl shadow-xl border border-slate-100 flex flex-col items-center">
            <span class="material-symbols-outlined animate-spin text-corporate-blue text-4xl mb-2">refresh</span>
            <div class="text-sm font-bold text-slate-600">Procesando...</div>
        </div>
    </div>
    
    <div class="flex h-screen overflow-hidden relative">
        <!-- Sidebar -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-primary text-white flex flex-col shrink-0 shadow-xl sidebar-hidden lg:relative lg:translate-x-0 transition-transform duration-300">
            <div class="bg-white h-24 flex items-center justify-center border-b-[3px] border-accent relative px-6 shrink-0 w-full shadow-sm">
                <img src="<?php echo base_url('assets/img/logo-tja.png'); ?>" alt="TJAECH" class="h-[60px] w-auto object-contain transition-transform hover:scale-105 duration-300">
                <button class="lg:hidden text-slate-400 absolute right-4 top-1/2 -translate-y-1/2 bg-slate-100 hover:bg-slate-200 rounded p-1.5 transition-colors" onclick="toggleSidebar()">
                    <span class="material-symbols-outlined text-lg">close</span>
                </button>
            </div>
            <nav class="flex-1 px-0 mt-4 overflow-y-auto">
                <div class="px-6 py-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Menú Principal</div>
                
                <?php $current = htmlspecialchars($title ?? ''); ?>

                <a href="<?php echo base_url('admin'); ?>" class="flex items-center gap-4 px-6 py-3 <?php echo ($current === 'Dashboard') ? 'text-accent sidebar-active bg-white/5' : 'text-slate-300 hover:bg-white/5'; ?> transition-colors">
                    <span class="material-symbols-outlined text-[20px]">dashboard</span>
                    <span class="text-sm <?php echo ($current === 'Dashboard') ? 'font-bold' : 'font-medium'; ?>">Dashboard</span>
                </a>
                
                <?php if (\app\Core\Auth::can('manage_courses') || \app\Core\Auth::can('view_courses')): ?>
                <a href="<?php echo base_url('admin/courses'); ?>" class="flex items-center gap-4 px-6 py-3 <?php echo ($current === 'Cursos') ? 'text-accent sidebar-active bg-white/5' : 'text-slate-300 hover:bg-white/5'; ?> transition-colors">
                    <span class="material-symbols-outlined text-[20px]">menu_book</span>
                    <span class="text-sm <?php echo ($current === 'Cursos') ? 'font-bold' : 'font-medium'; ?>">Cursos</span>
                </a>
                <?php endif; ?>

                <?php if (\app\Core\Auth::can('manage_participants') || \app\Core\Auth::can('view_participants')): ?>
                <a href="<?php echo base_url('admin/participants'); ?>" class="flex items-center gap-4 px-6 py-3 <?php echo ($current === 'Participantes') ? 'text-accent sidebar-active bg-white/5' : 'text-slate-300 hover:bg-white/5'; ?> transition-colors">
                    <span class="material-symbols-outlined text-[20px]">group</span>
                    <span class="text-sm <?php echo ($current === 'Participantes') ? 'font-bold' : 'font-medium'; ?>">Participantes</span>
                </a>
                <?php endif; ?>

                <?php if (\app\Core\Auth::can('manage_certificates') || \app\Core\Auth::can('view_certificates')): ?>
                <a href="<?php echo base_url('admin/certificates'); ?>" class="flex items-center gap-4 px-6 py-3 <?php echo ($current === 'Constancias') ? 'text-accent sidebar-active bg-white/5' : 'text-slate-300 hover:bg-white/5'; ?> transition-colors">
                    <span class="material-symbols-outlined text-[20px]">workspace_premium</span>
                    <span class="text-sm <?php echo ($current === 'Constancias') ? 'font-bold' : 'font-medium'; ?>">Constancias</span>
                </a>
                <?php endif; ?>

                <?php if (\app\Core\Auth::can('view_audit')): ?>
                <a href="<?php echo base_url('admin/audit'); ?>" class="flex items-center gap-4 px-6 py-3 <?php echo ($current === 'Auditoria') ? 'text-accent sidebar-active bg-white/5' : 'text-slate-300 hover:bg-white/5'; ?> transition-colors">
                    <span class="material-symbols-outlined text-[20px]">policy</span>
                    <span class="text-sm <?php echo ($current === 'Auditoria') ? 'font-bold' : 'font-medium'; ?>">Auditoría</span>
                </a>
                <?php endif; ?>

                <?php if (\app\Core\Auth::can('manage_users')): ?>
                <a href="<?php echo base_url('admin/users'); ?>" class="flex items-center gap-4 px-6 py-3 <?php echo ($current === 'Usuarios') ? 'text-accent sidebar-active bg-white/5' : 'text-slate-300 hover:bg-white/5'; ?> transition-colors">
                    <span class="material-symbols-outlined text-[20px]">manage_accounts</span>
                    <span class="text-sm <?php echo ($current === 'Usuarios') ? 'font-bold' : 'font-medium'; ?>">Usuarios</span>
                </a>
                <?php endif; ?>
            </nav>
            <div class="p-6 border-t border-white/10">
                <div class="flex items-center gap-3 p-2 rounded-lg bg-white/5">
                    <div class="w-8 h-8 rounded-full bg-accent/20 flex items-center justify-center text-accent">
                        <span class="material-symbols-outlined text-[18px]">person</span>
                    </div>
                    <div class="flex flex-col overflow-hidden">
                        <span class="text-xs font-bold truncate"><?php echo htmlspecialchars(\app\Core\Auth::user()['name'] ?? 'Usuario'); ?></span>
                        <span class="text-[10px] text-slate-400 truncate"><?php echo htmlspecialchars(\app\Core\Auth::user()['email'] ?? ''); ?></span>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col overflow-hidden" data-page="<?php echo htmlspecialchars($title ?? ''); ?>">
            <!-- Topbar -->
            <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 lg:px-8 shrink-0">
                <div class="flex items-center gap-3">
                    <button class="lg:hidden text-primary" onclick="toggleSidebar()">
                        <span class="material-symbols-outlined text-[28px]">menu</span>
                    </button>
                    <h1 class="text-base lg:text-lg font-bold text-primary truncate"><?php echo htmlspecialchars($title ?? 'Admin'); ?></h1>
                    <span class="hidden sm:inline-block text-[10px] bg-slate-100 text-slate-500 px-2 py-0.5 rounded border border-slate-200">TJAECH</span>
                </div>
                <div class="flex items-center gap-3 lg:gap-6">
                    <a href="<?php echo base_url('/'); ?>" target="_blank" class="flex items-center gap-2 text-slate-500 hover:text-primary cursor-pointer transition-colors" title="Ver portal público">
                        <span class="material-symbols-outlined text-[22px]">language</span>
                    </a>
                    <div class="h-6 w-px bg-slate-200 hidden sm:block"></div>
                    <a href="<?php echo base_url('admin/logout'); ?>" class="flex items-center gap-1 text-slate-600 hover:text-red-600 font-medium text-sm transition-colors">
                        <span class="hidden sm:inline">Cerrar Sesión</span>
                        <span class="material-symbols-outlined text-[20px]">logout</span>
                    </a>
                </div>
            </header>

            <!-- Page Content -->
            <div class="flex-1 overflow-y-auto p-4 lg:p-8 bg-background-light">
                <?php echo $content; ?>
            </div>
        </main>

        <!-- Mobile Navigation Bottom Bar -->
        <div class="lg:hidden fixed bottom-0 left-0 right-0 bg-primary h-16 flex items-center justify-around border-t border-white/10 px-2 z-40">
            <a href="<?php echo base_url('admin'); ?>" class="flex flex-col items-center gap-1 <?php echo ($current === 'Dashboard') ? 'text-accent' : 'text-slate-400'; ?>">
                <span class="material-symbols-outlined text-[20px]">dashboard</span>
                <span class="text-[9px] font-bold uppercase">Inicio</span>
            </a>
            <a href="<?php echo base_url('admin/courses'); ?>" class="flex flex-col items-center gap-1 <?php echo ($current === 'Cursos') ? 'text-accent' : 'text-slate-400'; ?>">
                <span class="material-symbols-outlined text-[20px]">menu_book</span>
                <span class="text-[9px] font-bold uppercase">Cursos</span>
            </a>
            <a href="<?php echo base_url('admin/certificates'); ?>" class="flex flex-col items-center gap-1 <?php echo ($current === 'Constancias') ? 'text-accent' : 'text-slate-400'; ?>">
                <span class="material-symbols-outlined text-[24px]">workspace_premium</span>
                <span class="text-[9px] font-bold uppercase">Const.</span>
            </a>
            <a href="<?php echo base_url('admin/participants'); ?>" class="flex flex-col items-center gap-1 <?php echo ($current === 'Participantes') ? 'text-accent' : 'text-slate-400'; ?>">
                <span class="material-symbols-outlined text-[20px]">group</span>
                <span class="text-[9px] font-bold uppercase">Personas</span>
            </a>
            <button class="flex flex-col items-center gap-1 text-slate-400" onclick="toggleSidebar()">
                <span class="material-symbols-outlined text-[20px]">menu</span>
                <span class="text-[9px] font-bold uppercase">Menú</span>
            </button>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- admin.js maintains functionality (fetches, table building). We will adapt it slightly if needed -->
    <script src="<?php echo base_url('assets/js/admin.js?v=' . time()); ?>"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            if (sidebar.classList.contains('sidebar-hidden')) {
                sidebar.classList.remove('sidebar-hidden');
            } else {
                sidebar.classList.add('sidebar-hidden');
            }
        }
        // Expose a show/hide loader function for SweetAlert or logic
        window.tjaLoader = {
            show: () => document.getElementById('tja-loader').style.display = 'flex',
            hide: () => document.getElementById('tja-loader').style.display = 'none'
        };
    </script>
</body>
</html>