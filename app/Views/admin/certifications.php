<?php use app\Models\Certification; ?>
<div id="certificationsApp" class="pb-24 lg:pb-0 space-y-6"
     data-can-manage="<?php echo !empty($can_manage) ? '1' : '0'; ?>"
     data-default-subject="<?php echo htmlspecialchars(Certification::DEFAULT_SUBJECT); ?>"
     data-expiring-days="<?php echo Certification::EXPIRING_DAYS; ?>"
     data-today="<?php echo Certification::today()->format('Y-m-d'); ?>">

    <!-- Encabezado -->
    <section class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-primary via-[#162a49] to-corporate-blue text-white shadow-lg">
        <div class="absolute inset-0 opacity-[.07]" style="background-image:repeating-radial-gradient(circle at 100% 0, #fff 0 1px, transparent 1px 18px)"></div>
        <span class="material-symbols-outlined absolute -right-4 -bottom-10 text-[180px] text-accent/20 select-none" aria-hidden="true">verified</span>
        <div class="relative p-6 lg:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="max-w-2xl">
                <span class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-[.2em] text-accent bg-accent/10 border border-accent/30 rounded-full px-3 py-1">
                    <span class="material-symbols-outlined text-sm">balance</span> CEMASC
                </span>
                <h2 class="mt-3 text-2xl lg:text-3xl font-extrabold tracking-tight">Certificaciones</h2>
                <p class="mt-1 text-sm text-slate-300 leading-relaxed"><?php echo htmlspecialchars(Certification::AREA_NAME); ?></p>
            </div>
            <?php if (!empty($can_manage)): ?>
            <button type="button" id="certNew" class="shrink-0 inline-flex items-center justify-center gap-2 bg-accent hover:brightness-110 text-primary font-extrabold uppercase tracking-widest text-xs px-5 py-3.5 rounded-xl shadow-lg shadow-black/20 transition">
                <span class="material-symbols-outlined text-lg">add_circle</span> Nueva certificación
            </button>
            <?php endif; ?>
        </div>
    </section>

    <!-- Indicadores -->
    <section class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4">
        <?php
        $tiles = [
            ['total', 'Total', 'description', 'bg-primary/10 text-primary'],
            ['valid', 'Vigentes', 'verified', 'bg-emerald-100 text-emerald-700'],
            ['expiring', 'Por vencer', 'hourglass_bottom', 'bg-amber-100 text-amber-700'],
            ['inactive', 'No vigentes', 'cancel', 'bg-red-100 text-red-700'],
        ];
        foreach ($tiles as [$key, $label, $icon, $color]): ?>
        <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 flex items-center gap-3 lg:gap-4"<?php echo $key === 'expiring' ? ' title="Vencen en los próximos ' . Certification::EXPIRING_DAYS . ' días"' : ''; ?>>
            <div class="w-10 h-10 shrink-0 rounded-lg flex items-center justify-center <?php echo $color; ?>">
                <span class="material-symbols-outlined"><?php echo $icon; ?></span>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider truncate"><?php echo $label; ?></p>
                <p class="text-xl font-bold text-primary" data-metric="<?php echo $key; ?>">--</p>
            </div>
        </div>
        <?php endforeach; ?>
    </section>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">
        <?php if (!empty($can_manage)): ?>
        <!-- Formulario -->
        <aside class="lg:col-span-5 xl:col-span-4 lg:sticky lg:top-0 space-y-4" id="certFormPanel">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 lg:p-6">
                <div class="flex items-center justify-between gap-2 mb-5">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-accent" id="certFormIcon">add_circle</span>
                        <h3 class="text-base font-bold text-primary uppercase tracking-tight" id="certFormTitle">Nueva certificación</h3>
                    </div>
                    <span id="certEditingBadge" class="hidden text-[10px] font-bold uppercase tracking-wider bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">Editando</span>
                </div>
                <form id="certForm" class="space-y-4" autocomplete="off">
                    <input type="hidden" name="cert_id">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider" for="cf_name">Nombre de la persona certificada</label>
                        <input id="cf_name" type="text" name="full_name" required maxlength="200" class="w-full h-11 px-4 rounded-lg border border-slate-200 text-sm focus:ring-corporate-blue focus:border-corporate-blue bg-white" placeholder="Nombre completo">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider" for="cf_number">Número de certificación</label>
                        <input id="cf_number" type="text" name="cert_number" required maxlength="30" class="block w-32 h-11 px-3 rounded-lg border border-slate-200 text-sm font-bold text-center focus:ring-corporate-blue focus:border-corporate-blue bg-white tabular-nums">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider" for="cf_subject">Materia</label>
                        <input id="cf_subject" type="text" name="subject" required maxlength="200" list="cf_subjects" class="w-full h-11 px-4 rounded-lg border border-slate-200 text-sm focus:ring-corporate-blue focus:border-corporate-blue bg-white">
                        <datalist id="cf_subjects">
                            <option value="<?php echo htmlspecialchars(Certification::DEFAULT_SUBJECT); ?>">
                        </datalist>
                    </div>
                    <div class="space-y-1.5">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Vigencia</span>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="block">
                                <span class="text-[10px] font-semibold text-slate-400 uppercase">Desde</span>
                                <input type="date" name="valid_from" required class="w-full h-11 px-3 rounded-lg border border-slate-200 text-sm focus:ring-corporate-blue focus:border-corporate-blue bg-white">
                            </label>
                            <label class="block">
                                <span class="text-[10px] font-semibold text-slate-400 uppercase">Hasta</span>
                                <input type="date" name="valid_until" required class="w-full h-11 px-3 rounded-lg border border-slate-200 text-sm focus:ring-corporate-blue focus:border-corporate-blue bg-white">
                            </label>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 pt-1">
                            <span class="text-[10px] font-semibold text-slate-400 uppercase">Rápido:</span>
                            <?php foreach ([1, 2, 3] as $y): ?>
                            <button type="button" data-years="<?php echo $y; ?>" class="cert-years text-[11px] font-bold text-corporate-blue bg-corporate-blue/5 hover:bg-corporate-blue hover:text-white border border-corporate-blue/20 rounded-full px-3 py-1 transition"><?php echo $y; ?> año<?php echo $y > 1 ? 's' : ''; ?></button>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Vista previa de lo que se verá al escanear -->
                    <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50/70 p-4">
                        <p class="text-[10px] font-bold uppercase tracking-[.2em] text-slate-400 mb-2 flex items-center gap-1"><span class="material-symbols-outlined text-sm">qr_code_scanner</span> Así se verá al escanear</p>
                        <div class="flex items-start gap-3">
                            <span id="pvBadge" class="shrink-0 inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-[10px] font-extrabold bg-slate-200 text-slate-500">—</span>
                            <div class="min-w-0 text-xs text-slate-600 space-y-0.5">
                                <p class="font-bold text-slate-800 truncate" id="pvName">Nombre de la persona</p>
                                <p>Núm. <strong id="pvNumber">--</strong> · <span id="pvSubject"></span></p>
                                <p id="pvDates" class="text-slate-400"></p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 pt-2">
                        <button type="button" id="certReset" class="bg-white border-2 border-slate-200 text-corporate-blue hover:bg-slate-50 font-bold py-2.5 rounded-lg transition-all uppercase tracking-widest text-xs flex justify-center items-center gap-2">
                            <span class="material-symbols-outlined text-lg">mop</span> Limpiar
                        </button>
                        <button type="submit" class="bg-corporate-blue hover:bg-opacity-90 text-white font-bold py-3 rounded-lg shadow-md transition-all uppercase tracking-widest text-xs flex justify-center items-center gap-2">
                            <span class="material-symbols-outlined text-lg">save</span> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </aside>
        <?php endif; ?>

        <!-- Listado -->
        <section class="<?php echo !empty($can_manage) ? 'lg:col-span-7 xl:col-span-8' : 'lg:col-span-12'; ?> order-first lg:order-none bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-4 lg:p-5 border-b border-slate-100 bg-slate-50/40 space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <span class="text-sm font-bold text-slate-800">Certificaciones registradas</span>
                    <div class="relative w-full sm:w-72">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                        <input type="search" id="certSearch" class="w-full pl-10 pr-4 py-2 bg-white border-slate-200 rounded-lg text-sm focus:ring-corporate-blue" placeholder="Buscar por nombre o número...">
                    </div>
                </div>
                <div class="flex gap-2 overflow-x-auto pb-1 -mx-1 px-1" id="certFilters">
                    <?php foreach (['ALL' => 'Todas', 'VALID' => 'Vigentes', 'EXPIRING' => 'Por vencer', 'EXPIRED' => 'No vigentes', 'REVOKED' => 'Revocadas'] as $k => $v): ?>
                    <button type="button" data-filter="<?php echo $k; ?>" class="cert-filter shrink-0 text-xs font-bold rounded-full px-3.5 py-1.5 border transition"><?php echo $v; ?></button>
                    <?php endforeach; ?>
                </div>
            </div>
            <ul id="certList" class="divide-y divide-slate-100"></ul>
            <div id="certEmpty" class="hidden p-10 text-center">
                <div class="mx-auto w-14 h-14 rounded-full bg-slate-100 flex items-center justify-center mb-3">
                    <span class="material-symbols-outlined text-slate-400 text-3xl">workspace_premium</span>
                </div>
                <p class="text-sm font-bold text-slate-600">Sin certificaciones para mostrar</p>
                <p class="text-xs text-slate-400 mt-1">Registra la primera con el formulario.</p>
            </div>
        </section>
    </div>
</div>
<script src="<?php echo base_url('assets/js/certifications.js?v=' . time()); ?>" defer></script>
