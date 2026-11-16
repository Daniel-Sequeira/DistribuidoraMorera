<!-- Modal Clientes-->
<div class="modal fade" id="modalFormCliente" tabindex="-1" aria-labelledby="modalFormClienteLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header headerRegister">
                <h5 class="modal-title" id="titleModal">Nuevo Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="tile">
                    <div class="tile-body">
                        <form id="formCliente" name="formCliente" novalidate class="form-horizontal">
                            <input type="hidden" id="id_cliente" name="id_cliente" value="">
                            <p class="text-primary"> Los campos con asterico (<span class="required">*</span>)son obligatorios.</p>

                            <div class="mb-3">
                                <label for="txtNombreRazonSocial" class="form-label">Razón Social<span class="required">*</span></label>
                                <input type="text" id="txtNombreRazonSocial" name="txtNombreRazonSocial" required="" class="form-control" placeholder="Nombre Razón Social" maxlength="200">
                                <div class="form-text"></div>
                            </div>

                            <div class="mb-3">
                                <label for="txtIdentificacion" class="form-label">Identificación<span class="required">*</span></label>
                                <input type="text" id="txtIdentificacion" name="txtIdentificacion" required="" class="form-control validNumber" placeholder="Formato 101110111" maxlength="50" onkeypress="return controlTag(event);">
                                <div class="form-text"></div>
                            </div>

                            <div class="mb-3">
                                <label for="txtTelefono" class="form-label">Teléfono<span class="required">*</span></label>
                                <input type="text" id="txtTelefono" name="txtTelefono" required="" class="form-control validNumber" placeholder="" maxlength="20" onkeypress="return controlTag(event);">
                                <div class="form-text"></div>
                                <div class="form-text"></div>
                            </div>

                            <div class="mb-3">
                                <label for="txtEmail" class="form-label">Correo<span class="required">*</span></label>
                                <input type="email" id="txtEmail" name="txtEmail" required="" class="form-control validEmail" placeholder="Correo" maxlength="150">
                                <div class="form-text"></div>
                            </div>

                            <div class="mb-3">
                                <label for="txtDireccion" class="form-label">Dirección<span class="required">*</span></label>
                                <input type="text" id="txtDireccion" name="txtDireccion" required="" class="form-control" maxlength="200">
                                <div class="form-text"></div>
                            </div>

                            <!--El campo estado es oculto ya que por defecto es true en base de datos al crear un CLiente nuevo -->
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
<div class="modal fade" id="modalViewCliente" tabindex="-1" aria-labelledby="modalFormClientesLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header headerUpdate">
                <h5 class="modal-title" id="titleModal">Datos del cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <table class="table table-bordered">
                    <tbody>
                       
                        <tr>
                            <td>Identificacion</td>
                            <td id="celIdentificacion"></td>
                        </tr>
                        <tr>
                            <td>Nombre Razón Social</td>
                            <td id="celNombre"></td>
                        </tr>
                        <tr>
                            <td>Teléfono</td>
                            <td id="celTelefono"></td>
                        </tr>
                        <tr>
                            <td>Email (Cliente)</td>
                            <td id="celEmail"></td>
                        </tr>
                         <tr>
                            <td>Dirección</td>
                            <td id="celDireccion"></td>
                        </tr>
                        <tr>
                            <td>Estado</td>
                            <td id="celEstado"></td>
                        </tr>
                        <tr>
                            <td>Fecha Registro</td>
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