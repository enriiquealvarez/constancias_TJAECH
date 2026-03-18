<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 flex items-center gap-4" id="metric-total">
        <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
            <span class="material-symbols-outlined text-primary">description</span>
        </div>
        <div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Emitidas</p>
            <p class="text-xl font-bold text-primary tja-metric-value">--</p>
        </div>
    </div>
    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 flex items-center gap-4" id="metric-verified">
        <div class="w-10 h-10 rounded-lg bg-success/10 flex items-center justify-center">
            <span class="material-symbols-outlined text-success">verified</span>
        </div>
        <div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Verificadas</p>
            <p class="text-xl font-bold text-primary tja-metric-value">--</p>
        </div>
    </div>
    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 flex items-center gap-4" id="metric-not">
        <div class="w-10 h-10 rounded-lg bg-orange-100 flex items-center justify-center">
            <span class="material-symbols-outlined text-orange-600">pending_actions</span>
        </div>
        <div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pendientes</p>
            <p class="text-xl font-bold text-primary tja-metric-value">--</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-10 gap-6 lg:gap-8 pb-20 lg:pb-0">
    <div class="lg:col-span-7">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h4 class="text-base font-bold text-primary uppercase tracking-tight mb-2">Acceso rápido</h4>
            <p class="text-sm text-slate-500 mb-6">Administra los recursos del portal desde los módulos directos.</p>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <?php if (\app\Core\Auth::can('manage_courses') || \app\Core\Auth::can('view_courses')): ?>
                <a href="<?php echo base_url('admin/courses'); ?>" class="flex flex-col items-center justify-center gap-2 p-4 rounded-xl border-2 border-slate-100 hover:border-corporate-blue hover:shadow-md transition-all group bg-slate-50/50 hover:bg-white text-center">
                    <span class="material-symbols-outlined text-2xl text-slate-400 group-hover:text-corporate-blue transition-colors">menu_book</span>
                    <span class="text-xs font-bold text-slate-600 group-hover:text-primary transition-colors uppercase tracking-wider">Cursos</span>
                </a>
                <?php endif; ?>
                <?php if (\app\Core\Auth::can('manage_participants') || \app\Core\Auth::can('view_participants')): ?>
                <a href="<?php echo base_url('admin/participants'); ?>" class="flex flex-col items-center justify-center gap-2 p-4 rounded-xl border-2 border-slate-100 hover:border-corporate-blue hover:shadow-md transition-all group bg-slate-50/50 hover:bg-white text-center">
                    <span class="material-symbols-outlined text-2xl text-slate-400 group-hover:text-corporate-blue transition-colors">group</span>
                    <span class="text-xs font-bold text-slate-600 group-hover:text-primary transition-colors uppercase tracking-wider">Personas</span>
                </a>
                <?php endif; ?>
                <?php if (\app\Core\Auth::can('manage_certificates') || \app\Core\Auth::can('view_certificates')): ?>
                <a href="<?php echo base_url('admin/certificates'); ?>" class="flex flex-col items-center justify-center gap-2 p-4 rounded-xl border-2 border-slate-100 hover:border-corporate-blue hover:shadow-md transition-all group bg-slate-50/50 hover:bg-white text-center">
                    <span class="material-symbols-outlined text-2xl text-slate-400 group-hover:text-corporate-blue transition-colors">workspace_premium</span>
                    <span class="text-xs font-bold text-slate-600 group-hover:text-primary transition-colors uppercase tracking-wider">Constancias</span>
                </a>
                <?php endif; ?>
                <?php if (\app\Core\Auth::can('view_audit')): ?>
                <a href="<?php echo base_url('admin/audit'); ?>" class="flex flex-col items-center justify-center gap-2 p-4 rounded-xl border-2 border-slate-100 hover:border-corporate-blue hover:shadow-md transition-all group bg-slate-50/50 hover:bg-white text-center">
                    <span class="material-symbols-outlined text-2xl text-slate-400 group-hover:text-corporate-blue transition-colors">policy</span>
                    <span class="text-xs font-bold text-slate-600 group-hover:text-primary transition-colors uppercase tracking-wider">Auditoría</span>
                </a>
                <?php endif; ?>
                <?php if (\app\Core\Auth::can('manage_users')): ?>
                <a href="<?php echo base_url('admin/users'); ?>" class="flex flex-col items-center justify-center gap-2 p-4 rounded-xl border-2 border-slate-100 hover:border-corporate-blue hover:shadow-md transition-all group bg-slate-50/50 hover:bg-white text-center">
                    <span class="material-symbols-outlined text-2xl text-slate-400 group-hover:text-corporate-blue transition-colors">manage_accounts</span>
                    <span class="text-xs font-bold text-slate-600 group-hover:text-primary transition-colors uppercase tracking-wider">Usuarios</span>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="lg:col-span-3">
        <div class="bg-primary text-white rounded-xl shadow-lg p-6 relative overflow-hidden">
            <div class="absolute -top-10 -right-10 opacity-10">
                <span class="material-symbols-outlined text-[120px]">security</span>
            </div>
            <h5 class="flex items-center gap-2 text-accent font-bold uppercase tracking-widest text-xs mb-3">
                <span class="material-symbols-outlined">security</span>
                Seguridad
            </h5>
            <p class="text-sm leading-relaxed text-slate-300 relative z-10">
                Verifica siempre los datos antes de emitir una constancia. El sistema genera tokens únicos no modificables.
            </p>
            <div class="h-px w-full bg-white/10 my-4"></div>
            <p class="text-xs text-slate-400 relative z-10">
                Todas tus acciones (registros, emisiones, accesos) quedan guardadas en el log de auditoría del TJAECH.
            </p>
        </div>
    </div>
</div>
