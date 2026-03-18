<section class="max-w-3xl mx-auto px-4 mt-12 mb-20 text-center">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-xl p-8 md:p-12 border border-slate-100 dark:border-slate-700">
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">Verificar Documento</h2>
        <p class="text-slate-500 mb-8">Ingresa el token impreso en el QR para validar el documento.</p>
        
        <form class="max-w-md mx-auto" method="get" action="<?php echo base_url('c'); ?>">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-grow relative">
                    <input type="text" class="w-full h-12 px-4 rounded-lg border border-slate-200 focus:border-institutional-gold focus:ring-2 focus:ring-institutional-gold/20 outline-none transition-all dark:bg-slate-900 dark:text-white" name="token" placeholder="Ejemplo: A1B2C3D4" required>
                </div>
                <button type="submit" class="h-12 px-6 bg-primary hover:bg-primary/90 text-white font-medium rounded-lg flex items-center justify-center gap-2 transition-colors">
                    <span class="material-symbols-outlined text-sm">circle</span>
                    Verificar
                </button>
            </div>
        </form>
        <div class="mt-6 p-4 rounded-lg bg-blue-50 text-blue-800 text-sm border border-blue-100 text-left">
            <span class="material-symbols-outlined text-blue-500 align-middle mr-2 text-base">info</span>
            Si llegaste aquí desde un QR, el sistema abrirá automáticamente el detalle del documento.
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
