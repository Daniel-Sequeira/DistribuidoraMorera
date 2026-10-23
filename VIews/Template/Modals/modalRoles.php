<!-- Modal -->
<div class="modal fade" id="modalFormRol" tabindex="-1" aria-labelledby="modalFormRolLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header headerRegister">
        <h5 class="modal-title" id="titleModal">Nuevo Rol</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <div class="tile">
          <div class="tile-body">

            <form id="formRol" name="formRol">
              <input type="hidden" id="id_rol" name="id_rol" value="">

              <div class="mb-3">
                <label for="nombreRolInput" class="form-label">Nombre del Rol</label>
                <input type="text" id="nombreRolInput" name="nombre_rol" class="form-control" placeholder="Existen 1 Administrador, 2 Ventas, 3 Bodega" maxlength="20">
                <div class="form-text">El nombre debe ser único y tener máximo 20 caracteres.</div>
              </div>
              <!--El campo estado es oculto ya que por defecto es true en base de datos al crear un rol nuevo  -->
              <div class="mb-3" id="divEstado" style="display: none;">
                <label for="listStatus" class="form-label">Estado</label>
                <select class="form-control" id="listStatus" name="listStatus">
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

