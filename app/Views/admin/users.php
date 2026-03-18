<section class="tja-section">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="tja-card tja-card-border">
                    <h4>Crear usuario</h4>
                    <form id="userForm">
                        <input type="hidden" name="id">
                        <div class="mb-2">
                            <label class="form-label">Nombre</label>
                            <input class="form-control" name="name" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Correo</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Roles</label>
                            <label class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="rolesAll">
                                <span class="form-check-label">Seleccionar todos</span>
                            </label>
                            <div class="tja-checklist">
                                <label class="form-check">
                                    <input class="form-check-input" type="checkbox" name="roles[]" value="ADMIN">
                                    <span class="form-check-label">Administrador</span>
                                </label>
                                <label class="form-check">
                                    <input class="form-check-input" type="checkbox" name="roles[]" value="COURSES">
                                    <span class="form-check-label">Cursos</span>
                                </label>
                                <label class="form-check">
                                    <input class="form-check-input" type="checkbox" name="roles[]" value="PARTICIPANTS">
                                    <span class="form-check-label">Participantes</span>
                                </label>
                                <label class="form-check">
                                    <input class="form-check-input" type="checkbox" name="roles[]" value="CERTIFICATES">
                                    <span class="form-check-label">Constancias</span>
                                </label>
                                <label class="form-check">
                                    <input class="form-check-input" type="checkbox" name="roles[]" value="READONLY">
                                    <span class="form-check-label">Solo lectura</span>
                                </label>
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Estatus</label>
                            <select class="form-select" name="status" required>
                                <option value="ACTIVE">Activo</option>
                                <option value="DISABLED">Deshabilitado</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contrase&ntilde;a</label>
                            <input type="password" class="form-control" name="password">
                            <div class="form-text">Deja en blanco para conservar la contrase&ntilde;a actual.</div>
                        </div>
                        <button class="btn btn-primary tja-btn w-100" type="submit"><i class="fa-solid fa-user-plus"></i> Guardar</button>
                        <button class="btn btn-outline-light tja-btn-outline w-100 mt-2" type="button" id="userReset"><i class="fa-solid fa-broom"></i> Limpiar</button>
                    </form>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="tja-card tja-card-border">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4>Usuarios registrados</h4>
                        <input class="form-control tja-search" id="userSearch" placeholder="Buscar por nombre o correo">
                    </div>
                    <div id="userCards" class="tja-user-grid"></div>
                </div>
                <div class="tja-card tja-card-border mt-3">
                    <h5>Permisos por rol</h5>
                    <div class="table-responsive">
                        <div class="tja-table-wrap">
                            <table class="table tja-table">
                            <thead>
                                <tr>
                                    <th>Rol</th>
                                    <th>Permisos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>ADMIN</td>
                                    <td>Todo el sistema, usuarios y auditoria</td>
                                </tr>
                                <tr>
                                    <td>COURSES</td>
                                    <td>Gestion de cursos</td>
                                </tr>
                                <tr>
                                    <td>PARTICIPANTS</td>
                                    <td>Gestion de participantes</td>
                                </tr>
                                <tr>
                                    <td>CERTIFICATES</td>
                                    <td>Gestion de constancias</td>
                                </tr>
                                <tr>
                                    <td>READONLY</td>
                                    <td>Solo lectura de cursos, participantes y constancias</td>
                                </tr>
                            </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
