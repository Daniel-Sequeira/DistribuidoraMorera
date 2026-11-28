
let tableMovimientos;
let stockLoteSeleccionado = 0;
let secuenciaCargaLotes = 0;

const monedaSimbolo = (typeof SMONEY !== 'undefined') ? SMONEY : '';

document.addEventListener('DOMContentLoaded', function () {

    // DataTable principal
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

    // Filtros rápidos en la tabla (Tipo y Estado)
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

    // Cargar catálogos iniciales (Productos y Clientes)
    cargarSelectsIniciales();

    // Cambio entre ENTRADA y SALIDA en el modal
    document.querySelector('#listTipoMovimiento').addEventListener('change', function () {

        actualizarCamposPorTipo(this.value);

        cargarLotesDeProducto(
            document.querySelector('#listProducto').value
        );
    });

    // Seleccionar cliente en movimiento SALIDA
    // Carga su lista congelada (Estado 1)
    document.querySelector('#listCliente').addEventListener('change', function () {

        let tipo = document.querySelector('#listTipoMovimiento').value;

        if (tipo === 'SALIDA' && this.value !== '') {
            cargarListaPreviaVentas(this.value);
        } else {
            limpiarTablaPreviaVentas();
        }
    });

    // Al seleccionar producto, cargar sus lotes
    document.querySelector('#listProducto').addEventListener('change', function () {
        cargarLotesDeProducto(this.value);
    });

    // Seleccionar lote existente o crear uno nuevo
    document.querySelector('#listLote').addEventListener('change', function () {

        actualizarCamposNuevoLote();

        // Si se selecciona crear un nuevo lote
        if (this.value === 'NUEVO') {

            stockLoteSeleccionado = 0;

            document.querySelector('#txtCostoUnitario').value = '0.00';

            document.querySelector('#helpStockDisponible').textContent =
                'Se registrará un nuevo lote de inventario.';

            return;
        }

        // Selección de lote existente
        let opt = this.options[this.selectedIndex];

        stockLoteSeleccionado = parseInt(
            opt?.getAttribute('data-libre')
        ) || 0;

        if (this.value && opt) {

            document.querySelector('#txtCostoUnitario').value =
                opt.getAttribute('data-costo') || '0.00';

            document.querySelector('#txtPrecioUnitario').value =
                opt.getAttribute('data-precio') || '0.00';

            document.querySelector('#helpStockDisponible').innerHTML =
                `Disponible libre: <strong>${stockLoteSeleccionado}</strong>`;

        } else {

            document.querySelector('#helpStockDisponible').innerHTML = '';
        }
    });

    
    // Submit form principak
    // lista ventas
   

    let formMovimiento = document.querySelector("#formMovimiento");

    formMovimiento.onsubmit = function (e) {

        e.preventDefault();

        let tipo = document.querySelector('#listTipoMovimiento').value;
        let idCliente = document.querySelector('#listCliente').value;
        let idProducto = document.querySelector('#listProducto').value;
        let idLote = document.querySelector('#listLote').value;

        let cantidad = parseInt(
            document.querySelector('#txtCantidad').value
        ) || 0;

        if (idProducto === '' || idLote === '' || cantidad <= 0) {

            swal(
                "Atención",
                "Seleccione producto, lote y una cantidad mayor a 0.",
                "error"
            );

            return false;
        }

        //Validar creación de un nuevo lote
        if (idLote === 'NUEVO') {

            if (tipo !== 'ENTRADA') {

                swal(
                    "Atención",
                    "Solo se pueden crear lotes en entradas.",
                    "error"
                );

                return false;
            }

            if (document.querySelector('#txtCodigoLote').value.trim() === '') {

                swal(
                    "Atención",
                    "Debe ingresar el código del nuevo lote.",
                    "error"
                );

                return false;
            }
        }

        // Validaciones para SALIDA
        if (tipo === 'SALIDA') {

            if (idCliente === '') {

                swal(
                    "Atención",
                    "Debe seleccionar un cliente para registrar una salida.",
                    "error"
                );

                return false;
            }

            if (cantidad > stockLoteSeleccionado) {

                swal(
                    "Stock insuficiente",
                    `Solo hay ${stockLoteSeleccionado} unidades libres en este lote (descontando reservas).`,
                    "warning"
                );

                return false;
            }
        }

        let request = (window.XMLHttpRequest)
            ? new XMLHttpRequest()
            : new ActiveXObject('Microsoft.XMLHTTP');

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

                        // ENTRADA: cerrar modal y limpiar formulario
                        let modalElement = document.querySelector(
                            '#modalFormMovimientos'
                        );

                        let modalForm = bootstrap.Modal.getInstance(
                            modalElement
                        );

                        if (modalForm) modalForm.hide();

                        formMovimiento.reset();

                        // Limpiar también campos del nuevo lote
                        cargarLotesDeProducto('');

                        swal(
                            "Entrada Registrada",
                            objData.msg,
                            "success"
                        );

                    } else {

                        // Salida: mantener abierto el modal
                        document.querySelector('#listProducto').value = '';

                        document.querySelector('#listLote').innerHTML =
                            '<option value="">Primero seleccione un producto...</option>';

                        document.querySelector('#listLote').disabled = true;

                        document.querySelector('#txtCantidad').value = '';

                        document.querySelector('#txtPrecioUnitario').value =
                            '0.00';

                        document.querySelector('#helpStockDisponible').innerHTML =
                            '';

                        stockLoteSeleccionado = 0;

                        cargarListaPreviaVentas(idCliente);

                        swal(
                            "Artículo Reservado",
                            objData.msg,
                            "success"
                        );
                    }

                } else {

                    swal("Error", objData.msg, "error");
                }
            }
        };
    };

    
    
    // ESTADO 1 -> ESTADO 2
    // Venta cambia estado

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

                        let modalElement = document.querySelector(
                            '#modalFormMovimientos'
                        );

                        let modalForm = bootstrap.Modal.getInstance(
                            modalElement
                        );

                        if (modalForm) modalForm.hide();

                        document.querySelector('#formMovimiento').reset();

                        limpiarTablaPreviaVentas();

                        tableMovimientos.ajax.reload(null, false);

                        swal(
                            "Pedido Procesado",
                            data.msg,
                            "success"
                        );

                    } else {

                        swal("Error", data.msg, "error");
                    }
                });
            }
        });
    });

    // Tipo de devolución según cantidad ingresada
    let inputCantDevolver = document.querySelector(
        '#txtCantidadDevolver'
    );

    if (inputCantDevolver) {

        inputCantDevolver.addEventListener('input', function () {
            actualizarTipoDevolucionVisual();
        });
    }

    
    // Reg devolucion parcial o total
   

    document.querySelector('#formDevolucion').onsubmit = function (e) {

        e.preventDefault();

        let cantOrig = parseInt(
            document.querySelector('#txtCantidadOriginal').value
        ) || 0;

        let cantDev = parseInt(
            document.querySelector('#txtCantidadDevolver').value
        ) || 0;

        if (cantDev <= 0 || cantDev > cantOrig) {

            swal(
                "Cantidad inválida",
                `La cantidad a devolver debe estar entre 1 y ${cantOrig}.`,
                "warning"
            );

            return;
        }

        let formData = new FormData(this);

        fetch(base_url + 'Movimientos/setDevolucion', {

            method: 'POST',
            body: formData

        })
        .then(res => res.json())
        .then(data => {

            if (data.status) {

                let modalDev = bootstrap.Modal.getInstance(
                    document.querySelector('#modalDevolucion')
                );

                if (modalDev) modalDev.hide();

                this.reset();

                tableMovimientos.ajax.reload(null, false);

                // Abrir reporte de devolución generado
                if (data.reporte) {

                    mostrarReporteDevolucion(data.reporte);

                } else {

                    swal(
                        "Devolución Registrada",
                        data.msg,
                        "success"
                    );
                }

            } else {

                swal("Error", data.msg, "error");
            }
        });
    };

    // Confirmar estado devuelto desde reporte
    let btnConfirmarDesdeReporte = document.querySelector(
        '#btnConfirmarDevueltoReporte'
    );

    if (btnConfirmarDesdeReporte) {

        btnConfirmarDesdeReporte.addEventListener('click', function () {

            let idDev = this.getAttribute('data-id');
            let cant = this.getAttribute('data-cant');

            if (idDev) {
                confirmarEstadoDevuelto(idDev, cant, true);
            }
        });
    }

}, false);


//Lista activa ventas
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

                        <td class="text-end">
                            ${monedaSimbolo}${parseFloat(item.precio_unitario).toFixed(2)}
                        </td>

                        <td class="text-end fw-bold">
                            ${monedaSimbolo}${subtotal.toFixed(2)}
                        </td>

                        <td class="text-center">

                            <button type="button"
                                    class="btn btn-danger btn-sm btn-del-item-ventas"
                                    data-id="${item.id_movimiento}"
                                    title="Quitar artículo y liberar reserva">

                                <i class="fa fa-trash"></i>

                            </button>

                        </td>
                    </tr>
                `;
            });

            tbody.innerHTML = html;

            document.querySelector('#totalListaVentas').innerHTML =
                `${monedaSimbolo}${totalGeneral.toFixed(2)}`;

            btnProcesar.disabled = false;
        });
}


// Limpiar tabla previa de ventas
function limpiarTablaPreviaVentas() {

    document.querySelector('#tbodyListaVentas').innerHTML = `
        <tr>
            <td colspan="7" class="text-center text-muted">
                No hay artículos agregados para este cliente.
            </td>
        </tr>
    `;

    document.querySelector('#totalListaVentas').innerHTML =
        `${monedaSimbolo}0.00`;

    document.querySelector('#btnProcesarVentas').disabled = true;
}


// Ediutar cantidad
document.addEventListener('change', function (e) {

    if (e.target && e.target.classList.contains('input-cant-ventas')) {

        let input = e.target;

        let idMov = input.getAttribute('data-id');

        let cantAnterior = parseInt(
            input.getAttribute('data-prev')
        ) || 1;

        let nuevaCant = parseInt(input.value) || 0;

        let maxPermitido = parseInt(
            input.getAttribute('max')
        ) || 1;

        let idCliente = document.querySelector('#listCliente').value;

        if (nuevaCant <= 0 || nuevaCant > maxPermitido) {

            swal(
                "Cantidad inválida",
                `Debe ingresar una cantidad entre 1 y ${maxPermitido}.`,
                "warning"
            );

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

                // Actualizar existencias libres del producto
                let prodActual = document.querySelector(
                    '#listProducto'
                ).value;

                if (prodActual) {

                    cargarLotesDeProducto(prodActual);
                }

            } else {

                swal("Error", data.msg, "error");

                input.value = cantAnterior;
            }
        });
    }
});


// Eliminar articulo

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

                let prodActual = document.querySelector(
                    '#listProducto'
                ).value;

                if (prodActual) {

                    cargarLotesDeProducto(prodActual);
                }

            } else {

                swal("Error", data.msg, "error");
            }
        });
    }
});

// VER, EDITAR LISTA, AVANZAR ESTADO Y DEVOLVER

document.addEventListener('click', function (e) {

    // A. Ver detalle del movimiento
    let btnView = e.target.closest('.btnViewMovimiento');

    if (btnView) {

        e.preventDefault();

        let idMovimiento = btnView.getAttribute("cl");

        let request = (window.XMLHttpRequest)
            ? new XMLHttpRequest()
            : new ActiveXObject('Microsoft.XMLHTTP');

        let ajaxUrl = base_url +
            'Movimientos/getMovimiento/' + idMovimiento;

        request.open("GET", ajaxUrl, true);

        request.send();

        request.onreadystatechange = function () {

            if (request.readyState == 4 && request.status == 200) {

                let objData = JSON.parse(request.responseText);

                if (objData.status) {

                    let d = objData.data;

                    document.querySelector("#celIdMovimiento").innerHTML =
                        d.id_movimiento;

                    document.querySelector("#celFecha").innerHTML =
                        d.fecha_movimiento;

                    document.querySelector("#celTipo").innerHTML =
                        (d.tipo_movimiento === 'ENTRADA')
                        ? '<span class="badge bg-success">ENTRADA</span>'
                        : '<span class="badge bg-danger">SALIDA</span>';

                    document.querySelector("#celEstado").innerHTML =
                        d.estado_badge;

                    document.querySelector("#celProducto").innerHTML =
                        `${d.codigo_producto} - ${d.producto}`;

                    document.querySelector("#celLote").innerHTML =
                        `${d.lote} (Ingreso: ${d.fecha_ingreso_lote || 'N/A'})`;

                    document.querySelector("#celCantidad").innerHTML =
                        d.cantidad;

                    document.querySelector("#celCosto").innerHTML =
                        d.costo_formato;

                    document.querySelector("#celPrecio").innerHTML =
                        d.precio_formato;

                    document.querySelector("#celTotal").innerHTML =
                        d.total_formato;

                    document.querySelector("#celCliente").innerHTML =
                        d.cliente;

                    document.querySelector("#celUsuario").innerHTML =
                        d.usuario;

                    document.querySelector("#celMotivo").innerHTML =
                        d.motivo;

                    let modalElement = document.querySelector(
                        '#modalViewMovimiento'
                    );

                    let modalView = bootstrap.Modal.getOrCreateInstance(
                        modalElement
                    );

                    modalView.show();

                } else {

                    swal("Error", objData.msg, "error");
                }
            }
        };
    }


    //edit list ventas

    let btnEditVentas = e.target.closest('.btnEditarListaVentas');

    if (btnEditVentas) {

        e.preventDefault();

        let idCliente = btnEditVentas.getAttribute('cl');

        // NUEVO: Verificar que el perfil permita SALIDA
        const selectTipo = document.querySelector('#listTipoMovimiento');

        if (!Array.from(selectTipo.options).some(op => op.value === 'SALIDA')) {

            swal(
                "Atención",
                "Este perfil no puede abrir el formulario de ventas.",
                "warning"
            );

            return;
        }

        document.querySelector("#formMovimiento").reset();

        selectTipo.value = 'SALIDA';

        actualizarCamposPorTipo('SALIDA');

        // NUEVO: Limpiar los campos del nuevo lote
        cargarLotesDeProducto('');

        document.querySelector('#listCliente').value = idCliente;

        cargarListaPreviaVentas(idCliente);

        let modalElement = document.querySelector(
            '#modalFormMovimientos'
        );

        let modalForm = bootstrap.Modal.getOrCreateInstance(
            modalElement
        );

        modalForm.show();
    }

//Cambio estado bodega
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

//Dev parcial
    let btnDev = e.target.closest('.btnDevolucion');

    if (btnDev) {

        e.preventDefault();

        let idMov = btnDev.getAttribute('cl');

        let cantOriginal = btnDev.getAttribute('cant');

        document.querySelector('#formDevolucion').reset();

        document.querySelector('#id_movimiento_dev').value = idMov;

        document.querySelector('#txtCantidadOriginal').value =
            cantOriginal;

        let inputCantDev = document.querySelector(
            '#txtCantidadDevolver'
        );

        inputCantDev.value = cantOriginal;

        inputCantDev.max = cantOriginal;

        actualizarTipoDevolucionVisual();

        let modalDev = bootstrap.Modal.getOrCreateInstance(
            document.querySelector('#modalDevolucion')
        );

        modalDev.show();
    }

//Devuelto
    let btnConfirmDev = e.target.closest('.btnConfirmarDevuelto');

    if (btnConfirmDev) {

        e.preventDefault();

        let idMov = btnConfirmDev.getAttribute('cl');

        let cant = btnConfirmDev.getAttribute('cant');

        confirmarEstadoDevuelto(idMov, cant, false);
    }

//reporte dev
    let btnRepDev = e.target.closest('.btnReporteDevolucion');

    if (btnRepDev) {

        e.preventDefault();

        let idMov = btnRepDev.getAttribute('cl');

        fetch(base_url + 'Movimientos/getReporteDevolucion/' + idMov)

            .then(res => res.json())

            .then(data => {

                if (data.status && data.reporte) {

                    mostrarReporteDevolucion(data.reporte);

                } else {

                    swal(
                        "Atención",
                        data.msg || "No se encontró información del reporte.",
                        "warning"
                    );
                }
            });
    }

});

//Dev sin sumar a stock
function confirmarEstadoDevuelto(
    idMovDevolucion,
    cantidad,
    cerrarModalReporte = false
) {

    swal({

        title: "Confirmar Estado Devuelto",

        text: `¿Confirma la recepción física de las ${cantidad} unidad(es) en bodega? El movimiento pasará a estado DEVUELTO y no se sumará al stock disponible.`,

        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, marcar como Devuelto",
        cancelButtonText: "Cancelar",
        closeOnConfirm: false,
        closeOnCancel: true

    }, function (isConfirm) {

        if (isConfirm) {

            let formData = new FormData();

            formData.append('id_movimiento', idMovDevolucion);
            formData.append('estado', 8);

            fetch(base_url + 'Movimientos/cambiarEstado', {

                method: 'POST',
                body: formData

            })
            .then(res => res.json())
            .then(data => {

                if (data.status) {

                    if (cerrarModalReporte) {

                        let modalRep = bootstrap.Modal.getInstance(
                            document.querySelector('#modalReporteDevolucion')
                        );

                        if (modalRep) modalRep.hide();
                    }

                    swal(
                        "Devolución Completada",
                        data.msg,
                        "success"
                    );

                    tableMovimientos.ajax.reload(null, false);

                } else {

                    swal("Error", data.msg, "error");
                }
            });
        }
    });
}

//Render dev

function mostrarReporteDevolucion(rep) {

    document.querySelector('#repIdDevolucion').textContent =
        '#' + rep.id_devolucion;

    document.querySelector('#repIdSalida').textContent =
        '#' + rep.id_salida_origen;

    document.querySelector('#repFechaDevolucion').textContent =
        rep.fecha_devolucion;

    document.querySelector('#repFechaSalida').textContent =
        rep.fecha_salida || 'N/A';

    document.querySelector('#repCliente').textContent =
        `${rep.identificacion_cliente} - ${rep.cliente}`;

    document.querySelector('#repProducto').textContent =
        `${rep.codigo_producto} - ${rep.producto}`;

    document.querySelector('#repLote').textContent =
        rep.lote;

    document.querySelector('#repCantOriginal').textContent =
        rep.cantidad_original;

    document.querySelector('#repCantDevuelta').textContent =
        rep.cantidad_devuelta;

    document.querySelector('#repCantConservada').textContent =
        rep.cantidad_conservada;

    document.querySelector('#repPrecioUnit').textContent =
        rep.precio_formato;

    document.querySelector('#repTotalDevuelto').textContent =
        rep.total_formato;

    document.querySelector('#repMotivo').textContent =
        rep.motivo_devolucion || 'Sin observaciones';

    document.querySelector('#repUsuario').textContent =
        rep.usuario_devolucion;

    let tipoTexto = (parseInt(rep.estado_salida) === 6)
        ? 'DEVOLUCIÓN TOTAL'
        : 'DEVOLUCIÓN PARCIAL';

    document.querySelector('#repTipoDevolucion').textContent =
        tipoTexto;

    let alertaStock = document.querySelector('#repAlertaStock');

    let btnConfirmarRep = document.querySelector(
        '#btnConfirmarDevueltoReporte'
    );

    if (parseInt(rep.estado_devolucion) === 8) {

        alertaStock.className = 'alert alert-success py-2 mb-3';

        alertaStock.innerHTML =
            '<i class="fa fa-check-circle"></i> <strong>ESTADO: DEVUELTO</strong> — Recepción física confirmada por bodega (sin suma al stock disponible).';

        btnConfirmarRep.classList.add('d-none');

    } else {

        alertaStock.className = 'alert alert-warning py-2 mb-3';

        alertaStock.innerHTML =
            '<i class="fa fa-clock-o"></i> <strong>ESTADO: PENDIENTE DEVUELTO</strong> — Pendiente de recepción física en bodega para confirmar estado <strong>Devuelto</strong> (no se sumará al stock disponible).';

        btnConfirmarRep.classList.remove('d-none');

        btnConfirmarRep.setAttribute('data-id', rep.id_devolucion);

        btnConfirmarRep.setAttribute('data-cant', rep.cantidad_devuelta);
    }

    let modalRep = bootstrap.Modal.getOrCreateInstance(
        document.querySelector('#modalReporteDevolucion')
    );

    modalRep.show();
}

//impres o guardar
function imprimirReporteDevolucion() {

    let contenido = document.querySelector(
        '#areaImpresionDevolucion'
    ).innerHTML;

    let ventanaPrint = window.open(
        '',
        '_blank',
        'width=900,height=700'
    );

    ventanaPrint.document.write(`
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <title>Reporte de Devolución de Mercadería</title>

            <link rel="stylesheet"
                  href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

            <style>
                body {
                    padding: 25px;
                    font-size: 13px;
                }

                .no-print {
                    display: none !important;
                }
            </style>
        </head>

        <body>

            ${contenido}

            <script>
                window.onload = function() {
                    window.print();
                    window.close();
                };
            <\/script>

        </body>
        </html>
    `);

    ventanaPrint.document.close();
}

//estado visual dev

function actualizarTipoDevolucionVisual() {

    let cantOrig = parseInt(
        document.querySelector('#txtCantidadOriginal').value
    ) || 0;

    let cantDev = parseInt(
        document.querySelector('#txtCantidadDevolver').value
    ) || 0;

    let badgeTipo = document.querySelector(
        '#badgeTipoDevolucionPreview'
    );

    if (!badgeTipo) return;

    if (cantDev > 0 && cantDev < cantOrig) {

        badgeTipo.className = 'badge bg-danger mt-1';

        badgeTipo.textContent =
            `Devolución Parcial (${cantDev} de ${cantOrig} unidades)`;

    } else if (cantDev === cantOrig && cantOrig > 0) {

        badgeTipo.className = 'badge bg-dark mt-1';

        badgeTipo.textContent =
            `Devolución Total (${cantDev} de ${cantOrig} unidades)`;

    } else {

        badgeTipo.className = 'badge bg-secondary mt-1';

        badgeTipo.textContent = 'Ingrese una cantidad válida';
    }
}



//funciones auxiliares
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


//prod y client activos

function cargarSelectsIniciales() {

    fetch(base_url + 'Movimientos/getSelectsIniciales')

        .then(res => res.json())

        .then(data => {

            let optProductos =
                '<option value="">Seleccione un producto...</option>';

            (data.productos || []).forEach(p => {

                optProductos +=
                    `<option value="${p.id_producto}">
                        ${p.codigo} - ${p.nombre}
                    </option>`;
            });

            document.querySelector('#listProducto').innerHTML =
                optProductos;

            let optClientes =
                '<option value="">Seleccione un cliente...</option>';

            (data.clientes || []).forEach(c => {

                optClientes +=
                    `<option value="${c.id_cliente}">
                        ${c.identificacion} - ${c.nombre_razon_social}
                    </option>`;
            });

            document.querySelector('#listCliente').innerHTML =
                optClientes;
        });
}

//mostrar o no nuevo lote

function actualizarCamposNuevoLote() {

    const tipo = document.querySelector(
        '#listTipoMovimiento'
    ).value;

    const lote = document.querySelector('#listLote').value;

    const divNuevoLote = document.querySelector('#divNuevoLote');

    const codigoLote = document.querySelector('#txtCodigoLote');

    const fechaVencimiento = document.querySelector(
        '#txtFechaVencimiento'
    );

    // Solo mostrar los campos cuando sea ENTRADA y lote NUEVO
    const crearLote = tipo === 'ENTRADA' && lote === 'NUEVO';

    divNuevoLote.classList.toggle('d-none', !crearLote);

    codigoLote.required = crearLote;

    codigoLote.disabled = !crearLote;

    fechaVencimiento.disabled = !crearLote;

    // Limpiar campos cuando se seleccione lote existente
    if (!crearLote) {

        codigoLote.value = '';

        fechaVencimiento.value = '';
    }
}


//Lotes de producto 

function cargarLotesDeProducto(idProducto) {

    const solicitudActual = ++secuenciaCargaLotes;

    const selectLote = document.querySelector('#listLote');

    const tipo = document.querySelector(
        '#listTipoMovimiento'
    ).value;

    stockLoteSeleccionado = 0;

    document.querySelector('#helpStockDisponible').textContent = '';

    selectLote.innerHTML =
        '<option value="">Cargando lotes...</option>';

    selectLote.disabled = true;

    actualizarCamposNuevoLote();

    // Si no existe producto seleccionado
    if (!idProducto) {

        selectLote.innerHTML =
            '<option value="">Primero seleccione un producto...</option>';

        return;
    }

    // Consultar lotes mediante AJAX
    fetch(base_url + 'Movimientos/getLotesProducto/' + idProducto)

        .then(res => {

            if (!res.ok) {
                throw new Error('Error al consultar lotes');
            }

            return res.json();
        })

        .then(lotes => {

            // Ignorar respuestas de productos seleccionados anteriormente
            if (solicitudActual !== secuenciaCargaLotes) {
                return;
            }

            if (!Array.isArray(lotes)) {
                throw new Error('Respuesta de lotes inválida');
            }

            selectLote.innerHTML =
                '<option value="">Seleccione un lote...</option>';

            lotes.forEach(l => {

                // Ocultar lotes sin existencias físicas
                if (Number(l.cantidad_disponible) <= 0) {
                    return;
                }

                // En SALIDA ocultar lotes totalmente reservados
                if (tipo === 'SALIDA' && Number(l.stock_libre) <= 0) {
                    return;
                }

                const option = new Option(

                    `${l.codigo_lote} (Disp: ${l.stock_libre} | Ingreso: ${l.fecha_ingreso})`,

                    l.id_lote
                );

                // Datos adicionales de cada lote
                option.dataset.libre = l.stock_libre;

                option.dataset.costo = l.costo_unitario;

                option.dataset.precio = l.precio_venta_sugerido;

                selectLote.add(option);
            });

            //  Permitir crear lotes únicamente en ENTRADA
            if (tipo === 'ENTRADA') {

                selectLote.add(
                    new Option('+ Crear nuevo lote', 'NUEVO')
                );
            }

            // Si no existen lotes disponibles
            if (selectLote.options.length === 1) {

                selectLote.options[0].text =
                    'No hay lotes disponibles';
            }

            selectLote.disabled = false;
        })

        .catch(error => {

            if (solicitudActual !== secuenciaCargaLotes) {
                return;
            }

            selectLote.innerHTML =
                '<option value="">Error al cargar lotes</option>';

            console.error(error);
        });
}

//movimiento modal

function openModal() {

    // Limpiar formulario
    document.querySelector("#formMovimiento").reset();

    // Seleccionar primer movimiento permitido por el perfil
    let selectTipoMovimiento = document.querySelector(
        '#listTipoMovimiento'
    );

    selectTipoMovimiento.selectedIndex = 0;

    // Obtener tipo disponible
    let tipoMovimiento = selectTipoMovimiento.value;

    // Actualizar campos del formulario
    actualizarCamposPorTipo(tipoMovimiento);

    limpiarTablaPreviaVentas();

    // NUEVO: Limpiar selector y campos del nuevo lote
    cargarLotesDeProducto('');

    // Abrir modal
    let modalElement = document.querySelector(
        '#modalFormMovimientos'
    );

    let modalForm = bootstrap.Modal.getOrCreateInstance(
        modalElement
    );

    modalForm.show();
}
