(() => {
    const app = document.getElementById('certificationsApp');
    if (!app) return;

    const base = document.body.getAttribute('data-base') || '';
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const canManage = app.dataset.canManage === '1';
    const defaultSubject = app.dataset.defaultSubject || '';
    const expiringDays = parseInt(app.dataset.expiringDays || '30', 10);
    const today = app.dataset.today;

    const form = document.getElementById('certForm');
    const list = document.getElementById('certList');
    const empty = document.getElementById('certEmpty');
    const search = document.getElementById('certSearch');

    let rows = [];
    let nextNumber = '01';
    let autoNumber = '';
    let filter = 'ALL';

    const STATES = {
        VALID: { label: 'VIGENTE', icon: 'verified', pill: 'bg-emerald-50 text-emerald-700 ring-emerald-200', bar: 'bg-emerald-500', stripe: 'bg-emerald-500' },
        EXPIRING: { label: 'VIGENTE', icon: 'hourglass_bottom', pill: 'bg-amber-50 text-amber-700 ring-amber-200', bar: 'bg-amber-500', stripe: 'bg-amber-400' },
        UPCOMING: { label: 'AÚN NO VIGENTE', icon: 'schedule', pill: 'bg-sky-50 text-sky-700 ring-sky-200', bar: 'bg-sky-500', stripe: 'bg-sky-400' },
        EXPIRED: { label: 'NO VIGENTE', icon: 'cancel', pill: 'bg-red-50 text-red-700 ring-red-200', bar: 'bg-red-500', stripe: 'bg-red-500' },
        REVOKED: { label: 'REVOCADA', icon: 'block', pill: 'bg-slate-100 text-slate-600 ring-slate-300', bar: 'bg-slate-400', stripe: 'bg-slate-400' },
    };

    const esc = (v) => String(v ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    const loader = (on) => window.tjaLoader && (on ? window.tjaLoader.show() : window.tjaLoader.hide());

    const api = async (url, options = {}) => {
        loader(true);
        try {
            const opts = Object.assign({ headers: { 'Content-Type': 'application/json' } }, options);
            if (opts.body && typeof opts.body !== 'string') opts.body = JSON.stringify(opts.body);
            const res = await fetch(base + url.replace(/^\//, ''), opts);
            const data = await res.json().catch(() => ({}));
            if (!res.ok || data.ok === false) throw new Error(data.message || 'Error en la solicitud');
            return data;
        } finally {
            loader(false);
        }
    };

    // Fechas 'YYYY-MM-DD' tratadas como días de calendario (sin desfase de zona horaria).
    const parseDay = (s) => { const [y, m, d] = s.split('-').map(Number); return Date.UTC(y, m - 1, d); };
    const dayDiff = (a, b) => Math.round((parseDay(b) - parseDay(a)) / 86400000);
    const fmt = (s) => s ? new Date(parseDay(s)).toLocaleDateString('es-MX', { day: 'numeric', month: 'short', year: 'numeric', timeZone: 'UTC' }) : '';
    const fmtLong = (s) => s ? new Date(parseDay(s)).toLocaleDateString('es-MX', { day: 'numeric', month: 'long', year: 'numeric', timeZone: 'UTC' }) : '';
    const addYears = (s, n) => {
        const d = new Date(parseDay(s));
        d.setUTCFullYear(d.getUTCFullYear() + n);
        d.setUTCDate(d.getUTCDate() - 1);
        return d.toISOString().slice(0, 10);
    };

    const computeState = (from, until, status) => {
        if (status === 'REVOKED') return 'REVOKED';
        if (!from || !until) return null;
        if (dayDiff(today, from) > 0) return 'UPCOMING';
        const left = dayDiff(today, until);
        if (left < 0) return 'EXPIRED';
        return left <= expiringDays ? 'EXPIRING' : 'VALID';
    };

    const progressOf = (r) => {
        const total = Math.max(1, dayDiff(r.valid_from, r.valid_until) + 1);
        const elapsed = dayDiff(r.valid_from, today) + 1;
        return Math.max(0, Math.min(100, Math.round(elapsed / total * 100)));
    };

    const remainingText = (r) => {
        const left = r.state.days_left;
        switch (r.state.code) {
            case 'REVOKED': return 'Revocada';
            case 'UPCOMING': return `Inicia en ${dayDiff(today, r.valid_from)} días`;
            case 'EXPIRED': return `Venció hace ${-left} día${left === -1 ? '' : 's'}`;
            default: return left === 0 ? 'Último día' : `Restan ${left} día${left === 1 ? '' : 's'}`;
        }
    };

    const publicUrl = (r) => base + 'certificacion/' + r.token;
    const qrUrl = (r, ext, download) => `${base}admin/api/certifications/qr/${r.id}.${ext}${download ? '?download=1' : ''}`;

    // ---------- Listado ----------
    const matchesFilter = (r) => {
        const c = r.state.code;
        if (filter === 'ALL') return true;
        if (filter === 'VALID') return c === 'VALID' || c === 'EXPIRING';
        return c === filter;
    };

    const paintFilters = () => {
        document.querySelectorAll('.cert-filter').forEach((b) => {
            const active = b.dataset.filter === filter;
            b.className = 'cert-filter shrink-0 text-xs font-bold rounded-full px-3.5 py-1.5 border transition ' +
                (active ? 'bg-primary text-white border-primary shadow-sm' : 'bg-white text-slate-500 border-slate-200 hover:border-primary/40 hover:text-primary');
        });
    };

    const render = () => {
        const items = rows.filter(matchesFilter);
        empty.classList.toggle('hidden', items.length > 0);
        list.innerHTML = items.map((r) => {
            const s = STATES[r.state.code];
            const pct = r.state.code === 'REVOKED' ? 100 : progressOf(r);
            const actions = canManage ? `
                <button class="cert-act p-2 rounded-lg text-slate-400 hover:text-primary hover:bg-slate-100 transition" data-act="edit" data-id="${r.id}" title="Editar"><span class="material-symbols-outlined text-[20px]">edit</span></button>
                <button class="cert-act p-2 rounded-lg text-slate-400 hover:text-amber-600 hover:bg-amber-50 transition" data-act="status" data-id="${r.id}" title="${r.status === 'REVOKED' ? 'Reactivar' : 'Revocar'}"><span class="material-symbols-outlined text-[20px]">${r.status === 'REVOKED' ? 'settings_backup_restore' : 'block'}</span></button>
                <button class="cert-act p-2 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition" data-act="delete" data-id="${r.id}" title="Eliminar"><span class="material-symbols-outlined text-[20px]">delete</span></button>` : '';
            return `
            <li class="relative group hover:bg-slate-50/70 transition">
                <span class="absolute left-0 top-0 bottom-0 w-1 ${s.stripe}"></span>
                <div class="pl-5 pr-3 sm:pr-4 py-4 flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4">
                    <button class="cert-act shrink-0 hidden sm:flex w-16 h-16 rounded-xl border border-slate-200 bg-white p-1 hover:border-corporate-blue hover:shadow-md transition" data-act="qr" data-id="${r.id}" title="Ver QR">
                        <img src="${qrUrl(r, 'svg')}" alt="QR" class="w-full h-full" loading="lazy">
                    </button>
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-[11px] font-black text-[#8a7638] bg-accent/15 border border-accent/30 rounded-md px-2 py-0.5 tabular-nums">Nº ${esc(r.cert_number)}</span>
                            <span class="inline-flex items-center gap-1 text-[10px] font-extrabold rounded-full px-2 py-0.5 ring-1 ${s.pill}">
                                <span class="material-symbols-outlined text-[14px]" style="font-variation-settings:'FILL' 1">${s.icon}</span>${s.label}
                            </span>
                        </div>
                        <p class="mt-1.5 text-sm sm:text-base font-bold text-primary truncate">${esc(r.full_name)}</p>
                        <p class="text-xs text-slate-500 truncate">${esc(r.subject)}</p>
                        <div class="mt-2 flex items-center gap-3">
                            <div class="flex-1 h-1.5 rounded-full bg-slate-100 overflow-hidden max-w-xs"><div class="h-full rounded-full ${s.bar}" style="width:${pct}%"></div></div>
                            <span class="text-[11px] text-slate-400 whitespace-nowrap">${fmt(r.valid_from)} – ${fmt(r.valid_until)}</span>
                        </div>
                        <p class="text-[11px] mt-1 font-semibold ${s.pill.split(' ')[1]}">${remainingText(r)}</p>
                    </div>
                    <div class="flex items-center justify-between sm:justify-end gap-1 border-t sm:border-0 border-slate-100 pt-2 sm:pt-0">
                        <button class="cert-act inline-flex items-center gap-1.5 text-xs font-bold text-white bg-corporate-blue hover:bg-primary rounded-lg px-3 py-2 transition" data-act="qr" data-id="${r.id}">
                            <span class="material-symbols-outlined text-[18px]">qr_code_2</span> QR
                        </button>
                        <div class="flex items-center">${actions}</div>
                    </div>
                </div>
            </li>`;
        }).join('');
    };

    const paintMetrics = (m) => {
        const set = (k, v) => { const el = app.querySelector(`[data-metric="${k}"]`); if (el) el.textContent = v; };
        set('total', m.total);
        set('valid', m.valid);
        set('expiring', m.expiring);
        set('inactive', m.expired + m.revoked);
    };

    const load = async () => {
        try {
            const data = await api('/admin/api/certifications?q=' + encodeURIComponent(search.value.trim()));
            rows = data.data || [];
            nextNumber = data.next_number || nextNumber;
            paintMetrics(data.metrics);
            render();
            // Actualiza el número sugerido solo si el usuario no lo ha modificado.
            if (form && !form.cert_id.value && form.cert_number.value === autoNumber) {
                form.cert_number.value = autoNumber = nextNumber;
            }
            updatePreview();
        } catch (err) {
            Swal.fire({ icon: 'error', title: 'Error', text: err.message });
        }
    };

    // ---------- QR ----------
    const copyText = async (text) => {
        try {
            await navigator.clipboard.writeText(text);
        } catch (e) {
            const ta = document.createElement('textarea');
            ta.value = text;
            document.body.appendChild(ta);
            ta.select();
            document.execCommand('copy');
            ta.remove();
        }
    };

    const showQr = (r) => {
        const s = STATES[r.state.code];
        Swal.fire({
            showConfirmButton: false,
            showCloseButton: true,
            width: 520,
            padding: '1.5rem',
            html: `
                <div class="text-left font-display">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-[11px] font-black text-[#8a7638] bg-accent/15 border border-accent/30 rounded-md px-2 py-0.5">Nº ${esc(r.cert_number)}</span>
                        <span class="inline-flex items-center gap-1 text-[10px] font-extrabold rounded-full px-2 py-0.5 ring-1 ${s.pill}">${s.label}</span>
                    </div>
                    <p class="text-lg font-extrabold text-primary leading-tight">${esc(r.full_name)}</p>
                    <p class="text-xs text-slate-500">${esc(r.subject)}</p>
                    <p class="text-xs text-slate-500 mt-0.5">Vigencia: ${fmtLong(r.valid_from)} al ${fmtLong(r.valid_until)}</p>
                    <div class="my-4 mx-auto w-56 h-56 sm:w-64 sm:h-64 rounded-2xl border-2 border-slate-100 p-2 bg-white shadow-inner">
                        <img src="${qrUrl(r, 'svg')}" alt="Código QR" class="w-full h-full">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <a href="${qrUrl(r, 'png', true)}" class="inline-flex items-center justify-center gap-1.5 text-xs font-bold text-white bg-corporate-blue hover:bg-primary rounded-lg px-3 py-2.5 no-underline"><span class="material-symbols-outlined text-[18px]">image</span> PNG (alta res.)</a>
                        <a href="${qrUrl(r, 'svg', true)}" class="inline-flex items-center justify-center gap-1.5 text-xs font-bold text-corporate-blue bg-corporate-blue/5 border border-corporate-blue/20 hover:bg-corporate-blue/10 rounded-lg px-3 py-2.5 no-underline"><span class="material-symbols-outlined text-[18px]">draw</span> SVG (vectorial)</a>
                        <button type="button" id="qrCopy" class="inline-flex items-center justify-center gap-1.5 text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg px-3 py-2.5"><span class="material-symbols-outlined text-[18px]">link</span> Copiar enlace</button>
                        <a href="${publicUrl(r)}" target="_blank" rel="noopener" class="inline-flex items-center justify-center gap-1.5 text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg px-3 py-2.5 no-underline"><span class="material-symbols-outlined text-[18px]">open_in_new</span> Ver verificación</a>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-3 leading-snug">Usa el SVG para imprenta o diseño (no pierde calidad). Deja un margen blanco alrededor del QR al colocarlo en el certificado.</p>
                </div>`,
            didOpen: () => {
                document.getElementById('qrCopy')?.addEventListener('click', async (e) => {
                    await copyText(publicUrl(r));
                    e.currentTarget.innerHTML = '<span class="material-symbols-outlined text-[18px]">check</span> ¡Copiado!';
                });
            },
        });
    };

    // ---------- Formulario ----------
    const preview = {
        badge: document.getElementById('pvBadge'),
        name: document.getElementById('pvName'),
        number: document.getElementById('pvNumber'),
        subject: document.getElementById('pvSubject'),
        dates: document.getElementById('pvDates'),
    };

    function updatePreview() {
        if (!form) return;
        const from = form.valid_from.value;
        const until = form.valid_until.value;
        const editing = rows.find((r) => String(r.id) === form.cert_id.value);
        const code = (from && until && until >= from) ? computeState(from, until, editing?.status) : null;
        const s = code ? STATES[code] : null;
        preview.badge.className = 'shrink-0 inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-[10px] font-extrabold ring-1 ' + (s ? s.pill : 'bg-slate-200 text-slate-500 ring-slate-200');
        preview.badge.textContent = s ? s.label : (from && until ? 'Fechas inválidas' : '—');
        preview.name.textContent = form.full_name.value.trim() || 'Nombre de la persona';
        preview.number.textContent = form.cert_number.value.trim() || '--';
        preview.subject.textContent = form.subject.value.trim();
        preview.dates.textContent = from && until ? `Vigente del ${fmtLong(from)} al ${fmtLong(until)}` : 'Captura las fechas de vigencia';
    }

    const setMode = (editing) => {
        document.getElementById('certFormTitle').textContent = editing ? 'Editar certificación' : 'Nueva certificación';
        document.getElementById('certFormIcon').textContent = editing ? 'edit_square' : 'add_circle';
        document.getElementById('certEditingBadge').classList.toggle('hidden', !editing);
    };

    const resetForm = () => {
        form.reset();
        form.cert_id.value = '';
        form.subject.value = defaultSubject;
        form.cert_number.value = autoNumber = nextNumber;
        form.valid_from.value = today;
        form.valid_until.value = '';
        setMode(false);
        updatePreview();
    };

    const focusForm = () => {
        document.getElementById('certFormPanel')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        setTimeout(() => form.full_name.focus(), 300);
    };

    if (form) {
        resetForm();
        form.addEventListener('input', updatePreview);
        document.getElementById('certReset').addEventListener('click', resetForm);
        document.getElementById('certNew')?.addEventListener('click', () => { resetForm(); focusForm(); });
        document.querySelectorAll('.cert-years').forEach((b) => b.addEventListener('click', () => {
            if (!form.valid_from.value) form.valid_from.value = today;
            form.valid_until.value = addYears(form.valid_from.value, parseInt(b.dataset.years, 10));
            updatePreview();
        }));

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            if (form.valid_until.value < form.valid_from.value) {
                Swal.fire({ icon: 'warning', title: 'Revisa la vigencia', text: 'La fecha final no puede ser anterior a la inicial.' });
                return;
            }
            const id = form.cert_id.value;
            const payload = {
                csrf,
                full_name: form.full_name.value.trim(),
                cert_number: form.cert_number.value.trim(),
                subject: form.subject.value.trim(),
                valid_from: form.valid_from.value,
                valid_until: form.valid_until.value,
            };
            try {
                const res = await api(id ? `/admin/api/certifications/${id}` : '/admin/api/certifications', { method: id ? 'PUT' : 'POST', body: payload });
                const newId = id || res.id;
                search.value = '';
                await load();
                resetForm();
                const saved = rows.find((r) => String(r.id) === String(newId));
                const result = await Swal.fire({
                    icon: 'success',
                    title: id ? 'Certificación actualizada' : 'Certificación registrada',
                    text: id ? 'El QR no cambia: los certificados ya impresos siguen funcionando.' : 'Ya puedes descargar su código QR.',
                    showCancelButton: true,
                    confirmButtonText: 'Ver QR',
                    cancelButtonText: 'Cerrar',
                    confirmButtonColor: '#1b456f',
                });
                if (result.isConfirmed && saved) showQr(saved);
            } catch (err) {
                Swal.fire({ icon: 'error', title: 'Error', text: err.message });
            }
        });
    }

    // ---------- Acciones ----------
    list.addEventListener('click', async (e) => {
        const btn = e.target.closest('.cert-act');
        if (!btn) return;
        const r = rows.find((x) => String(x.id) === btn.dataset.id);
        if (!r) return;

        if (btn.dataset.act === 'qr') return showQr(r);

        if (btn.dataset.act === 'edit') {
            form.cert_id.value = r.id;
            form.full_name.value = r.full_name;
            form.cert_number.value = r.cert_number;
            form.subject.value = r.subject;
            form.valid_from.value = r.valid_from;
            form.valid_until.value = r.valid_until;
            setMode(true);
            updatePreview();
            focusForm();
            return;
        }

        if (btn.dataset.act === 'status') {
            const revoke = r.status !== 'REVOKED';
            const ok = await Swal.fire({
                icon: revoke ? 'warning' : 'question',
                title: revoke ? '¿Revocar certificación?' : '¿Reactivar certificación?',
                html: revoke
                    ? `Al escanear el QR de <b>${esc(r.full_name)}</b> se mostrará como <b class="text-red-600">NO VIGENTE / REVOCADA</b>.`
                    : `La certificación de <b>${esc(r.full_name)}</b> volverá a mostrarse según sus fechas de vigencia.`,
                showCancelButton: true,
                confirmButtonText: revoke ? 'Sí, revocar' : 'Sí, reactivar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: revoke ? '#dc2626' : '#1b456f',
            });
            if (!ok.isConfirmed) return;
            try {
                await api(`/admin/api/certifications/status/${r.id}`, { method: 'POST', body: { csrf, status: revoke ? 'REVOKED' : 'ACTIVE' } });
                await load();
            } catch (err) {
                Swal.fire({ icon: 'error', title: 'Error', text: err.message });
            }
            return;
        }

        if (btn.dataset.act === 'delete') {
            const ok = await Swal.fire({
                icon: 'warning',
                title: '¿Eliminar certificación?',
                html: `Se eliminará la certificación <b>Nº ${esc(r.cert_number)}</b> de <b>${esc(r.full_name)}</b>.<br><br>
                       <span class="text-sm text-slate-500">Si el certificado ya fue impreso, su QR mostrará "no encontrado". Para conservar el historial es preferible <b>revocarla</b>.</span>`,
                showCancelButton: true,
                confirmButtonText: 'Eliminar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#dc2626',
            });
            if (!ok.isConfirmed) return;
            try {
                await api(`/admin/api/certifications/${r.id}`, { method: 'DELETE', body: { csrf } });
                if (form && form.cert_id.value === String(r.id)) resetForm();
                await load();
            } catch (err) {
                Swal.fire({ icon: 'error', title: 'Error', text: err.message });
            }
        }
    });

    document.querySelectorAll('.cert-filter').forEach((b) => b.addEventListener('click', () => {
        filter = b.dataset.filter;
        paintFilters();
        render();
    }));

    let searchTimer;
    search.addEventListener('input', () => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(load, 300);
    });

    paintFilters();
    load();
})();
