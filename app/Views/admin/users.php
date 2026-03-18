<div class="grid grid-cols-1 lg:grid-cols-10 gap-6 lg:gap-8 pb-20 lg:pb-0">
    <!-- Left Column: Form -->
    <div class="lg:col-span-3">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center gap-2 mb-6">
                <span class="material-symbols-outlined text-accent">person_add</span>
                <h2 class="text-base font-bold text-primary uppercase tracking-tight">Reg. Usuario</h2>
            </div>
            <form id="userForm" class="space-y-4">
                <input type="hidden" name="id">
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Nombre</label>
                    <input type="text" name="name" required class="w-full h-11 px-4 rounded-lg border border-slate-200 text-sm focus:ring-corporate-blue bg-white">
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Correo</label>
                    <input type="email" name="email" required class="w-full h-11 px-4 rounded-lg border border-slate-200 text-sm focus:ring-corporate-blue bg-white">
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Roles</label>
                    <label class="flex items-center gap-2 cursor-pointer pb-2 border-b border-slate-100">
                        <input type="checkbox" id="rolesAll" class="rounded text-corporate-blue focus:ring-corporate-blue">
                        <span class="text-xs font-bold text-slate-700">Seleccionar todos</span>
                    </label>
                    <div class="space-y-2 pt-1">
                        <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" name="roles[]" value="ADMIN" class="rounded text-corporate-blue"><span class="text-xs text-slate-600">Administrador</span></label>
                        <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" name="roles[]" value="COURSES" class="rounded text-corporate-blue"><span class="text-xs text-slate-600">Cursos</span></label>
                        <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" name="roles[]" value="PARTICIPANTS" class="rounded text-corporate-blue"><span class="text-xs text-slate-600">Participantes</span></label>
                        <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" name="roles[]" value="CERTIFICATES" class="rounded text-corporate-blue"><span class="text-xs text-slate-600">Constancias</span></label>
                        <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" name="roles[]" value="READONLY" class="rounded text-corporate-blue"><span class="text-xs text-slate-600">Solo lectura</span></label>
                    </div>
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Estatus</label>
                    <select name="status" required class="w-full h-11 px-4 rounded-lg border border-slate-200 text-sm focus:ring-corporate-blue bg-white">
                        <option value="ACTIVE">Activo</option>
                        <option value="DISABLED">Deshabilitado</option>
                    </select>
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Contraseña</label>
                    <input type="password" name="password" class="w-full h-11 px-4 rounded-lg border border-slate-200 text-sm focus:ring-corporate-blue bg-white">
                    <p class="text-[10px] text-slate-400">Deja en blanco para conservar la actual.</p>
                </div>
                <div class="pt-4 space-y-3">
                    <button type="submit" class="w-full bg-corporate-blue hover:bg-opacity-90 text-white font-bold py-3 rounded-lg shadow-md transition-all uppercase tracking-widest text-xs flex justify-center items-center gap-2">
                        <span class="material-symbols-outlined text-lg">save</span> Guardar
                    </button>
                    <button type="button" id="userReset" class="w-full bg-white border-2 border-slate-200 text-corporate-blue hover:bg-slate-50 font-bold py-2.5 rounded-lg transition-all uppercase tracking-widest text-xs flex justify-center items-center gap-2">
                        <span class="material-symbols-outlined text-lg">mop</span> Limpiar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Right Column: List -->
    <div class="lg:col-span-7 space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-4 lg:p-6 border-b border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4 bg-slate-50/30">
                <span class="text-sm font-bold text-slate-800">Usuarios Registrados</span>
                <div class="relative w-full sm:w-64">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                    <input type="text" id="userSearch" class="w-full pl-10 pr-4 py-2 bg-white border-slate-200 rounded-lg text-sm focus:ring-corporate-blue" placeholder="Buscar usuario...">
                </div>
            </div>
            <!-- admin.js uses grid structure with classes tja-user-card etc, we will patch it dynamically or via CSS -->
            <div id="userCards" class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 lg:p-6 bg-slate-50/30"></div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h5 class="text-sm font-bold text-primary uppercase tracking-tight mb-4">Permisos por rol</h5>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm text-slate-600">
                    <thead class="border-b border-slate-200">
                        <tr>
                            <th class="py-2 font-bold text-slate-500 uppercase text-[10px] tracking-wider">Rol</th>
                            <th class="py-2 font-bold text-slate-500 uppercase text-[10px] tracking-wider">Permisos</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr><td class="py-2 font-bold text-xs">ADMIN</td><td class="py-2 text-xs">Todo el sistema, usuarios y auditoría</td></tr>
                        <tr><td class="py-2 font-bold text-xs">COURSES</td><td class="py-2 text-xs">Gestión de cursos</td></tr>
                        <tr><td class="py-2 font-bold text-xs">PARTICIPANTS</td><td class="py-2 text-xs">Gestión de participantes</td></tr>
                        <tr><td class="py-2 font-bold text-xs">CERTIFICATES</td><td class="py-2 text-xs">Gestión de constancias</td></tr>
                        <tr><td class="py-2 font-bold text-xs">READONLY</td><td class="py-2 text-xs">Solo lectura (sin edición)</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    const observer = new MutationObserver(() => {
        document.querySelectorAll('#userCards > div').forEach(card => {
            if(card.classList.contains('bg-white')) return; // Already processed
            card.className = 'bg-white border border-slate-200 rounded-xl p-4 shadow-sm hover:shadow-md transition-shadow flex flex-col gap-4';
            
            const head = card.querySelector('.tja-user-head');
            if(head) head.className = 'flex justify-between items-start border-b border-slate-100 pb-3';
            
            const name = card.querySelector('.tja-user-name');
            if(name) name.className = 'text-sm font-bold text-primary truncate max-w-[150px]';
            
            const email = card.querySelector('.tja-user-email');
            if(email) email.className = 'text-xs text-slate-500 truncate max-w-[150px]';
            
            const badge = card.querySelector('.badge');
            if(badge) {
                if(badge.classList.contains('tja-badge-success')) badge.className = 'text-[10px] font-bold text-success bg-success/10 px-2 py-0.5 rounded-full uppercase';
                else badge.className = 'text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full uppercase';
            }
            
            const meta = card.querySelector('.tja-user-meta');
            if(meta) meta.className = 'grid grid-cols-2 gap-2 text-xs text-slate-600 pb-2';
            
            card.querySelectorAll('.tja-user-label').forEach(l => l.className = 'text-[9px] font-bold uppercase tracking-wider text-slate-400');
            card.querySelectorAll('.tja-user-value').forEach(l => l.className = 'font-medium truncate');
            
            const actions = card.querySelector('.tja-actions');
            if(actions) actions.className = 'flex justify-end gap-1 pt-2 border-t border-slate-50';
            
            card.querySelectorAll('button.tja-action-btn').forEach(btn => {
                btn.className = 'p-1.5 hover:bg-slate-100 rounded-md text-slate-400 hover:text-primary transition-all ml-1 tja-action-btn';
                if(btn.classList.contains('tja-btn-edit')) btn.innerHTML = '<span class="material-symbols-outlined text-[18px]">edit</span>';
                if(btn.classList.contains('tja-btn-danger')) {
                  btn.className = 'p-1.5 hover:bg-red-50 rounded-md text-slate-400 hover:text-red-500 transition-all ml-1 tja-action-btn';
                  btn.innerHTML = '<span class="material-symbols-outlined text-[18px]">delete</span>';
                }
                if(btn.classList.contains('tja-btn-status')) {
                  btn.innerHTML = '<span class="material-symbols-outlined text-[18px]">change_circle</span>';
                }
            });
        });
    });
    const ucards = document.getElementById('userCards');
    if(ucards) observer.observe(ucards, {childList: true});
</script>
