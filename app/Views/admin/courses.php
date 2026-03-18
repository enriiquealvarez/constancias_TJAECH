<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 pb-20 lg:pb-0">
    <!-- Left Column: Form -->
    <?php if (!empty($can_manage)): ?>
    <div class="lg:col-span-4">
        <div class="bg-white rounded-xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden border border-slate-100 relative">
            <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-primary to-accent"></div>
            <div class="p-6 bg-slate-50/50 border-b border-slate-100 flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-white shadow-sm flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined text-[18px]">menu_book</span>
                </div>
                <h2 class="text-[13px] font-bold text-primary uppercase tracking-widest">Nuevo Curso</h2>
            </div>
            <div class="p-6">
                <form id="courseForm" class="space-y-5">
                    <input type="hidden" name="id">
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest pl-1">Nombre del Programa</label>
                        <input type="text" name="name" required class="w-full h-11 px-4 rounded-lg border-slate-200 text-sm focus:ring-primary focus:border-primary bg-slate-50/50 hover:bg-white transition-colors placeholder-slate-300" placeholder="Ej. Taller de Responsabilidades">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest pl-1">Edición</label>
                        <input type="text" name="edition" class="w-full h-11 px-4 rounded-lg border-slate-200 text-sm focus:ring-primary focus:border-primary bg-slate-50/50 hover:bg-white transition-colors" placeholder="Ej. 1ra. Edición 2026">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest pl-1">Fecha Inicio</label>
                            <input type="date" name="start_date" class="w-full h-11 px-3 rounded-lg border-slate-200 text-sm focus:ring-primary focus:border-primary bg-slate-50/50 hover:bg-white transition-colors text-slate-600">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest pl-1">Fecha Fin</label>
                            <input type="date" name="end_date" class="w-full h-11 px-3 rounded-lg border-slate-200 text-sm focus:ring-primary focus:border-primary bg-slate-50/50 hover:bg-white transition-colors text-slate-600">
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest pl-1">Modalidad</label>
                        <select name="modality" class="w-full h-11 px-4 rounded-lg border-slate-200 text-sm focus:ring-primary focus:border-primary bg-slate-50/50 hover:bg-white transition-colors text-slate-600">
                            <option value="">Seleccione Modalidad...</option>
                            <option value="Presencial">Presencial</option>
                            <option value="En Línea">En Línea / Virtual</option>
                            <option value="Híbrida">Híbrida</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest pl-1">Área Organizadora</label>
                        <input type="text" name="area" class="w-full h-11 px-4 rounded-lg border-slate-200 text-sm focus:ring-primary focus:border-primary bg-slate-50/50 hover:bg-white transition-colors placeholder-slate-300" placeholder="Ej. Instituto de Formación">
                    </div>
                    <div class="pt-4 flex items-center gap-3">
                        <button type="submit" class="flex-1 bg-primary hover:bg-primary/95 text-white font-bold py-3 px-4 rounded-lg shadow-[0_4px_14px_0_rgba(27,69,111,0.39)] hover:shadow-[0_6px_20px_rgba(27,69,111,0.23)] hover:-translate-y-0.5 transition-all text-[11px] uppercase tracking-widest flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">save</span> Confirmar Registro
                        </button>
                        <button type="button" id="courseReset" class="w-12 h-12 bg-white border border-slate-200 text-slate-400 hover:text-slate-600 hover:bg-slate-50 rounded-lg transition-colors flex justify-center items-center" title="Limpiar formulario">
                            <span class="material-symbols-outlined text-[20px]">mop</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Right Column: List / Table -->
    <div class="<?php echo !empty($can_manage) ? 'lg:col-span-8' : 'lg:col-span-12'; ?>">
        <div class="bg-white rounded-xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden border border-slate-100 flex flex-col h-full min-h-[500px]">
            <!-- Header Filter Area -->
            <div class="p-6 md:p-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 border-b border-slate-100/60 relative">
                <div class="absolute left-0 top-0 bottom-0 w-1 bg-accent"></div>
                <div>
                    <h3 class="text-xl font-bold text-primary tracking-tight">Registro General de Cursos</h3>
                    <p class="text-[13px] text-slate-500 mt-1">Administre el catálogo de programas de capacitación.</p>
                </div>
                <div class="relative w-full md:w-72">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-[20px] pointer-events-none">search</span>
                    <input type="text" id="courseSearch" class="w-full pl-11 pr-4 py-2.5 bg-slate-50/80 border-slate-200 rounded-lg text-sm focus:ring-primary focus:border-primary focus:bg-white transition-colors" placeholder="Buscar por nombre o edición">
                </div>
            </div>

            <!-- Table Area -->
            <div class="overflow-x-auto w-full flex-1">
                <table class="w-full text-left border-collapse min-w-[800px]">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Programa</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Edición</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Periodo</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Modalidad</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-right">Opciones</th>
                        </tr>
                    </thead>
                    <tbody id="courseTable" data-can-manage="<?php echo !empty($can_manage) ? '1' : '0'; ?>" class="divide-y divide-slate-100">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
