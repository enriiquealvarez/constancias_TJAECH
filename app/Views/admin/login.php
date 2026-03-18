<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TJAECH - Acceso Administrativo</title>
    <link rel="icon" type="image/svg+xml" href="<?php echo base_url('assets/img/favicon.svg'); ?>">
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@300;400;500;600&display=swap" rel="stylesheet">
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
                        "background-dark": "#0a0c1a",
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
        body {
            min-height: max(884px, 100dvh);
            background: radial-gradient(circle at top right, rgba(196, 175, 107, 0.08), transparent 40%),
                        radial-gradient(circle at bottom left, rgba(196, 175, 107, 0.05), transparent 40%),
                        #0a0c1a;
        }
    </style>
</head>
<body class="font-display flex items-center justify-center p-4">
    <!-- Central Login Card -->
    <div class="w-full max-w-[440px] bg-white rounded-xl shadow-2xl overflow-hidden flex flex-col relative z-10">
        <!-- Header / Logo Area -->
        <div class="pt-10 pb-6 px-8 flex flex-col items-center">
            <div class="w-full h-24 bg-slate-50 rounded-lg flex items-center justify-center border border-slate-200 mb-6 px-4">
                <img src="<?php echo base_url('assets/img/logo-tja.png'); ?>" alt="TJAECH" class="h-16 w-auto">
            </div>
            <h1 class="text-slate-900 text-2xl font-bold tracking-tight text-center">
                Acceso Administrativo
            </h1>
            <p class="text-slate-500 text-sm mt-2 text-center">
                Ingrese sus credenciales para gestionar el portal
            </p>
        </div>
        <!-- Form Area -->
        <div class="px-8 pb-10">
            <form id="loginForm" class="space-y-5">
                <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf); ?>">
                <!-- Email Field -->
                <div>
                    <label class="block text-slate-700 text-sm font-semibold mb-2" for="email">
                        Correo electrónico
                    </label>
                    <div class="relative">
                        <input type="email" id="email" name="email" placeholder="ejemplo@tjaech.gob.mx" required
                               class="w-full h-12 px-4 rounded-lg border border-slate-200 focus:border-institutional-gold focus:ring-2 focus:ring-institutional-gold/20 bg-white text-slate-900 placeholder:text-slate-400 transition-all outline-none">
                    </div>
                </div>
                <!-- Password Field -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-slate-700 text-sm font-semibold" for="password">
                            Contraseña
                        </label>
                    </div>
                    <div class="relative flex">
                        <input type="password" id="password" name="password" placeholder="••••••••" required
                               class="flex-1 h-12 pl-4 pr-10 rounded-lg border border-slate-200 focus:border-institutional-gold focus:ring-2 focus:ring-institutional-gold/20 bg-white text-slate-900 placeholder:text-slate-400 transition-all outline-none">
                    </div>
                </div>
                <!-- Forgot Password Option -->
                <div class="flex justify-end">
                    <a href="<?php echo base_url('admin/forgot'); ?>" class="text-xs font-medium text-slate-500 hover:text-primary transition-colors hover:underline">¿Olvidó su contraseña?</a>
                </div>
                <!-- Login Button -->
                <button type="submit" class="w-full h-12 bg-institutional-gold hover:opacity-90 text-white font-bold rounded-lg transition-all shadow-lg shadow-institutional-gold/20 flex items-center justify-center gap-2 mt-4">
                    <span>Ingresar</span>
                    <span class="material-symbols-outlined text-lg">login</span>
                </button>
            </form>
            <!-- Footer Link -->
            <div class="mt-8 pt-6 border-t border-slate-100 flex justify-center">
                <a href="<?php echo base_url('/'); ?>" class="flex items-center gap-2 text-slate-500 hover:text-primary text-sm font-medium transition-colors">
                    <span class="material-symbols-outlined text-base">arrow_back</span>
                    Volver al Portal de Búsqueda
                </a>
            </div>
        </div>
    </div>
    <!-- Decorative background elements -->
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-institutional-gold/5 rounded-full blur-[120px]"></div>
        <div class="absolute -bottom-[10%] -right-[10%] w-[40%] h-[40%] bg-institutional-gold/5 rounded-full blur-[120px]"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const form = e.target;
            const btn = form.querySelector('button[type="submit"]');
            const originalContent = btn.innerHTML;
            btn.innerHTML = '<span class="material-symbols-outlined animate-spin">refresh</span> Verificando...';
            btn.disabled = true;

            const payload = {
                email: form.email.value.trim(),
                password: form.password.value,
                csrf: form.csrf.value
            };
            try {
                const res = await fetch('<?php echo base_url('admin/login'); ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (data.ok) {
                    window.location.href = data.redirect;
                } else {
                    Swal.fire({ icon: 'error', title: 'Acceso denegado', text: data.message || 'Error de autenticación', confirmButtonColor: '#1b456f' });
                    btn.innerHTML = originalContent;
                    btn.disabled = false;
                }
            } catch(err) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Error de conexión', confirmButtonColor: '#1b456f' });
                btn.innerHTML = originalContent;
                btn.disabled = false;
            }
        });
    </script>
</body>
</html>
