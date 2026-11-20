let tableMovimientos;
let stockLoteSeleccionado = 0;
const monedaSimbolo = (typeof SMONEY !== 'undefined') ? SMONEY : '';

document.addEventListener('DOMContentLoaded', function () {
    // 1. Inicializar DataTable principal (11 columnas)
    tableMovimientos = $('#tableMovimientos').DataTable({
        "aProcessing": true,
        "aServerSide": false,
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "ajax": {
            "url": base_url + "Movimientos/getMovimientos",
            "dataSrc": ""
        },
        "columns": [
            { "data": "id_movimiento" },
            { "data": "fecha_movimiento" },
            { "data": "tipo_movimiento" },
            { "data": "producto" },
            { "data": "lote" },
            { "data": "cantidad" },
            { "data": "monto_unitario" },
            { "data": "cliente" },
            { "data": "estado_badge" },
            { "data": "usuario" },
            { "data": "opciones" }
        ],
        "responsive": true,
        "bDestroy": true,
        "iDisplayLength": 10,
        "order": [[0, "desc"]],
        "dom": 'Bfrtip',
        "buttons": [
            {
                "extend": "copyHtml5",
                "text": "<i class='fa fa-copy'></i> Copiar",
                "titleAttr": "Copiar",
                "className": "btn btn-secondary"
            },
            {
                "extend": "excelHtml5",
                "text": "<i class='fa fa-file-excel-o'></i> Excel",
                "titleAttr": "Exportar a Excel",
                "className": "btn btn-success"
            },
            {
                "extend": "csvHtml5",
                "text": "<i class='fa fa-file-text-o'></i> CSV",
                "titleAttr": "Exportar a CSV",
                "className": "btn btn-info"
            },
            {
                "extend": "pdfHtml5",
                "text": "<i class='fa fa-file-pdf-o'></i> PDF",
                "titleAttr": "Exportar a PDF",
                "className": "btn btn-danger"
            }
        ]
    });

    // 2. Filtros rápidos en la tabla (Tipo y Estado)
    let filtroTipo = document.querySelector('#filtroTipoMovimiento');
    if (filtroTipo) {
        filtroTipo.addEventListener('change', function (e) {
            tableMovimientos.column(2).search(e.target.value).draw();
        });
    }

    let filtroEstado = document.querySelector('#filtroEstadoMovimiento');
    if (filtroEstado) {
        filtroEstado.addEventListener('change', function (e) {
            tableMovimientos.column(8).search(e.target.value).draw();
        });
    }

    // 3. Cargar catálogos iniciales (Productos y Clientes)
    cargarSelectsIniciales();

    // 4. Cambio entre ENTRADA y SALIDA en el modal
    document.querySelector('#listTipoMovimiento').addEventListener('change', function () {
        actualizarCamposPorTipo(this.value);
    });

    // 5. Al seleccionar Cliente en modo SALIDA -> Cargar su lista congelada (Estado 1)
    document.querySelector('#listCliente').addEventListener('change', function () {
        let tipo = document.querySelector('#listTipoMovimiento').value;
        if (tipo === 'SALIDA' && this.value !== '') {
            cargarListaPreviaVentas(this.value);
        } else {
            limpiarTablaPreviaVentas();
        }
    });

    // 6. Al seleccionar Producto -> Cargar sus lotes desde producto_lotes
    document.querySelector('#listProducto').addEventListener('change', function () {
        cargarLotesDeProducto(this.value);
    });

    // 7. Al seleccionar Lote -> Mostrar stock libre y autocompletar costo/precio del lote
    document.querySelector('#listLote').addEventListener('change', function () {
        let opt = this.options[this.selectedIndex];
        stockLoteSeleccionado = parseInt(opt.getAttribute('data-libre')) || 0;

        if (this.value) {
            document.querySelector('#txtCostoUnitario').value = opt.getAttribute('data-costo') || '0.00';
            document.querySelector('#txtPrecioUnitario').value = opt.getAttribute('data-precio') || '0.00';
            document.querySelector('#helpStockDisponible').innerHTML = `Disponible libre: <strong>${stockLoteSeleccionado}</strong>`;
        } else {
            document.querySelector('#helpStockDisponible').innerHTML = '';
        }
    });

    // 8. Submit del formulario principal (ENTRADA directa o SALIDA a lista de Ventas)
    let formMovimiento = document.querySelector("#formMovimiento");
    formMovimiento.onsubmit = function (e) {
        e.preventDefault();

        let tipo = document.querySelector('#listTipoMovimiento').value;
        let idCliente = document.querySelector('#listCliente').value;
        let idProducto = document.querySelector('#listProducto').value;
        let idLote = document.querySelector('#listLote').value;
        let cantidad = parseInt(document.querySelector('#txtCantidad').value) || 0;

        if (idProducto === '' || idLote === '' || cantidad <= 0) {
            swal("Atención", "Seleccione producto, lote y una cantidad mayor a 0.", "error");
            return false;
        }

        if (tipo === 'SALIDA') {
            if (idCliente === '') {
                swal("Atención", "Debe seleccionar un cliente para registrar una salida.", "error");
                return false;
            }
            if (cantidad > stockLoteSeleccionado) {
                swal("Stock insuficiente", `Solo hay ${stockLoteSeleccionado} unidades libres en este lote (descontando reservas).`, "warning");
                return false;
            }
        }

        let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
        let ajaxUrl = base_url + 'Movimientos/setMovimiento';
        let formData = new FormData(formMovimiento);

        request.open("POST", ajaxUrl, true);
        request.send(formData);

        request.onreadystatechange = function () {
            if (request.readyState == 4 && request.status == 200) {
                let objData = JSON.parse(request.responseText);

                if (objData.status) {
                    tableMovimientos.ajax.reload(null, false);

                    if (objData.tipo === 'ENTRADA') {
                        // En ENTRADA se cierra el modal y se limpia todo
                        let modalElement = document.querySelector('#modalFormMovimientos');
                        let modalForm = bootstrap.Modal.getInstance(modalElement);
                        if (modalForm) modalForm.hide();

                        formMovimiento.reset();
                        swal("Entrada Registrada", objData.msg, "success");
                    } else {
                        // En SALIDA el modal sigue abierto para seguir agregando o procesar
                        document.querySelector('#listProducto').value = '';
                        document.querySelector('#listLote').innerHTML = '<option value="">Primero seleccione un producto...</option>';
                        document.querySelector('#listLote').disabled = true;
                        document.querySelector('#txtCantidad').value = '';
                        document.querySelector('#txtPrecioUnitario').value = '0.00';
                        document.querySelector('#helpStockDisponible').innerHTML = '';
                        stockLoteSeleccionado = 0;

                        cargarListaPreviaVentas(idCliente);
                        swal("Artículo Reservado", objData.msg, "success");
                    }
                } else {
                    swal("Error", objData.msg, "error");
                }
            }
        };
    };

    // 9. Procesar Pedido de Ventas -> Pasa de Estado 1 a Estado 2 (En Proceso para Bodega)
    document.querySelector('#btnProcesarVentas').addEventListener('click', function () {
        let idCliente = document.querySelector('#listCliente').value;
        if (!idCliente) return;

        swal({
            title: "Procesar Pedido a Bodega",
            text: "Los artículos pasarán a estado 'En Proceso' para embalaje y ya no podrán editarse desde Ventas. ¿Continuar?",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Sí, procesar pedido",
            cancelButtonText: "Cancelar",
            closeOnConfirm: false,
            closeOnCancel: true
        }, function (isConfirm) {
            if (isConfirm) {
                let formData = new FormData();
                formData.append('id_cliente', idCliente);

                fetch(base_url + 'Movimientos/procesarVentas', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status) {
                        let modalElement = document.querySelector('#modalFormMovimientos');
                        let modalForm = bootstrap.Modal.getInstance(modalElement);
                        if (modalForm) modalForm.hide();

                        document.querySelector('#formMovimiento').reset();
                        limpiarTablaPreviaVentas();
                        tableMovimientos.ajax.reload(null, false);
                        swal("Pedido Procesado", data.msg, "success");
                    } else {
                        swal("Error", data.msg, "error");
                    }
                });
            }
        });
    });

    // 10. Submit del Modal de Devolución Parcial o Total
    document.querySelector('#formDevolucion').onsubmit = function (e) {
        e.preventDefault();
        let formData = new FormData(this);

        fetch(base_url + 'Movimientos/setDevolucion', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.status) {
                let modalDev = bootstrap.Modal.getInstance(document.querySelector('#modalDevolucion'));
                if (modalDev) modalDev.hide();

                this.reset();
                tableMovimientos.ajax.reload(null, false);
                swal("Devolución Registrada", data.msg, "success");
            } else {
                swal("Error", data.msg, "error");
            }
        });
    };
}, false);


// FUNCIONES DE GESTIÓN DE LISTA PREVIA DE VENTAS (ESTADO 1 - CONGELADO)

function cargarListaPreviaVentas(idCliente) {
    fetch(base_url + 'Movimientos/getListaVentas/' + idCliente)
        .then(res => res.json())
        .then(lista => {
            let tbody = document.querySelector('#tbodyListaVentas');
            let btnProcesar = document.querySelector('#btnProcesarVentas');
            let totalGeneral = 0;

            if (!lista || lista.length === 0) {
                limpiarTablaPreviaVentas();
                return;
            }

            let html = '';
            lista.forEach(item => {
                let subtotal = parseFloat(item.subtotal) || 0;
                totalGeneral += subtotal;

                html += `
                    <tr>
                        <td>${item.codigo}</td>
                        <td>${item.producto}</td>
                        <td>${item.lote}</td>
                        <td>
                            <input type="number" 
                                   class="form-control form-control-sm input-cant-ventas" 
                                   data-id="${item.id_movimiento}" 
                                   data-prev="${item.cantidad}" 
                                   min="1" 
                                   max="${item.max_permitido}" 
                                   value="${item.cantidad}"
                                   title="Máximo disponible en lote: ${item.max_permitido}">
                        </td>
                        <td class="text-end">${monedaSimbolo}${parseFloat(item.precio_unitario).toFixed(2)}</td>
                        <td class="text-end fw-bold">${monedaSimbolo}${subtotal.toFixed(2)}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-sm btn-del-item-ventas" data-id="${item.id_movimiento}" title="Quitar artículo y liberar reserva">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });

            tbody.innerHTML = html;
            document.querySelector('#totalListaVentas').innerHTML = `${monedaSimbolo}${totalGeneral.toFixed(2)}`;
            btnProcesar.disabled = false;
        });
}

function limpiarTablaPreviaVentas() {
    document.querySelector('#tbodyListaVentas').innerHTML = `
        <tr>
            <td colspan="7" class="text-center text-muted">No hay artículos agregados para este cliente.</td>
        </tr>
    `;
    document.querySelector('#totalListaVentas').innerHTML = `${monedaSimbolo}0.00`;
    document.querySelector('#btnProcesarVentas').disabled = true;
}

// Editar cantidad al vuelo en la tabla previa de Ventas
document.addEventListener('change', function (e) {
    if (e.target && e.target.classList.contains('input-cant-ventas')) {
        let input = e.target;
        let idMov = input.getAttribute('data-id');
        let cantAnterior = parseInt(input.getAttribute('data-prev')) || 1;
        let nuevaCant = parseInt(input.value) || 0;
        let maxPermitido = parseInt(input.getAttribute('max')) || 1;
        let idCliente = document.querySelector('#listCliente').value;

        if (nuevaCant <= 0 || nuevaCant > maxPermitido) {
            swal("Cantidad inválida", `Debe ingresar una cantidad entre 1 y ${maxPermitido}.`, "warning");
            input.value = cantAnterior;
            return;
        }

        let formData = new FormData();
        formData.append('id_movimiento', idMov);
        formData.append('cantidad', nuevaCant);

        fetch(base_url + 'Movimientos/editItemVentas', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.status) {
                cargarListaPreviaVentas(idCliente);
                tableMovimientos.ajax.reload(null, false);
                // Si hay un producto seleccionado arriba, refrescar sus lotes para actualizar el stock libre visual
                let prodActual = document.querySelector('#listProducto').value;
                if (prodActual) cargarLotesDeProducto(prodActual);
            } else {
                swal("Error", data.msg, "error");
                input.value = cantAnterior;
            }
        });
    }
});

// Quitar artículo de la lista previa de Ventas (Libera cantidad congelada)
document.addEventListener('click', function (e) {
    let btnDelItem = e.target.closest('.btn-del-item-ventas');
    if (btnDelItem) {
        let idMov = btnDelItem.getAttribute('data-id');
        let idCliente = document.querySelector('#listCliente').value;

        let formData = new FormData();
        formData.append('id_movimiento', idMov);

        fetch(base_url + 'Movimientos/delItemVentas', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.status) {
                cargarListaPreviaVentas(idCliente);
                tableMovimientos.ajax.reload(null, false);
                let prodActual = document.querySelector('#listProducto').value;
                if (prodActual) cargarLotesDeProducto(prodActual);
            } else {
                swal("Error", data.msg, "error");
            }
        });
    }
});

// ACCIONES DE LA TABLA PRINCIPAL (VER, EDITAR LISTA, AVANZAR ESTADO, DEVOLUCIÓN)

document.addEventListener('click', function (e) {
    // A. Ver Detalle del Movimiento
    let btnView = e.target.closest('.btnViewMovimiento');
    if (btnView) {
        e.preventDefault();
        let idMovimiento = btnView.getAttribute("cl");
        let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
        let ajaxUrl = base_url + 'Movimientos/getMovimiento/' + idMovimiento;

        request.open("GET", ajaxUrl, true);
        request.send();

        request.onreadystatechange = function () {
            if (request.readyState == 4 && request.status == 200) {
                let objData = JSON.parse(request.responseText);
                if (objData.status) {
                    let d = objData.data;
                    document.querySelector("#celIdMovimiento").innerHTML = d.id_movimiento;
                    document.querySelector("#celFecha").innerHTML = d.fecha_movimiento;
                    document.querySelector("#celTipo").innerHTML = (d.tipo_movimiento === 'ENTRADA')
                        ? '<span class="badge bg-success">ENTRADA</span>'
                        : '<span class="badge bg-danger">SALIDA</span>';
                    document.querySelector("#celEstado").innerHTML = d.estado_badge;
                    document.querySelector("#celProducto").innerHTML = `${d.codigo_producto} - ${d.producto}`;
                    document.querySelector("#celLote").innerHTML = `${d.lote} (Ingreso: ${d.fecha_ingreso_lote || 'N/A'})`;
                    document.querySelector("#celCantidad").innerHTML = d.cantidad;
                    document.querySelector("#celCosto").innerHTML = d.costo_formato;
                    document.querySelector("#celPrecio").innerHTML = d.precio_formato;
                    document.querySelector("#celTotal").innerHTML = d.total_formato;
                    document.querySelector("#celCliente").innerHTML = d.cliente;
                    document.querySelector("#celUsuario").innerHTML = d.usuario;
                    document.querySelector("#celMotivo").innerHTML = d.motivo;

                    let modalElement = document.querySelector('#modalViewMovimiento');
                    let modalView = bootstrap.Modal.getOrCreateInstance(modalElement);
                    modalView.show();
                } else {
                    swal("Error", objData.msg, "error");
                }
            }
        };
    }

    // B. Abrir modal para editar lista pendiente de Ventas (Estado 1) de un cliente
    let btnEditVentas = e.target.closest('.btnEditarListaVentas');
    if (btnEditVentas) {
        e.preventDefault();
        let idCliente = btnEditVentas.getAttribute('cl');

        document.querySelector("#formMovimiento").reset();
        document.querySelector('#listTipoMovimiento').value = 'SALIDA';
        actualizarCamposPorTipo('SALIDA');

        document.querySelector('#listCliente').value = idCliente;
        cargarListaPreviaVentas(idCliente);

        let modalElement = document.querySelector('#modalFormMovimientos');
        let modalForm = bootstrap.Modal.getOrCreateInstance(modalElement);
        modalForm.show();
    }

    // C. Cambiar Estado: Bodega En Proceso (2) -> Enviado (3) -> Entregado (4)
    let btnEstado = e.target.closest('.btnCambiarEstado');
    if (btnEstado) {
        e.preventDefault();
        let idMov = btnEstado.getAttribute('cl');
        let nuevoEstado = btnEstado.getAttribute('estado');
        let textoAccion = (nuevoEstado == 3)
            ? "marcar este movimiento como ENVIADO (se descontará el stock físico del lote)"
            : "confirmar que el pedido fue ENTREGADO al cliente";

        swal({
            title: "Actualizar Estado",
            text: "¿Desea " + textoAccion + "?",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Sí, confirmar",
            cancelButtonText: "Cancelar",
            closeOnConfirm: false,
            closeOnCancel: true
        }, function (isConfirm) {
            if (isConfirm) {
                let formData = new FormData();
                formData.append('id_movimiento', idMov);
                formData.append('estado', nuevoEstado);

                fetch(base_url + 'Movimientos/cambiarEstado', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status) {
                        swal("Actualizado", data.msg, "success");
                        tableMovimientos.ajax.reload(null, false);
                    } else {
                        swal("Error", data.msg, "error");
                    }
                });
            }
        });
    }

    // D. Abrir Modal de Devolución Parcial o Total (desde Estado 4)
    let btnDev = e.target.closest('.btnDevolucion');
    if (btnDev) {
        e.preventDefault();
        let idMov = btnDev.getAttribute('cl');
        let cantOriginal = btnDev.getAttribute('cant');

        document.querySelector('#formDevolucion').reset();
        document.querySelector('#id_movimiento_dev').value = idMov;
        document.querySelector('#txtCantidadOriginal').value = cantOriginal;

        let inputCantDev = document.querySelector('#txtCantidadDevolver');
        inputCantDev.value = cantOriginal;
        inputCantDev.max = cantOriginal;

        let modalDev = bootstrap.Modal.getOrCreateInstance(document.querySelector('#modalDevolucion'));
        modalDev.show();
    }
});


// FUNCIONES AUXILIARES DE FORMULARIO Y SELECTS


function actualizarCamposPorTipo(tipo) {
    let divCliente = document.querySelector('#divCliente');
    let selectCliente = document.querySelector('#listCliente');
    let divCosto = document.querySelector('#divCostoUnitario');
    let divPrecio = document.querySelector('#divPrecioUnitario');
    let seccionVentas = document.querySelector('#seccionListaVentas');
    let btnText = document.querySelector('#btnText');

    if (tipo === 'SALIDA') {
        divCliente.classList.remove('d-none');
        selectCliente.setAttribute('required', 'required');

        divPrecio.classList.remove('d-none');
        divCosto.classList.add('d-none');

        seccionVentas.classList.remove('d-none');
        btnText.innerHTML = "Agregar a Lista (Reservar)";

        if (selectCliente.value !== '') {
            cargarListaPreviaVentas(selectCliente.value);
        } else {
            limpiarTablaPreviaVentas();
        }
    } else {
        divCliente.classList.add('d-none');
        selectCliente.removeAttribute('required');
        selectCliente.value = '';

        divCosto.classList.remove('d-none');
        divPrecio.classList.add('d-none');

        seccionVentas.classList.add('d-none');
        btnText.innerHTML = "Registrar Entrada";
    }
}

function cargarSelectsIniciales() {
    fetch(base_url + 'Movimientos/getSelectsIniciales')
        .then(res => res.json())
        .then(data => {
            let optProductos = '<option value="">Seleccione un producto...</option>';
            (data.productos || []).forEach(p => {
                optProductos += `<option value="${p.id_producto}">${p.codigo} - ${p.nombre}</option>`;
            });
            document.querySelector('#listProducto').innerHTML = optProductos;

            let optClientes = '<option value="">Seleccione un cliente...</option>';
            (data.clientes || []).forEach(c => {
                optClientes += `<option value="${c.id_cliente}">${c.identificacion} - ${c.nombre_razon_social}</option>`;
            });
            document.querySelector('#listCliente').innerHTML = optClientes;
        });
}

function cargarLotesDeProducto(idProducto) {
    let selectLote = document.querySelector('#listLote');
    stockLoteSeleccionado = 0;
    document.querySelector('#helpStockDisponible').innerHTML = '';

    if (!idProducto) {
        selectLote.innerHTML = '<option value="">Primero seleccione un producto...</option>';
        selectLote.disabled = true;
        return;
    }

    fetch(base_url + 'Movimientos/getLotesProducto/' + idProducto)
        .then(res => res.json())
        .then(lotes => {
            let options = '<option value="">Seleccione un lote...</option>';
            lotes.forEach(l => {
                options += `<option value="${l.id_lote}" 
                                data-libre="${l.stock_libre}" 
                                data-costo="${l.costo_unitario}" 
                                data-precio="${l.precio_venta_sugerido}">
                                ${l.codigo_lote} (Disp: ${l.stock_libre} | Ingreso: ${l.fecha_ingreso})
                            </option>`;
            });
            selectLote.innerHTML = options;
            selectLote.disabled = false;
        });
}

function openModal() {
    document.querySelector("#formMovimiento").reset();
    document.querySelector('#listTipoMovimiento').value = 'ENTRADA';
    actualizarCamposPorTipo('ENTRADA');
    limpiarTablaPreviaVentas();

    let selectLote = document.querySelector('#listLote');
    selectLote.innerHTML = '<option value="">Primero seleccione un producto...</option>';
    selectLote.disabled = true;
    document.querySelector('#helpStockDisponible').innerHTML = '';
    stockLoteSeleccionado = 0;

    let modalElement = document.querySelector('#modalFormMovimientos');
    let modalForm = bootstrap.Modal.getOrCreateInstance(modalElement);
    modalForm.show();
}