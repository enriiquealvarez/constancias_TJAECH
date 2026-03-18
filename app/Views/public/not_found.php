<section class="max-w-3xl mx-auto px-4 mt-12 mb-20 text-center">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-xl p-8 md:p-12 border border-slate-100 dark:border-slate-700 flex flex-col items-center">
        <div class="text-red-500 mb-4 bg-red-50 p-4 rounded-full">
            <span class="material-symbols-outlined text-5xl">error</span>
        </div>
        <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">Documento no encontrado</h3>
        <p class="text-slate-500 mb-6">El token proporcionado no existe o no está registrado. Verifica que el QR sea legible o intenta nuevamente.</p>
        
        <?php if (!empty($token)): ?>
            <div class="mb-6 p-3 bg-slate-50 border border-slate-200 rounded-lg text-slate-700 text-sm">
                Token consultado: <strong class="font-mono text-primary"><?php echo htmlspecialchars($token); ?></strong>
            </div>
        <?php endif; ?>
        
        <a href="<?php echo base_url('verificar'); ?>" class="inline-flex items-center gap-2 h-12 px-6 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium rounded-lg transition-colors">
            <span class="material-symbols-outlined text-sm">refresh</span>
            Intentar otra vez
        </a>
    </div>
</section>
