<div class="pb-20 lg:pb-0">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4 border-b border-slate-100 pb-4">
            <h2 class="text-lg font-bold text-primary uppercase tracking-tight flex items-center gap-2">
                <span class="material-symbols-outlined text-accent">policy</span> Auditoría de acciones
            </h2>
            <div class="flex gap-2 w-full sm:w-auto">
                <div class="relative flex-1 sm:w-64">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                    <input type="text" id="auditSearch" class="w-full pl-10 pr-4 py-2 bg-slate-50 border-slate-200 rounded-lg text-sm focus:ring-corporate-blue focus:border-corporate-blue" placeholder="Buscar usuario...">
                </div>
                <a href="<?php echo base_url('admin/api/audit/export'); ?>" class="flex items-center justify-center bg-white border border-slate-200 text-slate-600 hover:text-corporate-blue hover:border-corporate-blue p-2 rounded-lg transition-colors" title="Exportar a CSV">
                    <span class="material-symbols-outlined">download</span>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4 mb-6">
            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Acción</label>
                <select id="auditAction" class="w-full h-10 px-3 rounded-lg border-slate-200 text-xs focus:ring-corporate-blue bg-slate-50">
                    <option value="">Todas</option>
                    <option value="LOGIN">LOGIN</option>
                    <option value="LOGOUT">LOGOUT</option>
                    <option value="CREATE">CREATE</option>
                    <option value="UPDATE">UPDATE</option>
                    <option value="DELETE">DELETE</option>
                    <option value="ISSUE">ISSUE</option>
                    <option value="STATUS">STATUS</option>
                    <option value="RESET_REQUEST">RESET_REQUEST</option>
                    <option value="RESET_PASSWORD">RESET_PASSWORD</option>
                </select>
            </div>
            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Entidad</label>
                <select id="auditEntity" class="w-full h-10 px-3 rounded-lg border-slate-200 text-xs focus:ring-corporate-blue bg-slate-50">
                    <option value="">Todas</option>
                    <option value="users">Usuarios</option>
                    <option value="courses">Cursos</option>
                    <option value="participants">Participantes</option>
                    <option value="certificates">Constancias</option>
                </select>
            </div>
            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Desde</label>
                <input type="date" id="auditFrom" class="w-full h-10 px-3 rounded-lg border-slate-200 text-xs focus:ring-corporate-blue bg-slate-50">
            </div>
            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Hasta</label>
                <input type="date" id="auditTo" class="w-full h-10 px-3 rounded-lg border-slate-200 text-xs focus:ring-corporate-blue bg-slate-50">
            </div>
            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Orden</label>
                <select id="auditSort" class="w-full h-10 px-3 rounded-lg border-slate-200 text-xs focus:ring-corporate-blue bg-slate-50">
                    <option value="created_at">Fecha</option>
                    <option value="action">Acción</option>
                    <option value="entity">Entidad</option>
                    <option value="user_name">Usuario</option>
                </select>
            </div>
            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Dirección</label>
                <select id="auditDir" class="w-full h-10 px-3 rounded-lg border-slate-200 text-xs focus:ring-corporate-blue bg-slate-50">
                    <option value="desc">Descendente</option>
                    <option value="asc">Ascendente</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto w-full border border-slate-100 rounded-lg">
            <table class="w-full text-left border-collapse min-w-[800px]">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-4 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-4 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Usuario</th>
                        <th class="px-4 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Acción</th>
                        <th class="px-4 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Entidad</th>
                        <th class="px-4 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">ID</th>
                        <th class="px-4 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">IP</th>
                    </tr>
                </thead>
                <tbody id="auditTable" class="divide-y divide-slate-100 text-xs text-slate-600">
                </tbody>
            </table>
        </div>
        
        <div class="flex justify-between items-center mt-4">
            <div class="text-xs text-slate-500 font-bold" id="auditMeta">Página 1</div>
            <div class="flex gap-2">
                <button id="auditPrev" class="px-3 py-1.5 border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-50 disabled:opacity-50 transition-colors">Anterior</button>
                <button id="auditNext" class="px-3 py-1.5 border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-50 disabled:opacity-50 transition-colors">Siguiente</button>
            </div>
        </div>
    </div>
</div>
<script>
    const observer = new MutationObserver(() => {
        document.querySelectorAll('#auditTable tr').forEach(tr => tr.classList.add('hover:bg-slate-50', 'transition-colors'));
        document.querySelectorAll('#auditTable td').forEach(td => td.classList.add('px-4', 'py-3'));
    });
    const atable = document.getElementById('auditTable');
    if(atable) observer.observe(atable, {childList: true});
</script>
