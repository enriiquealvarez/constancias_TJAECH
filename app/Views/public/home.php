<section class="tja-hero">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <div class="tja-hero-card">
                    <span class="tja-chip"><i class="fa-solid fa-shield-check"></i> Verificacion oficial</span>
                    <h1>Verificacion de constancias, certificados y reconocimientos</h1>
                    <p>Portal institucional del Tribunal de Justicia Administrativa del Estado de Chiapas para validar documentos emitidos por la Coordinacion de Capacitacion.</p>
                    <div class="d-flex flex-column flex-md-row gap-3">
                        <a href="<?php echo base_url('verificar'); ?>" class="btn btn-primary tja-btn"><i class="fa-solid fa-qrcode"></i> Verificar documento</a>
                        <a href="<?php echo base_url('admin/login'); ?>" class="btn btn-outline-light tja-btn-outline"><i class="fa-solid fa-user-shield"></i> Acceso administrativo</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="tja-feature-grid">
                    <div class="tja-card">
                        <i class="fa-solid fa-qrcode"></i>
                        <h5>QR seguro</h5>
                        <p>Tokens unicos para cada documento emitido.</p>
                    </div>
                    <div class="tja-card">
                        <i class="fa-solid fa-user-check"></i>
                        <h5>Validacion publica</h5>
                        <p>Resultados inmediatos sin necesidad de login.</p>
                    </div>
                    <div class="tja-card">
                        <i class="fa-solid fa-chart-line"></i>
                        <h5>Control interno</h5>
                        <p>Panel administrativo con metricas y auditoria.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="tja-section">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="tja-card tja-card-border">
                    <h4>Validar un documento</h4>
                    <p>Escanea el QR impreso en el documento o ingresa el token manualmente.</p>
                    <a href="<?php echo base_url('verificar'); ?>" class="btn btn-secondary tja-btn"><i class="fa-solid fa-magnifying-glass"></i> Ir a verificacion</a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="tja-card tja-card-border">
                    <h4>Institucional</h4>
                    <p>Este sistema garantiza transparencia y autenticidad de los documentos emitidos por el Tribunal.</p>
                    <span class="badge text-bg-warning tja-badge-soft">TJA Chiapas</span>
                </div>
            </div>
        </div>
    </div>
</section>
