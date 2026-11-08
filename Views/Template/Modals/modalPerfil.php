<!-- Modal Perfil -->
<div class="modal fade" id="modalFormPerfil" tabindex="-1" aria-labelledby="modalFormUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header headerUpdate">
                <h5 class="modal-title" id="titleModal">Actualizar Datos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="tile">
                    <div class="tile-body">
                        <form id="formPerfil" name="formPerfil" novalidate class="form-horizontal">
                            
                            <p class="text-primary">Los campos con asterisco (<span class="required">*</span>) son obligatorios.</p>
                            
                            <div class="mb-3">
                                <label for="txtIdentificacion" class="form-label">Identificación<span class="required">*</span></label>
                                <input type="text" id="txtIdentificacion" name="txtIdentificacion" class="form-control validNumber" value="<?= htmlspecialchars($_SESSION['userData']['identificacion'] ?? '') ?>" required="">
                                <div class="form-text"></div>
                            </div>

                            <div class="mb-3">
                                <label for="txtNombre" class="form-label">Nombre completo<span class="required">*</span></label>
                                <input type="text" id="txtNombre" name="txtNombre" class="form-control validText" value="<?= htmlspecialchars($_SESSION['userData']['nombre_completo'] ?? '') ?>" required="">
                                <div class="form-text"></div>
                            </div>

                            <div class="mb-3">
                                <label for="txtEmail" class="form-label">Correo</label>
                                <input type="email" id="txtEmail" name="txtEmail" class="form-control validEmail" value="<?= htmlspecialchars($_SESSION['userData']['correo'] ?? '') ?>" required="" readonly disabled>
                                <div class="form-text"></div>
                            </div>

                            <div class="mb-3">
                                <label for="txtPassword" class="form-label">Password</label>
                                <input type="password" id="txtPassword" name="txtPassword" class="form-control">
                            </div>
                            
                            <div class="mb-3">
                                <label for="txtPasswordConfirm" class="form-label">Confirmar Password</label>
                                <input type="password" id="txtPasswordConfirm" name="txtPasswordConfirm" class="form-control">
                            </div>
                                
                            <div class="d-flex justify-content-end gap-2">
                                <button id="btnActionForm" type="submit" class="btn btn-info">
                                    <i class="fas fa-check-circle"></i> <span id="btnText">Actualizar</span>
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

