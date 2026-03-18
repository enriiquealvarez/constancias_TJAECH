<section class="tja-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="tja-card tja-card-border">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <i class="fa-solid fa-circle-xmark tja-icon-danger"></i>
                        <h3 class="mb-0">Documento no encontrado</h3>
                    </div>
                    <p>El token proporcionado no existe o no esta registrado. Verifica que el QR sea legible o intenta nuevamente.</p>
                    <?php if (!empty($token)): ?>
                        <div class="tja-note">Token consultado: <strong><?php echo htmlspecialchars($token); ?></strong></div>
                    <?php endif; ?>
                    <a href="<?php echo base_url('verificar'); ?>" class="btn btn-secondary tja-btn"><i class="fa-solid fa-rotate-left"></i> Intentar otra vez</a>
                </div>
            </div>
        </div>
    </div>
</section>
