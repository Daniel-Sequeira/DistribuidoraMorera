<!-- Modal Crear / Editar Proveedor -->
<div class="modal fade" id="modalFormProveedores" tabindex="-1" aria-labelledby="modalFormProveedoresLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header headerRegister">
                <h5 class="modal-title" id="titleModal">Nuevo Proveedor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="tile">
                    <div class="tile-body">
                        <form id="formProveedor" name="formProveedor" novalidate class="form-horizontal">
                            <input type="hidden" id="id_proveedor" name="id_proveedor" value="">
                            <p class="text-primary">Los campos con asterisco (<span class="required">*</span>) son obligatorios.</p>

                            <div class="row">
                                <div class="col-md-5 mb-3">
                                    <label for="txtIdentificacion" class="form-label">Identificación (Cédula / RUC) <span class="required">*</span></label>
                                    <input type="text" id="txtIdentificacion" name="txtIdentificacion" class="form-control" placeholder="Número de identificación" maxlength="50" required>
                                </div>

                                <div class="col-md-7 mb-3">
                                    <label for="txtNombre" class="form-label">Nombre o Razón Social <span class="required">*</span></label>
                                    <input type="text" id="txtNombre" name="txtNombre" class="form-control" placeholder="Nombre de la empresa o proveedor" maxlength="200" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="txtTelefono" class="form-label">Teléfono</label>
                                    <input type="text" id="txtTelefono" name="txtTelefono" class="form-control" placeholder="Ej. 8888-8888" maxlength="20">
                                </div>

                                <div class="col-md-5 mb-3">
                                    <label for="txtCorreo" class="form-label">Correo Electrónico</label>
                                    <input type="email" id="txtCorreo" name="txtCorreo" class="form-control" placeholder="correo@proveedor.com" maxlength="150">
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="listStatus" class="form-label">Estado <span class="required">*</span></label>
                                    <select class="form-select" id="listStatus" name="listStatus" required>
                                        <option value="1">Activo</option>
                                        <option value="0">Inactivo</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="txtDireccion" class="form-label">Dirección</label>
                                <textarea id="txtDireccion" name="txtDireccion" class="form-control" rows="3" placeholder="Dirección física o comercial del proveedor"></textarea>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-6 mb-2">
                                    <button id="btnActionForm" type="submit" class="btn btn-primary btn-lg w-100">
                                        <i class="fas fa-check-circle"></i> <span id="btnText">Guardar</span>
                                    </button>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <button type="button" class="btn btn-secondary btn-lg w-100" data-bs-dismiss="modal">
                                        <i class="fas fa-times-circle"></i> Cancelar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal Ver Detalle de Proveedor -->
<div class="modal fade" id="modalViewProveedor" tabindex="-1" aria-labelledby="modalViewProveedorLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header header-primary">
                <h5 class="modal-title" id="modalViewProveedorLabel">Datos del Proveedor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <table class="table table-bordered table-sm mb-0">
                    <tbody>
                        <tr>
                            <th style="width: 35%;">ID Proveedor:</th>
                            <td id="celIdProveedor"></td>
                        </tr>
                        <tr>
                            <th>Identificación:</th>
                            <td id="celIdentificacion"></td>
                        </tr>
                        <tr>
                            <th>Nombre / Razón Social:</th>
                            <td id="celNombre"></td>
                        </tr>
                        <tr>
                            <th>Teléfono:</th>
                            <td id="celTelefono"></td>
                        </tr>
                        <tr>
                            <th>Correo:</th>
                            <td id="celCorreo"></td>
                        </tr>
                        <tr>
                            <th>Dirección:</th>
                            <td id="celDireccion"></td>
                        </tr>
                        <tr>
                            <th>Estado:</th>
                            <td id="celEstado"></td>
                        </tr>
                        <tr>
                            <th>Fecha Registro:</th>
                            <td id="celFechaRegistro"></td>
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