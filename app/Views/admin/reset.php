<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TJAECH - Restablecer contraseña</title>
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
    <div class="w-full max-w-[440px] bg-white rounded-xl shadow-2xl overflow-hidden flex flex-col relative z-10">
        <div class="pt-10 pb-6 px-8 flex flex-col items-center">
            <h1 class="text-slate-900 text-2xl font-bold tracking-tight text-center">
                Restablecer contraseña
            </h1>
            <p class="text-slate-500 text-sm mt-2 text-center px-2">
                Ingresa el código que enviamos a tu correo y tu nueva contraseña.
            </p>
        </div>
        <div class="px-8 pb-10">
            <form id="resetForm" class="space-y-4">
                <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf); ?>">
                <div>
                    <label class="block text-slate-700 text-sm font-semibold mb-1" for="email">Correo electrónico</label>
                    <input type="email" id="email" name="email" required class="w-full h-11 px-4 rounded-lg border border-slate-200 focus:border-institutional-gold focus:ring-2 focus:ring-institutional-gold/20 outline-none">
                </div>
                <div>
                    <label class="block text-slate-700 text-sm font-semibold mb-1" for="token">Código de seguridad (6 dígitos)</label>
                    <input type="text" id="token" name="token" required maxlength="6" class="w-full h-11 px-4 rounded-lg border border-slate-200 focus:border-institutional-gold focus:ring-2 focus:ring-institutional-gold/20 outline-none tracking-widest text-center font-mono font-bold text-lg text-primary">
                </div>
                <div>
                    <label class="block text-slate-700 text-sm font-semibold mb-1" for="password">Nueva contraseña</label>
                    <input type="password" id="password" name="password" required minlength="8" class="w-full h-11 px-4 rounded-lg border border-slate-200 focus:border-institutional-gold focus:ring-2 focus:ring-institutional-gold/20 outline-none">
                </div>
                <div>
                    <label class="block text-slate-700 text-sm font-semibold mb-1" for="confirm">Confirmar nueva contraseña</label>
                    <input type="password" id="confirm" name="confirm" required minlength="8" class="w-full h-11 px-4 rounded-lg border border-slate-200 focus:border-institutional-gold focus:ring-2 focus:ring-institutional-gold/20 outline-none">
                </div>
                
                <button type="submit" class="w-full h-12 bg-primary hover:bg-primary/90 text-white font-bold rounded-lg transition-all shadow-lg flex items-center justify-center gap-2 mt-6">
                    <span class="material-symbols-outlined text-lg">save</span>
                    Guardar y acceder
                </button>
            </form>
            <div class="mt-8 pt-6 border-t border-slate-100 flex justify-center">
                <a href="<?php echo base_url('admin/login'); ?>" class="flex items-center gap-2 text-slate-500 hover:text-primary text-sm font-medium transition-colors">
                    <span class="material-symbols-outlined text-base">arrow_back</span>
                    Regresar al login
                </a>
            </div>
        </div>
    </div>
    
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-institutional-gold/5 rounded-full blur-[120px]"></div>
        <div class="absolute -bottom-[10%] -right-[10%] w-[40%] h-[40%] bg-institutional-gold/5 rounded-full blur-[120px]"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('resetForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const form = e.target;
            const btn = form.querySelector('button[type="submit"]');
            const originalContent = btn.innerHTML;
            btn.innerHTML = '<span class="material-symbols-outlined animate-spin">refresh</span> Procesando...';
            btn.disabled = true;

            const payload = {
                email: form.email.value.trim(),
                token: form.token.value.trim(),
                password: form.password.value,
                confirm: form.confirm.value,
                csrf: form.csrf.value
            };

            if (payload.password !== payload.confirm) {
                Swal.fire({ icon: 'warning', title: 'Verificar', text: 'Las contraseñas no coinciden', confirmButtonColor: '#1b456f' });
                btn.innerHTML = originalContent;
                btn.disabled = false;
                return;
            }

            try {
                const res = await fetch('<?php echo base_url('admin/reset'); ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (data.ok) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: 'Contraseña actualizada correctamente',
                        confirmButtonColor: '#1b456f',
                    }).then(() => {
                        window.location.href = data.redirect || '<?php echo base_url('admin/login'); ?>';
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Error', confirmButtonColor: '#1b456f' });
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
