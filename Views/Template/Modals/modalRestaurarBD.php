<!-- Modal Restaurar Base de Datos -->
<div class="modal fade" id="modalRestaurarBD" tabindex="-1" role="dialog" aria-labelledby="titleModalRestaurar" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="titleModalRestaurar">
          <i class="fa fa-database me-2"></i> Restaurar Base de Datos
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <form id="formRestaurarBD" enctype="multipart/form-data">
        <div class="modal-body">
          <div class="alert alert-warning">
            <strong><i class="fa fa-exclamation-triangle"></i> Advertencia:</strong>
            Esta acción reemplazará las tablas y registros actuales de <strong><?= DB_NAME ?></strong> con el contenido del respaldo seleccionado.
          </div>

          <div class="mb-3 form-group">
            <label for="archivo_sql" class="form-label font-weight-bold">Archivo de respaldo (.sql)</label>
            <input type="file" class="form-control" id="archivo_sql" name="archivo_sql" accept=".sql" required>
            <small class="form-text text-muted">Utiliza únicamente archivos generados desde la opción "Respaldo BD".</small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-danger" id="btnEjecutarRestauracion">
            <i class="fa fa-cloud-upload"></i> Iniciar Restauración
          </button>
        </div>
      </form>
    </div>
  </div>
</div>