<!-- Quick Statistics Section (Stitch Design) -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 flex items-center gap-4">
        <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
            <span class="material-symbols-outlined text-primary">description</span>
        </div>
        <div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Emitidas</p>
            <p class="text-xl font-bold text-primary" id="stat-total">--</p>
        </div>
    </div>
    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 flex items-center gap-4">
        <div class="w-10 h-10 rounded-lg bg-success/10 flex items-center justify-center">
            <span class="material-symbols-outlined text-success">verified</span>
        </div>
        <div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Verificadas</p>
            <p class="text-xl font-bold text-primary" id="stat-verified">--</p>
        </div>
    </div>
    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 flex items-center gap-4">
        <div class="w-10 h-10 rounded-lg bg-orange-100 flex items-center justify-center">
            <span class="material-symbols-outlined text-orange-600">pending_actions</span>
        </div>
        <div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pendientes</p>
            <p class="text-xl font-bold text-primary" id="stat-pending">--</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-10 gap-6 lg:gap-8 pb-20 lg:pb-0">
    <!-- Left Column: Form -->
    <?php if (!empty($can_manage)): ?>
    <div class="lg:col-span-3">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center gap-2 mb-6">
                <span class="material-symbols-outlined text-accent">add_circle</span>
                <h2 class="text-base font-bold text-primary uppercase tracking-tight">Emitir constancia</h2>
            </div>
            <form id="certificateForm" class="space-y-4">
                <input type="hidden" name="id">
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Participante</label>
                    <select name="participant_id" required class="w-full rounded-lg border-slate-200 text-sm focus:ring-corporate-blue focus:border-corporate-blue bg-white py-2.5"></select>
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Curso</label>
                    <select name="course_id" required class="w-full rounded-lg border-slate-200 text-sm focus:ring-corporate-blue focus:border-corporate-blue bg-white py-2.5"></select>
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Tipo de documento</label>
                    <select name="doc_type" class="w-full rounded-lg border-slate-200 text-sm focus:ring-corporate-blue focus:border-corporate-blue bg-white py-2.5">
                        <option>Constancia</option>
                        <option>Certificado</option>
                        <option>Reconocimiento</option>
                    </select>
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Estatus inicial</label>
                    <div class="flex gap-4 pt-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="status" value="VERIFIED" class="text-corporate-blue focus:ring-corporate-blue">
                            <span class="text-sm text-slate-700">Verificado</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="status" value="NOT_VERIFIED" checked class="text-corporate-blue focus:ring-corporate-blue">
                            <span class="text-sm text-slate-700">Pendiente</span>
                        </label>
                    </div>
                </div>
                <div class="pt-4 space-y-3">
                    <button type="submit" class="w-full bg-corporate-blue hover:bg-opacity-90 text-white font-bold py-3 rounded-lg shadow-md transition-all uppercase tracking-widest text-xs flex justify-center items-center gap-2">
                        <span class="material-symbols-outlined text-lg">verified</span>
                        Emitir Documento
                    </button>
                    <button type="button" id="certificateReset" class="w-full bg-white border-2 border-slate-200 text-corporate-blue hover:bg-slate-50 font-bold py-2.5 rounded-lg transition-all uppercase tracking-widest text-xs flex justify-center items-center gap-2">
                        <span class="material-symbols-outlined text-lg">mop</span>
                        Limpiar
                    </button>
                </div>
            </form>
            <div id="issueResult" class="mt-4 text-sm text-center font-bold"></div>
        </div>
        <div class="mt-6 p-4 rounded-xl border-l-4 border-accent bg-primary text-white shadow-lg">
            <p class="text-[11px] font-medium opacity-80 uppercase tracking-widest mb-1">Aviso Importante</p>
            <p class="text-xs leading-relaxed">Cada constancia emitida genera un token único de seguridad para verificación vía QR institucional.</p>
        </div>
    </div>
    <?php endif; ?>

    <!-- Right Column: List / Table -->
    <div class="<?php echo !empty($can_manage) ? 'lg:col-span-7' : 'lg:col-span-10'; ?>">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <!-- Table Header / Search -->
            <div class="p-4 lg:p-6 border-b border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4 bg-slate-50/30">
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <span class="text-sm font-bold text-slate-800">Registros Recientes</span>
                </div>
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <div class="relative w-full sm:w-64">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                        <input type="text" id="certificateSearch" class="w-full pl-10 pr-4 py-2 bg-white border-slate-200 rounded-lg text-sm focus:ring-corporate-blue focus:border-corporate-blue" placeholder="Buscar constancia...">
                    </div>
                    <a href="<?php echo base_url('admin/api/certificates/export'); ?>" class="flex items-center justify-center bg-white border border-slate-200 text-slate-600 hover:text-corporate-blue hover:border-corporate-blue p-2 rounded-lg transition-colors" title="Exportar a CSV">
                        <span class="material-symbols-outlined">download</span>
                    </a>
                </div>
            </div>

            <!-- Note: admin.js injects raw table rows containing mostly bootstrap markup for certificates.
                 We will adapt CSS / admin.js in a separate step to render the table correctly in Tailwind -->
            <div class="overflow-x-auto w-full">
                <table class="w-full text-left border-collapse min-w-[800px]">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="px-5 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Participante</th>
                            <th class="px-5 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Curso</th>
                            <th class="px-5 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Tipo</th>
                            <th class="px-5 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Estatus</th>
                            <th class="px-5 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Token</th>
                            <th class="px-5 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="certificateTable" data-can-manage="<?php echo !empty($can_manage) ? '1' : '0'; ?>" class="divide-y divide-slate-100">
                    </tbody>
                </table>
            </div>
            
        </div>
    </div>
</div>
