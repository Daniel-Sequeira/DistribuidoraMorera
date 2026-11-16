<!-- Modal Estanterias-->
<div class="modal fade" id="modalFormEstanterias" tabindex="-1" aria-labelledby="modalFormEstanteriasLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header headerRegister">
                <h5 class="modal-title" id="titleModal">Nueva Estantería</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="tile">
                    <div class="tile-body">
                        <form id="formEstanteria" name="formEstanteria" novalidate class="form-horizontal">
                            <input type="hidden" id="id_estanteria" name="id_estanteria" value="">
                            <input type="hidden" id="foto_actual" name="foto_actual" value="">
                             <input type="hidden" id="foto_remove" name="foto_remove" value="0">
                            <p class="text-primary"> Los campos con asterico (<span class="required">*</span>)son obligatorios.</p>
                            <div class="row">
                                <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="txtCodigo_estanteria" class="form-label">Código Estantería<span class="required">*</span></label>
                                            <input type="text" id="txtCodigo_estanteria" name="txtCodigo_estanteria" class="form-control" placeholder="" maxlength="10">
                                            <div class="form-text">El cod es alfabético ascendente.</div>
                                        </div>
                                         <div class="form-group">
                                            <label for="txtDescripcion" class="form-label">Descripción<span class="required">*</span></label>
                                            <input type="text" id="txtDescripcion" name="txtDescripcion" class="form-control" placeholder="" maxlength="100">
                                            <div class="form-text">Descripción.</div>
                                        </div>
                                        <div class="form-group">
                                            <label for="listStatus" class="form-label">Estado</label>
                                            <select class="form-control" id="listStatus" name="listStatus">
                                            <option value="1">Activo</option>
                                            <option value="0">Inactivo</option>
                                            </select>
                                        </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="photo">
                                                <label for="foto">Foto (570x380)</label>
                                                <div class="prevPhoto">
                                                <span class="delPhoto notBlock">X</span>
                                                <label for="foto"></label>
                                                <div>
                                                    <img id="img" src="<?= media(); ?>images/uploads/portada_estanteria.png">
                                                </div>
                                                </div>
                                                <div class="upimg">
                                                <input type="file" name="foto" id="foto">
                                                </div>
                                                <div id="form_alert"></div>
                                            </div>

                                            
                                        </div>

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

<!-- Modal Ver una sola estanteria-->
<div class="modal fade" id="modalViewEstanteria" tabindex="-1" aria-labelledby="modalFormEstanteriaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header headerUpdate">
                <h5 class="modal-title" id="titleModal">Estantería</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <table class="table table-bordered">
                    <tbody>
                       
                        <tr>
                            <td>Id Estantería</td>
                            <td id="celIdEstanteria"></td>
                        </tr>
                        <tr>
                            <td>Código Estantería</td>
                            <td id="celCodigo_estanteria"></td>
                        </tr>
                        <tr>
                            <td>Descripción</td>
                            <td id="celDescripcion"></td>
                        </tr>
                        <tr>
                            <td>Estado</td>
                            <td id="celEstado"></td>
                        </tr>
                          <tr>
                            <td>Imagen</td>
                            <td> 
                                <img id="imgEstanteria" src="" alt="Portada de estantería" width="150">
                            </td>
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