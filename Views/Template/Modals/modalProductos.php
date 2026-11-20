<!-- Modal Crear / Editar Producto -->
<div class="modal fade" id="modalFormProductos" tabindex="-1" aria-labelledby="modalFormProductosLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="modal-header headerRegister">
                <h5 class="modal-title" id="titleModal">Nuevo Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="tile">
                    <div class="tile-body">
                        <form id="formProducto" name="formProducto" novalidate class="form-horizontal">
                            <input type="hidden" id="id_producto" name="id_producto" value="">
                            <p class="text-primary">Los campos con asterisco (<span class="required">*</span>) son obligatorios.</p>

                            <div class="row">
                                <!-- Columna Izquierda: Datos Maestros del Catálogo (productos) -->
                                <div class="col-md-7">
                                    <div class="mb-3">
                                        <label for="txtNombre" class="form-label">Nombre Producto <span class="required">*</span></label>
                                        <input type="text" id="txtNombre" name="txtNombre" class="form-control" placeholder="Nombre del producto" maxlength="100" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="txtDescripcion" class="form-label">Descripción Producto</label>
                                        <textarea id="txtDescripcion" name="txtDescripcion" class="form-control" placeholder="Especificaciones o detalles del producto" rows="3"></textarea>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="listProveedor" class="form-label">Proveedor <span class="required">*</span></label>
                                            <select class="form-select" id="listProveedor" name="listProveedor" required>
                                                <option value="">Seleccione un proveedor...</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="listEstanteria" class="form-label">Estantería <span class="required">*</span></label>
                                            <select class="form-select" id="listEstanteria" name="listEstanteria" required>
                                                <option value="">Seleccione una estantería...</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="txtCodigo" class="form-label">Código de Producto <span class="required">*</span></label>
                                            <input type="text" id="txtCodigo" name="txtCodigo" class="form-control" placeholder="Código único o de barras" maxlength="30" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="listStatus" class="form-label">Estado <span class="required">*</span></label>
                                            <select class="form-select" id="listStatus" name="listStatus" required>
                                                <option value="1">Activo</option>
                                                <option value="0">Inactivo</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Columna Derecha: Precios y Lote Inicial FIFO (producto_lotes / movimientos_inventario) -->
                                <div class="col-md-5">
                                    <div class="p-3 border rounded bg-light">
                                        <h6 class="fw-bold text-dark mb-3">
                                            <i class="fas fa-boxes-stacked"></i> Precios e Inventario Inicial (Entrada)
                                        </h6>

                                        <div class="mb-3">
                                            <label for="txtPrecio" class="form-label">Precio de Venta / Salida <span class="required">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text">₡</span>
                                                <input class="form-control" id="txtPrecio" name="txtPrecio" type="number" step="0.01" min="0.01" placeholder="0.00" required>
                                            </div>
                                            <div class="form-text">Al editar, actualizar este precio solo aplica a ventas futuras.</div>
                                        </div>

                                        <!-- Bloque que se bloquea en modo Edición para no romper el flujo FIFO -->
                                        <div id="bloqueLoteInicial">
                                            <hr>
                                            <div class="mb-3">
                                                <label for="txtLote" class="form-label">Código de Lote Inicial <span class="required">*</span></label>
                                                <input type="text" id="txtLote" name="txtLote" class="form-control" placeholder="Ej. LOTE-001" maxlength="30" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                    <label for="txtFechaVencimiento" class="form-label">Fecha Vencimiento</label>
                                                    <input type="date" id="txtFechaVencimiento" name="txtFechaVencimiento" class="form-control">
                                                    <div class="form-text">Opcional si no perece.</div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="txtCosto" class="form-label">Costo Unitario/Entrada <span class="required">*</span></label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">₡</span>
                                                        <input class="form-control" id="txtCosto" name="txtCosto" type="number" step="0.01" min="0" placeholder="0.00" required>
                                                    </div>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label for="txtStock" class="form-label">Cantidad Inicial <span class="required">*</span></label>
                                                    <input class="form-control" id="txtStock" name="txtStock" type="number" step="1" min="1" placeholder="0" required>
                                                </div>
                                            </div>

                                            <div id="notaEdicionFifo" class="alert alert-info py-2 px-3 mb-0 small d-none">
                                                <i class="fas fa-info-circle"></i> El stock y los lotes se editan en  <strong>Movimientos (Entradas/Salidas)</strong> para seguir orden FIFO.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Botones de Acción -->
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

<!-- Modal Ver Detalle de Producto y Lotes FIFO Activos -->
<div class="modal fade" id="modalViewProducto" tabindex="-1" aria-labelledby="modalViewProductoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header header-primary">
                <h5 class="modal-title" id="modalViewProductoLabel">Detalles del Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <table class="table table-bordered table-sm mb-4">
                    <tbody>
                        <tr>
                            <th style="width: 30%;">ID Producto:</th>
                            <td id="celIdProducto"></td>
                        </tr>
                        <tr>
                            <th>Código:</th>
                            <td id="celCodigo"></td>
                        </tr>
                        <tr>
                            <th>Nombre:</th>
                            <td id="celNombre"></td>
                        </tr>
                        <tr>
                            <th>Descripción:</th>
                            <td id="celDescripcion"></td>
                        </tr>
                        <tr>
                            <th>Proveedor:</th>
                            <td id="celProveedor"></td>
                        </tr>
                        <tr>
                            <th>Estantería:</th>
                            <td id="celEstanteria"></td>
                        </tr>
                        <tr>
                            <th>Precio de Venta Vigente:</th>
                            <td id="celPrecio"></td>
                        </tr>
                        <tr>
                            <th>Stock Total Disponible:</th>
                            <td id="celStock"></td>
                        </tr>
                        <tr>
                            <th>Estado:</th>
                            <td id="celEstado"></td>
                        </tr>
                        <tr>
                            <th>Fecha de Registro:</th>
                            <td id="celFechaRegistro"></td>
                        </tr>
                    </tbody>
                </table>

                <h6 class="fw-bold mb-2"><i class="fas fa-layer-group"></i> Lotes Activos (Orden de salida FIFO)</h6>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-sm text-center mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Código Lote</th>
                                <th>Fecha Ingreso</th>
                                <th>Vencimiento</th>
                                <th>Costo Unit.</th>
                                <th>Cant. Inicial</th>
                                <th>Disponible</th>
                            </tr>
                        </thead>
                        <tbody id="tableLotesActivos">
                            <!-- Se llena dinámicamente vía JS con los lotes ordenados por fecha_ingreso ASC -->
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>

        </div>
    </div>
</div>