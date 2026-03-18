<section class="tja-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5">
                <div class="tja-card tja-card-border">
                    <h3 class="mb-3">Recuperar acceso</h3>
                    <p>Ingresa tu correo registrado y te enviaremos un codigo de 6 digitos.</p>
                    <form id="forgotForm">
                        <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf); ?>">
                        <div class="mb-3">
                            <label class="form-label">Correo</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <button class="btn btn-primary tja-btn w-100" type="submit"><i class="fa-solid fa-paper-plane"></i> Enviar codigo</button>
                    </form>
                    <div class="mt-3">
                        <a href="<?php echo base_url('admin/reset'); ?>">Ya tengo un codigo</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.getElementById('forgotForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const form = e.target;
        const payload = {
            email: form.email.value.trim(),
            csrf: form.csrf.value
        };
        try {
            const res = await fetch('<?php echo base_url('admin/forgot'); ?>', {
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
                Swal.fire({ icon: 'success', title: 'Listo', text: data.message });
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Error' });
            }
        } catch (err) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo procesar la solicitud.' });
        }
    });
</script>
