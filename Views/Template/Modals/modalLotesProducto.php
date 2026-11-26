<!-- Modal Lotes Activos del Producto -->
<div class="modal fade" id="modalLotesProducto" tabindex="-1" role="dialog" aria-labelledby="titleModalLotes" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="titleModalLotes">
          <i class="fas fa-boxes me-2"></i> Lotes Activos del Producto
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-light border d-flex justify-content-between align-items-center mb-3">
          <div>
            <strong id="loteProdCodigo">-</strong> &mdash; <span id="loteProdNombre">Cargando...</span>
          </div>
          <span class="badge bg-primary fs-6">Stock Total: <span id="loteProdStock">0</span></span>
        </div>

        <div class="table-responsive">
          <table class="table table-striped table-bordered table-sm mb-0" id="tableDetalleLotes">
            <thead class="table-light">
              <tr>
                <th>Código de Lote</th>
                <th class="text-center">Disponible</th>
                <th class="text-center">Reservada</th>
                <th class="text-center">Fecha Vencimiento</th>
                <th class="text-center">Estado</th>
              </tr>
            </thead>
            <tbody id="tbodyDetalleLotes">
              <tr>
                <td colspan="5" class="text-center">Cargando lotes...</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>