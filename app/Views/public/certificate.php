<?php
$status = $record['status'] === 'VERIFIED';
?>
<section class="max-w-3xl mx-auto px-4 mt-12 mb-20">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-xl p-8 md:p-12 border border-slate-100 dark:border-slate-700">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 pb-6 border-b border-slate-100 dark:border-slate-700 gap-4">
            <h3 class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white">Resultado de verificación</h3>
            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold <?php echo $status ? 'bg-green-50 text-green-700 border border-green-200 dark:bg-green-900/30 dark:text-green-400 dark:border-green-800' : 'bg-red-50 text-red-700 border border-red-200 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800'; ?>">
                <span class="material-symbols-outlined text-lg"><?php echo $status ? 'check_circle' : 'cancel'; ?></span>
                <?php echo $status ? 'VERIFICADO' : 'NO VERIFICADO'; ?>
            </span>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 mb-4">
            <div>
                <div class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 flex items-center gap-1.5"><span class="material-symbols-outlined text-sm">person</span> Nombre completo</div>
                <div class="text-lg font-medium text-slate-900 dark:text-white"><?php echo htmlspecialchars($record['full_name']); ?></div>
            </div>
            <div>
                <div class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 flex items-center gap-1.5"><span class="material-symbols-outlined text-sm">school</span> Curso</div>
                <div class="text-lg font-medium text-slate-900 dark:text-white"><?php echo htmlspecialchars($record['course_name']); ?> <?php echo htmlspecialchars($record['edition']); ?></div>
            </div>
            <div>
                <div class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 flex items-center gap-1.5"><span class="material-symbols-outlined text-sm">description</span> Tipo de documento</div>
                <div class="text-lg font-medium text-slate-900 dark:text-white"><?php echo htmlspecialchars($record['doc_type']); ?></div>
            </div>
            <div>
                <div class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 flex items-center gap-1.5"><span class="material-symbols-outlined text-sm">tag</span> Token</div>
                <div class="text-lg font-mono text-primary font-bold tracking-tight bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 px-3 py-1 rounded-lg inline-block"><?php echo htmlspecialchars($record['token']); ?></div>
            </div>
        </div>
        
        <div class="mt-10 p-5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 text-slate-500 text-sm flex gap-3">
            <span class="material-symbols-outlined text-primary">contact_support</span>
            <div>
                Para información adicional sobre la validez de este documento, contacte al sistema de soporte del Tribunal de Justicia Administrativa del Estado de Chiapas.
            </div>
        </div>
    </div>
</section>
