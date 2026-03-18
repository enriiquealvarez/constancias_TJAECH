<section class="tja-section">
    <div class="container">
        <div class="row g-4">
            <?php if (!empty($can_manage)): ?>
                <div class="col-lg-4">
                    <div class="tja-card tja-card-border">
                        <h4>Registrar curso</h4>
                        <form id="courseForm">
                            <input type="hidden" name="id">
                            <div class="mb-2">
                                <label class="form-label">Nombre</label>
                                <input class="form-control" name="name" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Edicion</label>
                                <input class="form-control" name="edition">
                            </div>
                            <div class="row">
                                <div class="col-6 mb-2">
                                    <label class="form-label">Inicio</label>
                                    <input type="date" class="form-control" name="start_date">
                                </div>
                                <div class="col-6 mb-2">
                                    <label class="form-label">Fin</label>
                                    <input type="date" class="form-control" name="end_date">
                                </div>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Modalidad</label>
                                <input class="form-control" name="modality">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Area organizadora</label>
                                <input class="form-control" name="area">
                            </div>
                            <button class="btn btn-primary tja-btn w-100" type="submit"><i class="fa-solid fa-floppy-disk"></i> Guardar</button>
                            <button class="btn btn-outline-light tja-btn-outline w-100 mt-2" type="button" id="courseReset"><i class="fa-solid fa-broom"></i> Limpiar</button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
            <div class="col-lg-8">
                <div class="tja-card tja-card-border">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4>Listado de cursos</h4>
                        <input class="form-control tja-search" id="courseSearch" placeholder="Buscar por nombre o edicion">
                    </div>
                    <div class="table-responsive">
                        <div class="tja-table-wrap">
                            <table class="table tja-table">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Edicion</th>
                                    <th>Fechas</th>
                                    <th>Modalidad</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="courseTable" data-can-manage="<?php echo !empty($can_manage) ? '1' : '0'; ?>"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
