<section class="tja-section">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="tja-card tja-card-border">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4>Auditoria de acciones</h4>
                        <div class="d-flex gap-2">
                            <input class="form-control tja-search" id="auditSearch" placeholder="Buscar por usuario, accion o entidad">
                            <a class="btn btn-outline-light tja-btn-outline" href="<?php echo base_url('admin/api/audit/export'); ?>"><i class="fa-solid fa-file-csv"></i> CSV</a>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Accion</label>
                            <select class="form-select" id="auditAction">
                                <option value="">Todas</option>
                                <option value="LOGIN">LOGIN</option>
                                <option value="LOGOUT">LOGOUT</option>
                                <option value="CREATE">CREATE</option>
                                <option value="UPDATE">UPDATE</option>
                                <option value="DELETE">DELETE</option>
                                <option value="ISSUE">ISSUE</option>
                                <option value="STATUS">STATUS</option>
                                <option value="RESET_REQUEST">RESET_REQUEST</option>
                                <option value="RESET_PASSWORD">RESET_PASSWORD</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Entidad</label>
                            <select class="form-select" id="auditEntity">
                                <option value="">Todas</option>
                                <option value="users">users</option>
                                <option value="courses">courses</option>
                                <option value="participants">participants</option>
                                <option value="certificates">certificates</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Desde</label>
                            <input type="date" class="form-control" id="auditFrom">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Hasta</label>
                            <input type="date" class="form-control" id="auditTo">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Orden</label>
                            <select class="form-select" id="auditSort">
                                <option value="created_at">Fecha</option>
                                <option value="action">Accion</option>
                                <option value="entity">Entidad</option>
                                <option value="user_name">Usuario</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Direccion</label>
                            <select class="form-select" id="auditDir">
                                <option value="desc">Descendente</option>
                                <option value="asc">Ascendente</option>
                            </select>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <div class="tja-table-wrap">
                            <table class="table tja-table">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Usuario</th>
                                    <th>Accion</th>
                                    <th>Entidad</th>
                                    <th>ID</th>
                                    <th>IP</th>
                                </tr>
                            </thead>
                            <tbody id="auditTable"></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="tja-note" id="auditMeta">Pagina 1 de 1</div>
                        <div class="btn-group">
                            <button class="btn btn-outline-light tja-btn-outline" id="auditPrev">Anterior</button>
                            <button class="btn btn-outline-light tja-btn-outline" id="auditNext">Siguiente</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
