<section class="max-w-3xl mx-auto px-4 mt-12 mb-20 text-center">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-xl p-8 md:p-12 border border-slate-100 dark:border-slate-700 flex flex-col items-center">
        <div class="text-emerald-500 mb-4 bg-emerald-50 p-4 rounded-full">
            <span class="material-symbols-outlined text-5xl">mark_email_read</span>
        </div>
        <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">Recepción Confirmada</h3>
        <p class="text-slate-500 mb-6">Hemos registrado exitosamente la confirmación de recepción para esta constancia oficial.</p>
        
        <?php if (!empty($record)): ?>
            <div class="mb-8 p-6 bg-slate-50 border border-slate-200 rounded-xl text-left w-full max-w-lg space-y-3">
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block">Participante</span>
                    <strong class="text-slate-800 text-base"><?php echo htmlspecialchars($record['full_name']); ?></strong>
                </div>
                <div class="pt-2 border-t border-slate-200/60">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block">Curso / Programa</span>
                    <strong class="text-slate-800 text-sm block mt-0.5"><?php echo htmlspecialchars($record['course_name']); ?></strong>
                </div>
                <div class="pt-2 border-t border-slate-200/60">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block">Tipo de Documento</span>
                    <span class="inline-block mt-1 text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 uppercase"><?php echo htmlspecialchars($record['doc_type']); ?></span>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="<?php echo base_url('c/' . $record['token']); ?>" class="inline-flex items-center justify-center gap-2 h-12 px-6 bg-primary hover:bg-primary/95 text-white font-medium rounded-lg shadow-sm transition-colors">
                <span class="material-symbols-outlined">visibility</span>
                Ver Constancia Oficial
            </a>
            <a href="<?php echo base_url('inicio'); ?>" class="inline-flex items-center justify-center gap-2 h-12 px-6 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium rounded-lg transition-colors">
                <span class="material-symbols-outlined text-sm">home</span>
                Volver al Inicio
            </a>
        </div>
    </div>
</section>
