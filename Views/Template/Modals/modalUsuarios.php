<!-- Modal Usuarios-->
<div class="modal fade" id="modalFormUsuario" tabindex="-1" aria-labelledby="modalFormUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header headerRegister">
                <h5 class="modal-title" id="titleModal">Nuevo Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="tile">
                    <div class="tile-body">
                        <form id="formUsuario" name="formUsuario" novalidate class="form-horizontal">
                            <input type="hidden" id="id_usuario" name="id_usuario" value="">

                            <div class="mb-3">
                                <label for="listRolid" class="form-label">Rol Usuario</label>
                                <select class="form-select form-select-lg" id="listRolid" name="listRolid" required>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="txtIdentificacion" class="form-label">Identificación</label>
                                <input type="text" id="txtIdentificacion" name="txtIdentificacion" required="" class="form-control validNumber" placeholder="Formato 101110111" maxlength="150" onkeypress="return controlTag(event);">
                                <div class="form-text"></div>
                            </div>

                            <div class="mb-3">
                                <label for="txtNombre" class="form-label">Nombre completo</label>
                                <input type="text" id="txtNombre" name="txtNombre" required="" class="form-control validText" placeholder="Nombre completo" maxlength="150">
                                <div class="form-text"></div>
                            </div>

                            <div class="mb-3">
                                <label for="txtEmail" class="form-label">Correo</label>
                                <input type="email" id="txtEmail" name="txtEmail" required="" class="form-control validEmail" placeholder="Correo" maxlength="150">
                                <div class="form-text"></div>
                            </div>

                            <div class="mb-3">
                                <label for="txtPassword" class="form-label">Password</label>
                                <input type="password" id="txtPassword" name="txtPassword" class="form-control" placeholder="Password">
                            </div>

                            <!--El campo estado es oculto ya que por defecto es true en base de datos al crear un Usuario nuevo -->
                            <div class="mb-3" id="divEstado" style="display: none;">
                                <label for="listStatus" class="form-label">Estado</label>
                                <select class="form-control selectpicker" id="listStatus" name="listStatus">
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <button id="btnActionForm" type="submit" class="btn btn-primary">
                                    <i class="fas fa-check-circle"></i> <span id="btnText">Guardar</span>
                                </button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <i class="fas fa-times-circle"></i> Cancelar
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal Ver un solo Usuario-->
<div class="modal fade" id="modalViewUsuario" tabindex="-1" aria-labelledby="modalFormUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header headerUpdate">
                <h5 class="modal-title" id="titleModal">Datos de usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td>Rol</td>
                            <td id="celRol">Admin</td>
                        </tr>
                        <tr>
                            <td>Identificacion</td>
                            <td id="celIdentificacion">503720273</td>
                        </tr>
                        <tr>
                            <td>Nombre</td>
                            <td id="celNombre">Daniel Seq</td>
                        </tr>
                        <tr>
                            <td>Email (Usuario)</td>
                            <td id="celEmail">daniel@mail.com</td>
                        </tr>
                        <tr>
                            <td>Estado</td>
                            <td id="celEstado">Activo</td>
                        </tr>
                        <tr>
                            <td>Fecha Registro</td>
                            <td id="celFechaRegistro">Timestamp</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>

        </div>
    </div>
</div>