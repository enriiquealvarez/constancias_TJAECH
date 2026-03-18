<section class="tja-section">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="tja-card tja-metric" id="metric-total">
                    <div class="tja-metric-icon"><i class="fa-solid fa-file-circle-check"></i></div>
                    <div>
                        <div class="tja-label">Constancias emitidas</div>
                        <div class="tja-metric-value">0</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="tja-card tja-metric" id="metric-verified">
                    <div class="tja-metric-icon"><i class="fa-solid fa-badge-check"></i></div>
                    <div>
                        <div class="tja-label">Verificadas</div>
                        <div class="tja-metric-value">0</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="tja-card tja-metric" id="metric-not">
                    <div class="tja-metric-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
                    <div>
                        <div class="tja-label">No verificadas</div>
                        <div class="tja-metric-value">0</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-lg-8">
                <div class="tja-card tja-card-border">
                    <h4>Acciones rapidas</h4>
                    <p>Administra cursos, participantes y constancias desde el panel.</p>
                    <div class="d-flex flex-wrap gap-2">
                        <?php if (\app\Core\Auth::can('manage_courses') || \app\Core\Auth::can('view_courses')): ?>
                            <a href="<?php echo base_url('admin/courses'); ?>" class="btn btn-secondary tja-btn"><i class="fa-solid fa-school"></i> Cursos</a>
                        <?php endif; ?>
                        <?php if (\app\Core\Auth::can('manage_participants') || \app\Core\Auth::can('view_participants')): ?>
                            <a href="<?php echo base_url('admin/participants'); ?>" class="btn btn-secondary tja-btn"><i class="fa-solid fa-users"></i> Participantes</a>
                        <?php endif; ?>
                        <?php if (\app\Core\Auth::can('manage_certificates') || \app\Core\Auth::can('view_certificates')): ?>
                            <a href="<?php echo base_url('admin/certificates'); ?>" class="btn btn-secondary tja-btn"><i class="fa-solid fa-qrcode"></i> Constancias</a>
                        <?php endif; ?>
                        <?php if (\app\Core\Auth::can('view_audit')): ?>
                            <a href="<?php echo base_url('admin/audit'); ?>" class="btn btn-secondary tja-btn"><i class="fa-solid fa-clipboard-list"></i> Auditoria</a>
                        <?php endif; ?>
                        <?php if (\app\Core\Auth::can('manage_users')): ?>
                            <a href="<?php echo base_url('admin/users'); ?>" class="btn btn-secondary tja-btn"><i class="fa-solid fa-user-gear"></i> Usuarios</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="tja-card tja-card-border">
                    <h5>Notas de seguridad</h5>
                    <p>Verifica siempre los datos antes de emitir una constancia. Todas las acciones quedan registradas en auditoria.</p>
                </div>
            </div>
        </div>
    </div>
</section>
