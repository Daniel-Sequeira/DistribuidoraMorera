<!--MODAL 1: REGISTRAR ENTRADA / GESTIONAR LISTA DE SALIDA (VENTAS - ESTADO 1) -->
<div class="modal fade" id="modalFormMovimientos" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header headerRegister">
                <h5 class="modal-title" id="titleModal">Nuevo Movimiento de Inventario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formMovimiento" name="formMovimiento" class="form-horizontal">
                    <p class="text-primary mb-3">
                        Los campos con asterisco (<span class="required">*</span>) son obligatorios. 
                        En movimientos de <strong>SALIDA</strong>, los artículos se reservan en estado <em>Recibido por ventas</em> hasta que pulse <strong>Procesar Pedido</strong>.
                    </p>

                    <div class="row">
                        <!-- Tipo de Movimiento -->
                        <div class="col-md-4 mb-3">
                            <label for="listTipoMovimiento" class="form-label">Tipo de Movimiento <span class="required">*</span></label>
                            <select class="form-select" id="listTipoMovimiento" name="listTipoMovimiento" required>
                                <option value="ENTRADA">ENTRADA (Ingreso a inventario)</option>
                                <option value="SALIDA">SALIDA (Despacho / Venta a cliente)</option>
                            </select>
                        </div>

                        <!-- Cliente (Visible y obligatorio solo en SALIDA) -->
                        <div class="col-md-8 mb-3 d-none" id="divCliente">
                            <label for="listCliente" class="form-label">Cliente <span class="required">*</span></label>
                            <select class="form-select" id="listCliente" name="listCliente">
                                <option value="">Seleccione un cliente...</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Producto -->
                        <div class="col-md-5 mb-3">
                            <label for="listProducto" class="form-label">Producto <span class="required">*</span></label>
                            <select class="form-select" id="listProducto" name="listProducto" required>
                                <option value="">Seleccione un producto...</option>
                            </select>
                        </div>

                        <!-- Lote (Cargado dinámicamente desde producto_lotes) -->
                        <div class="col-md-5 mb-3">
                            <label for="listLote" class="form-label">Lote Disponible <span class="required">*</span></label>
                            <select class="form-select" id="listLote" name="listLote" required disabled>
                                <option value="">Primero seleccione un producto...</option>
                            </select>
                        </div>

                        <!-- Cantidad -->
                        <div class="col-md-2 mb-3">
                            <label for="txtCantidad" class="form-label">Cantidad <span class="required">*</span></label>
                            <input type="number" class="form-control" id="txtCantidad" name="txtCantidad" min="1" step="1" placeholder="0" required>
                            <small id="helpStockDisponible" class="form-text text-primary fw-bold"></small>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Costo Unitario (Visible en ENTRADA) -->
                        <div class="col-md-4 mb-3" id="divCostoUnitario">
                            <label for="txtCostoUnitario" class="form-label">Costo Unitario <span class="required">*</span></label>
                            <input type="number" class="form-control" id="txtCostoUnitario" name="txtCostoUnitario" min="0" step="0.01" value="0.00">
                        </div>

                        <!-- Precio Unitario (Visible en SALIDA) -->
                        <div class="col-md-4 mb-3 d-none" id="divPrecioUnitario">
                            <label for="txtPrecioUnitario" class="form-label">Precio Unitario <span class="required">*</span></label>
                            <input type="number" class="form-control" id="txtPrecioUnitario" name="txtPrecioUnitario" min="0" step="0.01" value="0.00">
                        </div>

                        <!-- Motivo / Referencia -->
                        <div class="col-md-8 mb-3">
                            <label for="txtMotivo" class="form-label">Motivo / Referencia</label>
                            <input type="text" class="form-control" id="txtMotivo" name="txtMotivo" maxlength="100" placeholder="Ej. Compra factura #102, Pedido telefónico, Ajuste...">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mb-2">
                        <button id="btnActionForm" class="btn btn-primary" type="submit">
                            <i class="fa fa-fw fa-lg fa-check-circle"></i><span id="btnText">Registrar Entrada</span>
                        </button>
                    </div>
                </form>

                <!-- LISTA DE ARTÍCULOS CONGELADOS (ESTADO 1)modo SALIDA cuando se selecciona un cliente -->
                <div id="seccionListaVentas" class="d-none mt-4 pt-3 border-top">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-bold text-uppercase mb-0">
                            <i class="fa fa-shopping-cart text-warning"></i> Artículos Reservados (Recibido por Ventas)
                        </h6>
                        <span class="badge bg-warning text-dark">Cantidades congeladas contra sobreventa</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-hover align-middle" id="tableListaVentas">
                            <thead class="table-light">
                                <tr>
                                    <th>Código</th>
                                    <th>Producto</th>
                                    <th>Lote</th>
                                    <th style="width: 150px;">Cantidad</th>
                                    <th class="text-end">Precio Unit.</th>
                                    <th class="text-end">Subtotal</th>
                                    <th class="text-center" style="width: 90px;">Quitar</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyListaVentas">
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No hay artículos agregados para este cliente.</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="table-secondary fw-bold">
                                    <td colspan="5" class="text-end">TOTAL ESTIMADO PEDIDO:</td>
                                    <td class="text-end" id="totalListaVentas">0.00</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <small class="text-muted">
                            <i class="fa fa-info-circle"></i> Al pulsar <strong>Procesar Pedido</strong>, pasará a <em>En Proceso</em> para despacho en bodega.
                        </small>
                        <button type="button" id="btnProcesarVentas" class="btn btn-success" disabled>
                            <i class="fa fa-paper-plane"></i> Procesar Pedido a Bodega
                        </button>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">
                    <i class="fa fa-fw fa-lg fa-times-circle"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!--  MODAL 2: VER DETALLE DEL MOVIMIENTO -->
<div class="modal fade" id="modalViewMovimiento" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header header-primary">
                <h5 class="modal-title">Detalle del Movimiento de Inventario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <td style="width: 35%;"><strong>ID Movimiento:</strong></td>
                            <td id="celIdMovimiento"></td>
                        </tr>
                        <tr>
                            <td><strong>Fecha y Hora:</strong></td>
                            <td id="celFecha"></td>
                        </tr>
                        <tr>
                            <td><strong>Tipo de Movimiento:</strong></td>
                            <td id="celTipo"></td>
                        </tr>
                        <tr>
                            <td><strong>Estado Actual:</strong></td>
                            <td id="celEstado"></td>
                        </tr>
                        <tr>
                            <td><strong>Producto:</strong></td>
                            <td id="celProducto"></td>
                        </tr>
                        <tr>
                            <td><strong>Lote / Fecha Ingreso:</strong></td>
                            <td id="celLote"></td>
                        </tr>
                        <tr>
                            <td><strong>Cantidad:</strong></td>
                            <td id="celCantidad" class="fw-bold"></td>
                        </tr>
                        <tr>
                            <td><strong>Costo Unitario:</strong></td>
                            <td id="celCosto"></td>
                        </tr>
                        <tr>
                            <td><strong>Precio Unitario:</strong></td>
                            <td id="celPrecio"></td>
                        </tr>
                        <tr>
                            <td><strong>Total Operación:</strong></td>
                            <td id="celTotal" class="fw-bold text-primary"></td>
                        </tr>
                        <tr>
                            <td><strong>Cliente:</strong></td>
                            <td id="celCliente"></td>
                        </tr>
                        <tr>
                            <td><strong>Registrado por:</strong></td>
                            <td id="celUsuario"></td>
                        </tr>
                        <tr>
                            <td><strong>Motivo / Historial:</strong></td>
                            <td id="celMotivo"></td>
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

<!--  MODAL 3: REGISTRAR DEVOLUCIÓN PARCIAL O TOTAL (ESTADOS 5 Y 6) -->
<div class="modal fade" id="modalDevolucion" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fa fa-undo"></i> Registrar Devolución de Mercadería</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formDevolucion" name="formDevolucion">
                    <input type="hidden" id="id_movimiento_dev" name="id_movimiento_dev">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Cantidad Entregada Originalmente:</label>
                        <input type="text" class="form-control bg-light" id="txtCantidadOriginal" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="txtCantidadDevolver" class="form-label fw-bold">Cantidad a Devolver <span class="required">*</span></label>
                        <input type="number" class="form-control" id="txtCantidadDevolver" name="txtCantidadDevolver" min="1" step="1" required>
                        <small class="form-text text-muted">
                            • Si devuelve una parte, pasará a <strong>Devolución Parcial</strong>.<br>
                            • Si devuelve la totalidad, pasará a <strong>Devolución Total</strong>.
                        </small>
                    </div>

                    <div class="mb-3">
                        <label for="txtMotivoDevolucion" class="form-label fw-bold">Motivo de la Devolución <span class="required">*</span></label>
                        <input type="text" class="form-control" id="txtMotivoDevolucion" name="txtMotivoDevolucion" maxlength="70" required placeholder="Ej. Producto defectuoso, error en despacho...">
                    </div>

                    <div class="modal-footer px-0 pb-0">
                        <button type="submit" class="btn btn-danger">
                            <i class="fa fa-check"></i> Confirmar Devolución
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>