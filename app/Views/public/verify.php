<section class="tja-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="tja-card tja-card-border">
                    <h3 class="mb-3">Verificar documento</h3>
                    <p>Ingresa el token impreso en el QR para validar el documento.</p>
                    <form class="tja-form" method="get" action="<?php echo base_url('c'); ?>">
                        <div class="input-group">
                            <input type="text" class="form-control" name="token" placeholder="Ejemplo: A1B2C3D4" required>
                            <button class="btn btn-primary tja-btn" type="submit"><i class="fa-solid fa-circle-check"></i> Verificar</button>
                        </div>
                    </form>
                    <div class="tja-note">Si llegaste aqui desde un QR, el sistema abrira automaticamente el detalle.</div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    const form = document.querySelector('form');
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const token = form.token.value.trim();
        if (!token) return;
        window.location.href = '<?php echo base_url('c'); ?>/' + encodeURIComponent(token);
    });
</script>
