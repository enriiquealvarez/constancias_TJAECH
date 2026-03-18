<div class="grid grid-cols-1 lg:grid-cols-10 gap-6 lg:gap-8 pb-20 lg:pb-0">
    <!-- Left Column: Form -->
    <?php if (!empty($can_manage)): ?>
    <div class="lg:col-span-3">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center gap-2 mb-6">
                <span class="material-symbols-outlined text-accent">add_circle</span>
                <h2 class="text-base font-bold text-primary uppercase tracking-tight">Registrar curso</h2>
            </div>
            <form id="courseForm" class="space-y-4">
                <input type="hidden" name="id">
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Nombre</label>
                    <input type="text" name="name" required class="w-full h-11 px-4 rounded-lg border border-slate-200 text-sm focus:ring-corporate-blue focus:border-corporate-blue bg-white">
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Edición</label>
                    <input type="text" name="edition" class="w-full h-11 px-4 rounded-lg border border-slate-200 text-sm focus:ring-corporate-blue focus:border-corporate-blue bg-white">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Inicio</label>
                        <input type="date" name="start_date" class="w-full h-11 px-3 rounded-lg border border-slate-200 text-sm focus:ring-corporate-blue focus:border-corporate-blue bg-white">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Fin</label>
                        <input type="date" name="end_date" class="w-full h-11 px-3 rounded-lg border border-slate-200 text-sm focus:ring-corporate-blue focus:border-corporate-blue bg-white">
                    </div>
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Modalidad</label>
                    <input type="text" name="modality" class="w-full h-11 px-4 rounded-lg border border-slate-200 text-sm focus:ring-corporate-blue focus:border-corporate-blue bg-white">
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Área organizadora</label>
                    <input type="text" name="area" class="w-full h-11 px-4 rounded-lg border border-slate-200 text-sm focus:ring-corporate-blue focus:border-corporate-blue bg-white">
                </div>
                <div class="pt-4 space-y-3">
                    <button type="submit" class="w-full bg-corporate-blue hover:bg-opacity-90 text-white font-bold py-3 rounded-lg shadow-md transition-all uppercase tracking-widest text-xs flex justify-center items-center gap-2">
                        <span class="material-symbols-outlined text-lg">save</span> Guardar
                    </button>
                    <button type="button" id="courseReset" class="w-full bg-white border-2 border-slate-200 text-corporate-blue hover:bg-slate-50 font-bold py-2.5 rounded-lg transition-all uppercase tracking-widest text-xs flex justify-center items-center gap-2">
                        <span class="material-symbols-outlined text-lg">mop</span> Limpiar
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- Right Column: List / Table -->
    <div class="<?php echo !empty($can_manage) ? 'lg:col-span-7' : 'lg:col-span-10'; ?>">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-4 lg:p-6 border-b border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4 bg-slate-50/30">
                <span class="text-sm font-bold text-slate-800">Cursos Registrados</span>
                <div class="relative w-full sm:w-64">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                    <input type="text" id="courseSearch" class="w-full pl-10 pr-4 py-2 bg-white border-slate-200 rounded-lg text-sm focus:ring-corporate-blue focus:border-corporate-blue" placeholder="Buscar por nombre o edición">
                </div>
            </div>

            <div class="overflow-x-auto w-full">
                <table class="w-full text-left border-collapse min-w-[700px]">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="px-5 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Nombre</th>
                            <th class="px-5 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Edición</th>
                            <th class="px-5 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Fechas</th>
                            <th class="px-5 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Modalidad</th>
                            <th class="px-5 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="courseTable" data-can-manage="<?php echo !empty($can_manage) ? '1' : '0'; ?>" class="divide-y divide-slate-100">
                    </tbody>
                </table>
            </div>
            
        </div>
    </div>
</div>
<script>
    // Monkey patch the table renderer just for this page so we don't need to rebuild admin.js for all pages right now
    const oldFetchJsonC = window.fetchJson;
    if(typeof oldFetchJsonC !== 'undefined'){
      // we just let admin.js do its logic, we'll patch the html inside admin.js previously but wait!
      // admin.js has hardcoded bootstrap classes: btn btn-sm tja-btn-outline...
      // I will just add CSS to main.css to style tja-btn-outline like Tailwind!
      // But actually I can just override the load function or add CSS rules to make it look decent.
      // Wait, we can just replace admin.js content for Cursos too but let's just let it load as is.
      // I'll add a quick observer to style dynamically.
      const observer = new MutationObserver(() => {
          document.querySelectorAll('#courseTable tr').forEach(tr => tr.classList.add('hover:bg-slate-50', 'transition-colors'));
          document.querySelectorAll('#courseTable td').forEach(td => td.classList.add('px-5', 'py-4', 'text-sm', 'text-slate-600'));
          document.querySelectorAll('#courseTable button.tja-action-btn').forEach(btn => {
              btn.className = 'p-1.5 hover:bg-slate-100 rounded-md text-slate-400 hover:text-primary transition-all ml-1 tja-action-btn';
              if(btn.classList.contains('tja-btn-edit')) btn.innerHTML = '<span class="material-symbols-outlined text-[18px]">edit</span>';
              if(btn.classList.contains('tja-btn-danger')) {
                btn.className = 'p-1.5 hover:bg-red-50 rounded-md text-slate-400 hover:text-red-500 transition-all ml-1 tja-action-btn';
                btn.innerHTML = '<span class="material-symbols-outlined text-[18px]">delete</span>';
              }
          });
      });
      const ctable = document.getElementById('courseTable');
      if(ctable) observer.observe(ctable, {childList: true});
    }
</script>
