<?php
$status = $record['status'] === 'VERIFIED';
?>
<section class="tja-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="tja-card tja-card-border">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h3 class="mb-0">Resultado de verificacion</h3>
                        <span class="badge <?php echo $status ? 'tja-badge-success' : 'tja-badge-danger'; ?>">
                            <i class="fa-solid <?php echo $status ? 'fa-circle-check' : 'fa-circle-xmark'; ?>"></i>
                            <?php echo $status ? 'VERIFICADO' : 'NO VERIFICADO'; ?>
                        </span>
                    </div>
                    <div class="tja-summary">
                        <div>
                            <div class="tja-label">Nombre completo</div>
                            <div class="tja-value"><?php echo htmlspecialchars($record['full_name']); ?></div>
                        </div>
                        <div>
                            <div class="tja-label">Curso</div>
                            <div class="tja-value"><?php echo htmlspecialchars($record['course_name']); ?> <?php echo htmlspecialchars($record['edition']); ?></div>
                        </div>
                        <div>
                            <div class="tja-label">Tipo de documento</div>
                            <div class="tja-value"><?php echo htmlspecialchars($record['doc_type']); ?></div>
                        </div>
                        <div>
                            <div class="tja-label">Token</div>
                            <div class="tja-value"><?php echo htmlspecialchars($record['token']); ?></div>
                        </div>
                    </div>
                    <div class="tja-note">Para informacion adicional, contacte al Tribunal de Justicia Administrativa del Estado de Chiapas.</div>
                </div>
            </div>
        </div>
    </div>
</section>
