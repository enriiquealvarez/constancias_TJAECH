<!-- Hero Section -->
<section class="bg-neutral-bg dark:bg-slate-900/50 py-16 md:py-24 px-4 text-center">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-primary dark:text-slate-100 text-3xl md:text-5xl font-extrabold leading-tight tracking-tight mb-4">
            Sistema de Verificación de Documentos
        </h1>
        <p class="text-slate-600 dark:text-slate-400 text-lg md:text-xl font-medium max-w-2xl mx-auto">
            Tribunal de Justicia Administrativa del Estado de Chiapas
        </p>
    </div>
</section>
<!-- Bloque Central: Tarjeta de Validación -->
<section class="max-w-3xl mx-auto px-4 -mt-12 mb-20">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-xl p-8 md:p-12 border border-slate-100 dark:border-slate-700">
        <div class="flex flex-col items-center text-center mb-10">
            <div class="bg-primary/10 p-4 rounded-full mb-6">
                <span class="material-symbols-outlined text-primary text-4xl">verified_user</span>
            </div>
            <h2 class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white mb-4">
                Validar Constancia, Certificado o Reconocimiento
            </h2>
            <p class="text-slate-500 dark:text-slate-400 text-base md:text-lg leading-relaxed">
                Ingrese el token impreso en su documento web o escanee el código QR provisto para confirmar la autenticidad del mismo.
            </p>
        </div>
        <form class="space-y-6" action="<?php echo base_url('c'); ?>" method="get">
            <div class="relative group">
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2 ml-1" for="token">
                    Token de Verificación
                </label>
                <div class="relative flex items-center">
                    <input class="block w-full h-16 px-6 pr-14 text-lg bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-institutional-gold focus:border-institutional-gold transition-all text-slate-900 dark:text-white placeholder:text-slate-400" id="token" name="token" placeholder="Ejemplo: A1B2C3D4..." type="text" required/>
                    <div class="absolute right-4 text-slate-400 group-focus-within:text-institutional-gold transition-colors">
                        <span class="material-symbols-outlined text-2xl">qr_code_scanner</span>
                    </div>
                </div>
            </div>
            <button class="w-full bg-institutional-gold hover:opacity-90 text-white font-bold py-5 px-8 rounded-xl shadow-lg shadow-institutional-gold/20 transition-all transform hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-3 text-lg" type="submit">
                <span class="material-symbols-outlined">search_check</span>
                Verificar Documento
            </button>
        </form>
        <div class="mt-10 pt-8 border-t border-slate-100 dark:border-slate-700 flex flex-col md:flex-row justify-center gap-8 text-sm text-slate-500">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-green-600">security</span>
                <span>Plataforma Oficial Segura</span>
            </div>
        </div>
    </div>
</section>
<script>
    const form = document.querySelector('form');
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const token = form.token.value.trim();
        if (!token) return;
        window.location.href = '<?php echo base_url('c'); ?>/' + encodeURIComponent(token);
    });
</script>
