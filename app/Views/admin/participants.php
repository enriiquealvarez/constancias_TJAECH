<section class="tja-section">
    <div class="container">
        <div class="row g-4">
            <?php if (!empty($can_manage)): ?>
                <div class="col-lg-4">
                    <div class="tja-card tja-card-border">
                        <h4>Registrar participante</h4>
                        <form id="participantForm">
                            <input type="hidden" name="id">
                            <div class="mb-2">
                                <label class="form-label">Nombre completo</label>
                                <input class="form-control" name="full_name" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Correo (opcional)</label>
                                <input type="email" class="form-control" name="email">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tipo</label>
                                <select class="form-select" name="type">
                                    <option value="INTERNAL">Interno</option>
                                    <option value="EXTERNAL">Externo</option>
                                </select>
                            </div>
                            <button class="btn btn-primary tja-btn w-100" type="submit"><i class="fa-solid fa-floppy-disk"></i> Guardar</button>
                            <button class="btn btn-outline-light tja-btn-outline w-100 mt-2" type="button" id="participantReset"><i class="fa-solid fa-broom"></i> Limpiar</button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
            <div class="col-lg-8">
                <div class="tja-card tja-card-border">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4>Listado de participantes</h4>
                        <input class="form-control tja-search" id="participantSearch" placeholder="Buscar por nombre o correo">
                    </div>
                    <div class="table-responsive">
                        <div class="tja-table-wrap">
                            <table class="table tja-table">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Correo</th>
                                    <th>Tipo</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="participantTable" data-can-manage="<?php echo !empty($can_manage) ? '1' : '0'; ?>"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
