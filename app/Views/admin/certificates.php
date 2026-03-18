<section class="tja-section">
    <div class="container">
        <div class="row g-4">
            <?php if (!empty($can_manage)): ?>
                <div class="col-lg-4">
                    <div class="tja-card tja-card-border">
                        <h4>Emitir constancia</h4>
                        <form id="certificateForm">
                            <input type="hidden" name="id">
                            <div class="mb-2">
                                <label class="form-label">Participante</label>
                                <select class="form-select" name="participant_id" required></select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Curso</label>
                                <select class="form-select" name="course_id" required></select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Tipo de documento</label>
                                <select class="form-select" name="doc_type">
                                    <option>Constancia</option>
                                    <option>Certificado</option>
                                    <option>Reconocimiento</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Estatus inicial</label>
                                <select class="form-select" name="status">
                                    <option value="NOT_VERIFIED">No verificado</option>
                                    <option value="VERIFIED">Verificado</option>
                                </select>
                            </div>
                            <button class="btn btn-primary tja-btn w-100" type="submit"><i class="fa-solid fa-qrcode"></i> Emitir</button>
                            <button class="btn btn-outline-light tja-btn-outline w-100 mt-2" type="button" id="certificateReset"><i class="fa-solid fa-broom"></i> Limpiar</button>
                        </form>
                        <div class="tja-note mt-3" id="issueResult"></div>
                    </div>
                </div>
            <?php endif; ?>
            <div class="col-lg-8">
                <div class="tja-card tja-card-border">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4>Constancias emitidas</h4>
                        <div class="d-flex gap-2">
                            <input class="form-control tja-search" id="certificateSearch" placeholder="Buscar por nombre, curso o token">
                            <a class="btn btn-outline-light tja-btn-outline" href="<?php echo base_url('admin/api/certificates/export'); ?>"><i class="fa-solid fa-file-csv"></i> CSV</a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <div class="tja-table-wrap">
                            <table class="table tja-table">
                            <thead>
                                <tr>
                                    <th>Participante</th>
                                    <th>Curso</th>
                                    <th>Tipo</th>
                                    <th>Estatus</th>
                                    <th>Token</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="certificateTable" data-can-manage="<?php echo !empty($can_manage) ? '1' : '0'; ?>"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
