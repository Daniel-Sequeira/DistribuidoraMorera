<!-- Modal -->
<div class="modal fade" id="modalFormRol" tabindex="-1" aria-labelledby="modalFormRolLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalFormRolLabel">Nuevo Rol</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- El formulario  -->
        <form id="formRol" name="formRol">
          <div class="mb-3">
            <label for="nombreRolInput" class="form-label">Nombre del Rol</label>
            <input type="text" id="nombreRolInput" name="nombre_rol" class="form-control" placeholder="Existen 1 Administrador, 2 Ventas, 3 Bodega" maxlength="20" required>
            <div class="form-text">El nombre debe ser único y tener máximo 20 caracteres.</div>
          </div>
          
          <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Guardar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>