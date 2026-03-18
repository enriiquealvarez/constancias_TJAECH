(() => {
    const base = document.body.getAttribute('data-base') || '';
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const loader = document.getElementById('tja-loader');
    let pending = 0;

    const setLoading = (active) => {
        if (!loader) return;
        if (active) {
            pending += 1;
            loader.classList.remove('d-none');
        } else {
            pending = Math.max(0, pending - 1);
            if (pending === 0) {
                loader.classList.add('d-none');
            }
        }
    };

    const fetchJson = async (url, options = {}) => {
        setLoading(true);
        const opts = Object.assign({
            headers: { 'Content-Type': 'application/json' }
        }, options);
        if (opts.body && typeof opts.body !== 'string') {
            opts.body = JSON.stringify(opts.body);
        }
        try {
            const res = await fetch(base + url.replace(/^\//, ''), opts);
            const text = await res.text();
            let data = {};
            if (text) {
                try {
                    data = JSON.parse(text);
                } catch (err) {
                    throw new Error('Respuesta invalida del servidor.');
                }
            }
            if (!res.ok || data.ok === false) {
                const err = new Error(data.message || 'Error en la solicitud');
                err.status = res.status;
                err.data = data;
                throw err;
            }
            return data;
        } finally {
            setLoading(false);
        }
    };

    const page = document.querySelector('main')?.getAttribute('data-page') || '';

    if (page === 'Dashboard') {
        const loadMetrics = () => {
            fetchJson('/admin/api/metrics')
                .then(data => {
                    document.querySelector('#metric-total .tja-metric-value').textContent = data.total;
                    document.querySelector('#metric-verified .tja-metric-value').textContent = data.verified;
                    document.querySelector('#metric-not .tja-metric-value').textContent = data.not_verified;
                })
                .catch(() => {});
        };
        loadMetrics();
    }

    if (page === 'Cursos') {
        const table = document.getElementById('courseTable');
        const form = document.getElementById('courseForm');
        const search = document.getElementById('courseSearch');
        const reset = document.getElementById('courseReset');
        const canManage = table?.getAttribute('data-can-manage') === '1';

        let current = [];
        const load = async (q = '') => {
            const data = await fetchJson('/admin/api/courses?q=' + encodeURIComponent(q));
            current = data.data;
            table.innerHTML = data.data.map(r => `
                <tr>
                    <td>${r.name}</td>
                    <td>${r.edition || ''}</td>
                    <td>${r.start_date || ''} ${r.end_date ? ' - ' + r.end_date : ''}</td>
                    <td>${r.modality || ''}</td>
                    <td class="text-end">
                        ${canManage ? `<div class="tja-actions">
                            <button class="btn btn-sm tja-btn-outline tja-action-btn tja-btn-edit" data-edit="${r.id}"><i class="fa-regular fa-pen-to-square"></i> Editar</button>
                            <button class="btn btn-sm tja-btn-outline tja-btn-danger tja-action-btn" data-del="${r.id}"><i class="fa-regular fa-trash-can"></i> Eliminar</button>
                        </div>` : ''}
                    </td>
                </tr>
            `).join('');
        };

        load();

        search.addEventListener('input', e => load(e.target.value));

        if (reset && form) {
            reset.addEventListener('click', () => form.reset());
        }

        if (form) {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const payload = Object.fromEntries(new FormData(form).entries());
                payload.csrf = csrf;
                const id = payload.id;
                delete payload.id;
                try {
                    if (id) {
                        await fetchJson(`/admin/api/courses/${id}`, { method: 'PUT', body: payload });
                    } else {
                        await fetchJson('/admin/api/courses', { method: 'POST', body: payload });
                    }
                    form.reset();
                    load(search.value);
                    Swal.fire({ icon: 'success', title: 'Guardado', timer: 1200, showConfirmButton: false });
                } catch (err) {
                    Swal.fire({ icon: 'error', title: 'Error', text: err.message });
                }
            });
        }

        table.addEventListener('click', async (e) => {
            const edit = e.target.getAttribute('data-edit');
            const del = e.target.getAttribute('data-del');
            if (edit && canManage) {
                const row = current.find(c => String(c.id) === String(edit));
                if (!row) return;
                form.id.value = row.id;
                form.name.value = row.name;
                form.edition.value = row.edition || '';
                form.start_date.value = row.start_date || '';
                form.end_date.value = row.end_date || '';
                form.modality.value = row.modality || '';
                form.area.value = row.area || '';
            }
            if (del && canManage) {
                const confirm = await Swal.fire({
                    icon: 'warning',
                    title: 'Eliminar curso',
                    text: 'Esta accion no se puede revertir.',
                    showCancelButton: true,
                    confirmButtonText: 'Si, eliminar'
                });
                if (confirm.isConfirmed) {
                    try {
                        await fetchJson(`/admin/api/courses/${del}`, { method: 'DELETE', body: { csrf } });
                        load(search.value);
                    } catch (err) {
                        Swal.fire({ icon: 'error', title: 'Error', text: err.message });
                    }
                }
            }
        });
    }

    if (page === 'Participantes') {
        const table = document.getElementById('participantTable');
        const form = document.getElementById('participantForm');
        const search = document.getElementById('participantSearch');
        const reset = document.getElementById('participantReset');
        const canManage = table?.getAttribute('data-can-manage') === '1';

        let current = [];
        const load = async (q = '') => {
            const data = await fetchJson('/admin/api/participants?q=' + encodeURIComponent(q));
            current = data.data;
            table.innerHTML = data.data.map(r => `
                <tr>
                    <td>${r.full_name}</td>
                    <td>${r.email || ''}</td>
                    <td>${r.type === 'EXTERNAL' ? 'Externo' : 'Interno'}</td>
                    <td class="text-end">
                        ${canManage ? `<div class="tja-actions">
                            <button class="btn btn-sm tja-btn-outline tja-action-btn tja-btn-edit" data-edit="${r.id}"><i class="fa-regular fa-pen-to-square"></i> Editar</button>
                            <button class="btn btn-sm tja-btn-outline tja-btn-danger tja-action-btn" data-del="${r.id}"><i class="fa-regular fa-trash-can"></i> Eliminar</button>
                        </div>` : ''}
                    </td>
                </tr>
            `).join('');
        };

        load();

        search.addEventListener('input', e => load(e.target.value));
        if (reset && form) {
            reset.addEventListener('click', () => form.reset());
        }

        if (form) {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const payload = Object.fromEntries(new FormData(form).entries());
                payload.csrf = csrf;
                const id = payload.id;
                delete payload.id;
                try {
                    if (id) {
                        await fetchJson(`/admin/api/participants/${id}`, { method: 'PUT', body: payload });
                    } else {
                        await fetchJson('/admin/api/participants', { method: 'POST', body: payload });
                    }
                    form.reset();
                    load(search.value);
                    Swal.fire({ icon: 'success', title: 'Guardado', timer: 1200, showConfirmButton: false });
                } catch (err) {
                    Swal.fire({ icon: 'error', title: 'Error', text: err.message });
                }
            });
        }

        table.addEventListener('click', async (e) => {
            const edit = e.target.getAttribute('data-edit');
            const del = e.target.getAttribute('data-del');
            if (edit && canManage) {
                const row = current.find(p => String(p.id) === String(edit));
                if (!row) return;
                form.id.value = row.id;
                form.full_name.value = row.full_name;
                form.email.value = row.email || '';
                form.type.value = row.type;
            }
            if (del && canManage) {
                const confirm = await Swal.fire({
                    icon: 'warning',
                    title: 'Eliminar participante',
                    text: 'Esta accion no se puede revertir.',
                    showCancelButton: true,
                    confirmButtonText: 'Si, eliminar'
                });
                if (confirm.isConfirmed) {
                    try {
                        await fetchJson(`/admin/api/participants/${del}`, { method: 'DELETE', body: { csrf } });
                        load(search.value);
                    } catch (err) {
                        Swal.fire({ icon: 'error', title: 'Error', text: err.message });
                    }
                }
            }
        });
    }

    if (page === 'Constancias') {
        const table = document.getElementById('certificateTable');
        const form = document.getElementById('certificateForm');
        const search = document.getElementById('certificateSearch');
        const reset = document.getElementById('certificateReset');
        const issueResult = document.getElementById('issueResult');
        const canManage = table?.getAttribute('data-can-manage') === '1';

        const loadSelects = async () => {
            if (!form) return;
            const courses = await fetchJson('/admin/api/courses');
            const participants = await fetchJson('/admin/api/participants');
            form.course_id.innerHTML = courses.data.map(c => `<option value="${c.id}">${c.name} ${c.edition || ''}</option>`).join('');
            form.participant_id.innerHTML = participants.data.map(p => `<option value="${p.id}">${p.full_name}</option>`).join('');
        };

        const load = async (q = '') => {
            const data = await fetchJson('/admin/api/certificates?q=' + encodeURIComponent(q));
            table.innerHTML = data.data.map(r => {
                const isVerified = r.status === 'VERIFIED';
                const manageButtons = canManage ? `
                    <button class="p-1.5 hover:bg-slate-100 rounded-md text-slate-400 hover:text-blue-600 transition-all" data-status="${r.id}" data-current="${r.status}" title="Cambiar estado"><span class="material-symbols-outlined text-[18px]">sync_alt</span></button>
                    <button class="p-1.5 hover:bg-red-50 rounded-md text-slate-400 hover:text-red-500 transition-all" data-del="${r.id}" title="Eliminar"><span class="material-symbols-outlined text-[18px]">delete</span></button>
                ` : '';
                return `
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-4">
                            <div class="text-sm font-semibold text-primary truncate max-w-[200px]">${r.full_name}</div>
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-600 truncate max-w-[200px]">${r.course_name}</td>
                        <td class="px-5 py-4">
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-slate-100 text-slate-600 uppercase">${r.doc_type}</span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold ${isVerified ? 'text-success bg-success/10' : 'text-orange-600 bg-orange-100'} px-2 py-1 rounded-full uppercase">
                                <span class="w-1.5 h-1.5 rounded-full ${isVerified ? 'bg-success' : 'bg-orange-600'}"></span> ${isVerified ? 'Verificado' : 'Pendiente'}
                            </span>
                        </td>
                        <td class="px-5 py-4 font-mono text-xs text-slate-500 bg-slate-50/50 rounded pointer-events-auto selection:bg-corporate-blue selection:text-white">${r.token}</td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button class="p-1.5 hover:bg-slate-100 rounded-md text-slate-400 hover:text-primary transition-all" data-qr="${r.token}" title="Ver QR"><span class="material-symbols-outlined text-[18px]">qr_code_2</span></button>
                                <button class="p-1.5 hover:bg-slate-100 rounded-md text-slate-400 hover:text-primary transition-all" data-copy="${r.token}" title="Copiar URL"><span class="material-symbols-outlined text-[18px]">link</span></button>
                                ${manageButtons}
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
        };

        loadSelects();
        load();

        search.addEventListener('input', e => load(e.target.value));
        if (reset && form) {
            reset.addEventListener('click', () => {
                form.reset();
                if (issueResult) {
                    issueResult.textContent = '';
                }
            });
        }

        if (form) {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const payload = Object.fromEntries(new FormData(form).entries());
                payload.csrf = csrf;
                try {
                    const data = await fetchJson('/admin/api/certificates/issue', { method: 'POST', body: payload });
                    form.reset();
                    load(search.value);
                    if (data.token) {
                        const url = base + 'c/' + data.token;
                        issueResult.textContent = 'URL para QR: ' + url;
                    }
                    Swal.fire({ icon: 'success', title: 'Constancia emitida', timer: 1200, showConfirmButton: false });
                } catch (err) {
                    Swal.fire({ icon: 'error', title: 'Error', text: err.message });
                }
            });
        }

        table.addEventListener('click', async (e) => {
            const btn = e.target.closest('button');
            if (!btn) return;
            const status = btn.getAttribute('data-status');
            const current = btn.getAttribute('data-current');
            const copy = btn.getAttribute('data-copy');
            const del = btn.getAttribute('data-del');
            const qr = btn.getAttribute('data-qr');

            if (qr) {
                const url = base + 'c/' + qr;
                const img = 'https://api.qrserver.com/v1/create-qr-code/?size=900x900&margin=20&data=' + encodeURIComponent(url);
                Swal.fire({
                    title: 'Generando QR',
                    text: 'Espera un momento...',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                const w = window.open('', '_blank');
                if (w) {
                    w.document.write(`<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>QR de constancia</title>
  <style>
    body{margin:0;font-family:Arial,Helvetica,sans-serif;background:#ffffff;color:#111426;display:flex;align-items:center;justify-content:center;min-height:100vh;}
    .card{background:#fff;border-radius:16px;padding:24px;border:1px solid rgba(27,68,110,.12);box-shadow:0 12px 26px rgba(27,68,110,.12);text-align:center;max-width:520px;width:90%;}
    img{width:320px;max-width:100%;height:auto;}
    .url{font-size:12px;color:#1B446E;margin-top:10px;word-break:break-all;}
    a{display:inline-block;margin-top:14px;padding:8px 12px;border-radius:999px;border:1px solid rgba(27,68,110,.25);background:rgba(27,68,110,.08);color:#111426;text-decoration:none;font-weight:600;}
  </style>
</head>
<body>
  <div class="card">
    <h3>QR de constancia</h3>
    <img src="${img}" alt="QR">
    <div class="url">${url}</div>
    <a href="${img}" download="qr-${qr}.png">Descargar PNG</a>
  </div>
</body>
</html>`);
                    w.document.close();
                }
                Swal.close();
                return;
            }

            if (copy) {
                const url = base + 'c/' + copy;
                await navigator.clipboard.writeText(url);
                Swal.fire({ icon: 'success', title: 'URL copiada', timer: 1200, showConfirmButton: false });
            }

            if (status && canManage) {
                const next = current === 'VERIFIED' ? 'NOT_VERIFIED' : 'VERIFIED';
                try {
                    await fetchJson(`/admin/api/certificates/status/${status}`, { method: 'POST', body: { csrf, status: next } });
                    load(search.value);
                } catch (err) {
                    Swal.fire({ icon: 'error', title: 'Error', text: err.message });
                }
            }

            if (del && canManage) {
                const confirm = await Swal.fire({
                    icon: 'warning',
                    title: 'Eliminar constancia',
                    text: 'Esta accion no se puede revertir.',
                    showCancelButton: true,
                    confirmButtonText: 'Si, eliminar'
                });
                if (confirm.isConfirmed) {
                    try {
                        await fetchJson(`/admin/api/certificates/${del}`, { method: 'DELETE', body: { csrf } });
                        load(search.value);
                    } catch (err) {
                        Swal.fire({ icon: 'error', title: 'Error', text: err.message });
                    }
                }
            }
        });
    }

    if (page === 'Auditoria') {
        const table = document.getElementById('auditTable');
        const search = document.getElementById('auditSearch');
        const meta = document.getElementById('auditMeta');
        const prev = document.getElementById('auditPrev');
        const next = document.getElementById('auditNext');
        const action = document.getElementById('auditAction');
        const entity = document.getElementById('auditEntity');
        const dateFrom = document.getElementById('auditFrom');
        const dateTo = document.getElementById('auditTo');
        const sort = document.getElementById('auditSort');
        const dir = document.getElementById('auditDir');
        const exportLink = document.querySelector('a[href*=\"/admin/api/audit/export\"]');
        let pageNum = 1;
        const perPage = 10;

        const buildQuery = (q) => {
            const params = new URLSearchParams();
            params.set('q', q || '');
            params.set('page', pageNum);
            params.set('per_page', perPage);
            if (action?.value) params.set('action', action.value);
            if (entity?.value) params.set('entity', entity.value);
            if (dateFrom?.value) params.set('date_from', dateFrom.value);
            if (dateTo?.value) params.set('date_to', dateTo.value);
            if (sort?.value) params.set('sort', sort.value);
            if (dir?.value) params.set('dir', dir.value);
            return params.toString();
        };

        const syncExport = () => {
            if (!exportLink) return;
            exportLink.href = base + 'admin/api/audit/export?' + buildQuery(search.value);
        };

        const load = async (q = '') => {
            try {
                const data = await fetchJson('/admin/api/audit?' + buildQuery(q));
                table.innerHTML = data.data.map(r => `
                    <tr>
                        <td>${r.created_at}</td>
                        <td>${r.user_name}</td>
                        <td>${r.action}</td>
                        <td>${r.entity}</td>
                        <td>${r.entity_id}</td>
                        <td>${r.ip}</td>
                    </tr>
                `).join('');
                meta.textContent = `Pagina ${data.meta.page} de ${data.meta.pages}`;
                prev.disabled = data.meta.page <= 1;
                next.disabled = data.meta.page >= data.meta.pages;
                syncExport();
            } catch (err) {
                Swal.fire({ icon: 'error', title: 'Error', text: err.message });
            }
        };

        load();
        search.addEventListener('input', e => { pageNum = 1; load(e.target.value); });
        [action, entity, dateFrom, dateTo, sort, dir].forEach((el) => {
            if (!el) return;
            el.addEventListener('change', () => { pageNum = 1; load(search.value); });
        });
        prev.addEventListener('click', () => { pageNum = Math.max(1, pageNum - 1); load(search.value); });
        next.addEventListener('click', () => { pageNum += 1; load(search.value); });
    }

    if (page === 'Usuarios') {
        const cards = document.getElementById('userCards');
        const form = document.getElementById('userForm');
        const search = document.getElementById('userSearch');
        const reset = document.getElementById('userReset');
        const rolesAll = document.getElementById('rolesAll');
        let current = [];

        const load = async (q = '') => {
            const data = await fetchJson('/admin/api/users?q=' + encodeURIComponent(q));
            current = data.data;
            cards.innerHTML = data.data.map(r => `
                <div class="tja-user-card">
                    <div class="tja-user-head">
                        <div>
                            <div class="tja-user-name">${r.name}</div>
                            <div class="tja-user-email">${r.email}</div>
                        </div>
                        <span class="badge ${r.status === 'ACTIVE' ? 'tja-badge-success' : 'tja-badge-danger'}">${r.status === 'ACTIVE' ? 'Activo' : 'Deshabilitado'}</span>
                    </div>
                    <div class="tja-user-meta">
                        <div>
                            <div class="tja-user-label">Roles</div>
                            <div class="tja-user-value">${(r.roles || []).join(', ')}</div>
                        </div>
                        <div>
                            <div class="tja-user-label">Alta</div>
                            <div class="tja-user-value">${r.created_at}</div>
                        </div>
                    </div>
                    <div class="tja-actions tja-actions-row">
                        <button class="btn btn-sm tja-btn-outline tja-action-btn tja-btn-edit" data-edit="${r.id}"><i class="fa-regular fa-pen-to-square"></i> Editar</button>
                        <button class="btn btn-sm tja-btn-outline tja-btn-danger tja-action-btn" data-del="${r.id}"><i class="fa-regular fa-trash-can"></i> Eliminar</button>
                        <button class="btn btn-sm tja-btn-outline tja-action-btn tja-btn-status" data-status="${r.id}" data-current="${r.status}"><i class="fa-regular fa-circle-${r.status === 'ACTIVE' ? 'check' : 'xmark'}"></i> ${r.status === 'ACTIVE' ? 'Deshabilitar' : 'Habilitar'}</button>
                    </div>
                </div>
            `).join('');
        };

        load();
        search.addEventListener('input', e => load(e.target.value));
        reset.addEventListener('click', () => {
            form.reset();
            form.querySelectorAll('input[name="roles[]"]').forEach((input) => {
                input.checked = false;
            });
            if (rolesAll) {
                rolesAll.checked = false;
            }
        });

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const payload = Object.fromEntries(new FormData(form).entries());
            payload.roles = Array.from(form.querySelectorAll('input[name="roles[]"]:checked')).map((input) => input.value);
            delete payload['roles[]'];
            payload.csrf = csrf;
            const id = payload.id;
            delete payload.id;
            try {
                if (id) {
                    await fetchJson(`/admin/api/users/${id}`, { method: 'PUT', body: payload });
                } else {
                    if (!payload.password) {
                        Swal.fire({ icon: 'warning', title: 'Contrasena requerida', text: 'Define una contrasena para nuevos usuarios.' });
                        return;
                    }
                    await fetchJson('/admin/api/users', { method: 'POST', body: payload });
                }
                form.reset();
                load(search.value);
                Swal.fire({ icon: 'success', title: 'Guardado', timer: 1200, showConfirmButton: false });
            } catch (err) {
                Swal.fire({ icon: 'error', title: 'Error', text: err.message });
            }
        });

        cards.addEventListener('click', async (e) => {
            const btn = e.target.closest('button');
            if (!btn) return;
            const edit = btn.getAttribute('data-edit');
            const del = btn.getAttribute('data-del');
            if (edit) {
                const row = current.find(u => String(u.id) === String(edit));
                if (!row) return;
                form.id.value = row.id;
                form.name.value = row.name;
                form.email.value = row.email;
                const roles = row.roles || [];
                form.querySelectorAll('input[name="roles[]"]').forEach((input) => {
                    input.checked = roles.includes(input.value);
                });
                if (rolesAll) {
                    const allChecked = form.querySelectorAll('input[name=\"roles[]\"]:checked').length === form.querySelectorAll('input[name=\"roles[]\"]').length;
                    rolesAll.checked = allChecked;
                }
                form.status.value = row.status;
                form.password.value = '';
            }
            if (del) {
                const confirm = await Swal.fire({
                    icon: 'warning',
                    title: 'Eliminar usuario',
                    text: 'Esta accion no se puede revertir.',
                    showCancelButton: true,
                    confirmButtonText: 'Si, eliminar'
                });
                if (confirm.isConfirmed) {
                    try {
                        await fetchJson(`/admin/api/users/${del}`, { method: 'DELETE', body: { csrf } });
                        load(search.value);
                    } catch (err) {
                        if (err.status === 409) {
                            const force = await Swal.fire({
                                icon: 'warning',
                                title: 'Eliminar con reasignacion',
                                text: 'El usuario tiene auditoria asociada. Deseas reasignar esa auditoria a tu usuario y eliminarlo?',
                                showCancelButton: true,
                                confirmButtonText: 'Si, reasignar y eliminar'
                            });
                            if (force.isConfirmed) {
                                try {
                                    await fetchJson(`/admin/api/users/${del}`, { method: 'DELETE', body: { csrf, reassign_audit: true } });
                                    load(search.value);
                                } catch (forceErr) {
                                    Swal.fire({ icon: 'error', title: 'Error', text: forceErr.message });
                                }
                            }
                        } else {
                            Swal.fire({ icon: 'error', title: 'Error', text: err.message });
                        }
                    }
                }
            }
            const status = btn.getAttribute('data-status');
            const currentStatus = btn.getAttribute('data-current');
            if (status) {
                const next = currentStatus === 'ACTIVE' ? 'DISABLED' : 'ACTIVE';
                try {
                    await fetchJson(`/admin/api/users/status/${status}`, { method: 'POST', body: { csrf, status: next } });
                    load(search.value);
                } catch (err) {
                    Swal.fire({ icon: 'error', title: 'Error', text: err.message });
                }
            }
        });
        if (rolesAll) {
            rolesAll.addEventListener('change', () => {
                const checked = rolesAll.checked;
                form.querySelectorAll('input[name="roles[]"]').forEach((input) => {
                    input.checked = checked;
                });
            });
        }
        form.querySelectorAll('input[name="roles[]"]').forEach((input) => {
            input.addEventListener('change', () => {
                if (!rolesAll) return;
                const total = form.querySelectorAll('input[name="roles[]"]').length;
                const checked = form.querySelectorAll('input[name="roles[]"]:checked').length;
                rolesAll.checked = total === checked;
            });
        });
    }
})();