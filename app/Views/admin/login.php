<section class="tja-section tja-auth">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                <div class="tja-auth-panel">
                    <div class="tja-auth-badge"><i class="fa-solid fa-shield-halved"></i>Sistema Seguro</div>
                    <h1 class="tja-auth-title">Acceso administrativo</h1>
                    <p class="tja-auth-text">Gestiona constancias, participantes y auditoria desde un panel seguro.</p>
                    <div class="tja-auth-highlights">
                        <div><i class="fa-solid fa-user-check"></i> Control de usuarios y roles.</div>
                        <div><i class="fa-solid fa-file-circle-check"></i> Emision y verificacion inmediata.</div>
                        <div><i class="fa-solid fa-clipboard-list"></i> Bitacora y trazabilidad de cambios.</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 offset-lg-1">
                <div class="tja-auth-card">
                    <div class="tja-auth-card-header">
                        <h3>Iniciar sesion</h3>
                        <p>Usa tu correo institucional para acceder.</p>
                    </div>
                    <form id="loginForm">
                        <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf); ?>">
                        <div class="mb-3">
                            <label class="form-label">Correo institucional</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contrase&ntilde;a</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <button class="btn btn-primary tja-btn tja-btn-lg w-100" type="submit"><i class="fa-solid fa-right-to-bracket"></i> Ingresar</button>
                        <div class="mt-3 text-center">
                            <a class="tja-auth-link" href="<?php echo base_url('admin/forgot'); ?>">&iquest;Olvidaste tu contrase&ntilde;a?</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.getElementById('loginForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const form = e.target;
        const payload = {
            email: form.email.value.trim(),
            password: form.password.value,
            csrf: form.csrf.value
        };
        const res = await fetch('<?php echo base_url('admin/login'); ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (data.ok) {
            window.location.href = data.redirect;
        } else {
            Swal.fire({ icon: 'error', title: 'Acceso denegado', text: data.message || 'Error' });
        }
    });
</script>
