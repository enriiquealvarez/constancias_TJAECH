<section class="tja-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5">
                <div class="tja-card tja-card-border">
                    <h3 class="mb-3">Restablecer contrase&ntilde;a</h3>
                    <form id="resetForm">
                        <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf); ?>">
                        <div class="mb-2">
                            <label class="form-label">Correo</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Codigo (6 digitos)</label>
                            <input type="text" class="form-control" name="token" maxlength="6" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Nueva contrase&ntilde;a</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirmar contrase&ntilde;a</label>
                            <input type="password" class="form-control" name="confirm" required>
                        </div>
                        <button class="btn btn-primary tja-btn w-100" type="submit"><i class="fa-solid fa-key"></i> Actualizar contrase&ntilde;a</button>
                    </form>
                    <div class="mt-3">
                        <a href="<?php echo base_url('admin/login'); ?>">Volver al login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.getElementById('resetForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const form = e.target;
        const payload = {
            email: form.email.value.trim(),
            token: form.token.value.trim(),
            password: form.password.value,
            confirm: form.confirm.value,
            csrf: form.csrf.value
        };
        try {
            const res = await fetch('<?php echo base_url('admin/reset'); ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const text = await res.text();
            let data = null;
            try {
                data = JSON.parse(text);
            } catch (e) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Respuesta invalida del servidor: ' + text.slice(0, 200) });
                return;
            }
            if (data.ok) {
                Swal.fire({ icon: 'success', title: 'Actualizada', text: 'Tu contrase&ntilde;a se actualizo correctamente.' })
                    .then(() => { window.location.href = data.redirect; });
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Error' });
            }
        } catch (err) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo procesar la solicitud.' });
        }
    });
</script>
