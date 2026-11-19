let tableProductos;
document.addEventListener('DOMContentLoaded', function () {
    // 1. Inicializar DataTable con las 9 columnas de la vista
    tableProductos = $('#tableProductos').DataTable({
        "aProcessing": true,
        "aServerSide": false,
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "ajax": {
            "url": base_url + "Productos/getProductos",
            "dataSrc": ""
        },
        "columns": [
            { "data": "id_producto" },
            { "data": "codigo" },
            { "data": "nombre" },
            { "data": "proveedor" },
            { "data": "estanteria" },
            { "data": "stock" },
            { "data": "precio" },
            { "data": "estado" },
            { "data": "opciones" }
        ],
        "responsive": true,
        "bDestroy": true,
        "iDisplayLength": 10,
        "order": [[0, "asc"]],
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

    // 2. Filtro por Estado (Columna índice 7: "Activo" / "Inactivo")
    document.addEventListener('change', function (e) {
        if (e.target && e.target.id === 'filtroEstado') {
            let valor = e.target.value;
            tableProductos.column(7).search(valor ? '^' + valor + '$' : '', true, false).draw();
        }
    });

    // 3. Cargar selectores de Proveedores y Estanterías al iniciar
    fntGetSelects();

    // 4. Guardar Nuevo Producto (con Lote Inicial FIFO) o Actualizar Producto
    let formProducto = document.querySelector("#formProducto");
    if (formProducto) {
        formProducto.onsubmit = function (e) {
            e.preventDefault();

            let intIdProducto = document.querySelector('#id_producto').value;
            let strNombre = document.querySelector('#txtNombre').value.trim();
            let intProveedor = document.querySelector('#listProveedor').value;
            let intEstanteria = document.querySelector('#listEstanteria').value;
            let strCodigo = document.querySelector('#txtCodigo').value.trim();
            let intStatus = document.querySelector('#listStatus').value;
            let decPrecio = document.querySelector('#txtPrecio').value;

            // Validación de campos base del catálogo
            if (strNombre === '' || intProveedor === '' || intEstanteria === '' || strCodigo === '' || intStatus === '' || decPrecio === '') {
                swal("Atención", "Todos los campos marcados con (*) son obligatorios.", "error");
                return false;
            }

            if (parseFloat(decPrecio) <= 0) {
                swal("Atención", "El precio de venta debe ser mayor a 0.", "error");
                return false;
            }

            // Validación exclusiva para producto nuevo (creación de primer lote y movimiento ENTRADA)
            if (intIdProducto === '') {
                let strLote = document.querySelector('#txtLote').value.trim();
                let decCosto = document.querySelector('#txtCosto').value;
                let intStock = document.querySelector('#txtStock').value;

                if (strLote === '' || decCosto === '' || intStock === '') {
                    swal("Atención", "Debe ingresar el código de lote, costo unitario y cantidad inicial.", "error");
                    return false;
                }
                if (parseFloat(decCosto) < 0 || parseInt(intStock) <= 0) {
                    swal("Atención", "La cantidad inicial debe ser mayor a 0 y el costo no puede ser negativo.", "error");
                    return false;
                }
            }

            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url + 'Productos/setProducto';
            let formData = new FormData(formProducto);

            request.open("POST", ajaxUrl, true);
            request.send(formData);

            request.onreadystatechange = function () {
                if (request.readyState == 4 && request.status == 200) {
                    let objData = JSON.parse(request.responseText);

                    if (objData.status) {
                        let modalElement = document.querySelector('#modalFormProductos');
                        let modalForm = bootstrap.Modal.getOrCreateInstance(modalElement);
                        modalForm.hide();

                        formProducto.reset();
                        swal("Productos", objData.msg, "success");
                        tableProductos.ajax.reload(null, false);
                    } else {
                        swal("Error", objData.msg, "error");
                    }
                }
                return false;
            };
        };
    }

    if (typeof initValidations === 'function') {
        initValidations();
    }
}, false);

// Cargar opciones de Proveedores y Estanterías desde el controlador
function fntGetSelects() {
    if (document.querySelector('#listProveedor')) {
        let ajaxUrlProv = base_url + 'Productos/getSelectProveedores';
        let requestProv = new XMLHttpRequest();
        requestProv.open("GET", ajaxUrlProv, true);
        requestProv.send();
        requestProv.onreadystatechange = function () {
            if (requestProv.readyState == 4 && requestProv.status == 200) {
                document.querySelector('#listProveedor').innerHTML = requestProv.responseText;
            }
        };
    }

    if (document.querySelector('#listEstanteria')) {
        let ajaxUrlEst = base_url + 'Productos/getSelectEstanterias';
        let requestEst = new XMLHttpRequest();
        requestEst.open("GET", ajaxUrlEst, true);
        requestEst.send();
        requestEst.onreadystatechange = function () {
            if (requestEst.readyState == 4 && requestEst.status == 200) {
                document.querySelector('#listEstanteria').innerHTML = requestEst.responseText;
            }
        };
    }
}

// Ver Detalle de Producto y sus Lotes Activos (Orden FIFO)
document.addEventListener('click', function (e) {
    let btnView = e.target.closest('.btnViewProducto');
    if (btnView) {
        e.preventDefault();

        let id_producto = btnView.getAttribute("pr") || btnView.getAttribute("cl");
        let request = new XMLHttpRequest();
        let ajaxUrl = base_url + 'Productos/getProducto/' + id_producto;

        request.open("GET", ajaxUrl, true);
        request.send();

        request.onreadystatechange = function () {
            if (request.readyState == 4 && request.status == 200) {
                let objData = JSON.parse(request.responseText);

                if (objData.status) {
                    let prod = objData.data;
                    document.querySelector("#celIdProducto").innerHTML = prod.id_producto;
                    document.querySelector("#celCodigo").innerHTML = prod.codigo;
                    document.querySelector("#celNombre").innerHTML = prod.nombre;
                    document.querySelector("#celDescripcion").innerHTML = prod.descripcion || 'Sin descripción';
                    document.querySelector("#celProveedor").innerHTML = prod.proveedor;
                    document.querySelector("#celEstanteria").innerHTML = prod.estanteria;
                    document.querySelector("#celPrecio").innerHTML = '₡' + parseFloat(prod.precio).toFixed(2);
                    document.querySelector("#celStock").innerHTML = prod.stock;
                    document.querySelector("#celFechaRegistro").innerHTML = prod.creado_en;

                    let estadoProducto = (prod.estado == true || prod.estado == 1)
                        ? '<span class="badge bg-success">Activo</span>'
                        : '<span class="badge bg-danger">Inactivo</span>';
                    document.querySelector("#celEstado").innerHTML = estadoProducto;

                    // Renderizar tabla de lotes activos ordenados por fecha_ingreso ASC (FIFO)
                    let tbodyLotes = document.querySelector("#tableLotesActivos");
                    tbodyLotes.innerHTML = "";

                    if (prod.lotes && prod.lotes.length > 0) {
                        prod.lotes.forEach((lote, index) => {
                            let badgeFifo = (index === 0)
                                ? ' <span class="badge bg-warning text-dark">Próximo a salir</span>'
                                : '';
                            tbodyLotes.innerHTML += `
                                <tr>
                                    <td>${lote.codigo_lote}${badgeFifo}</td>
                                    <td>${lote.fecha_ingreso}</td>
                                    <td>$${parseFloat(lote.costo_unitario).toFixed(2)}</td>
                                    <td>${lote.cantidad_inicial}</td>
                                    <td class="fw-bold">${lote.cantidad_disponible}</td>
                                </tr>
                            `;
                        });
                    } else {
                        tbodyLotes.innerHTML = `
                            <tr>
                                <td colspan="5" class="text-muted">No hay lotes con existencias disponibles.</td>
                            </tr>
                        `;
                    }

                    let modalElement = document.querySelector('#modalViewProducto');
                    let modalView = bootstrap.Modal.getOrCreateInstance(modalElement);
                    modalView.show();
                } else {
                    swal("Error", objData.msg, "error");
                }
            }
        };
    }
});

// Editar Producto (Bloquea campos de lote/stock para proteger la cola FIFO)
document.addEventListener('click', function (e) {
    let btnEdit = e.target.closest('.btnEditProducto');
    if (btnEdit) {
        e.preventDefault();

        document.querySelector('#titleModal').innerHTML = "Actualizar Producto";
        document.querySelector('#btnText').innerHTML = "Actualizar";

        let modalHeader = document.querySelector('#modalFormProductos .modal-header');
        if (modalHeader.classList.contains('headerRegister')) {
            modalHeader.classList.replace('headerRegister', 'headerUpdate');
        }

        let btnAction = document.querySelector('#btnActionForm');
        if (btnAction.classList.contains('btn-primary')) {
            btnAction.classList.replace('btn-primary', 'btn-info');
        }

        let id_producto = btnEdit.getAttribute("pr") || btnEdit.getAttribute("cl");
        let request = new XMLHttpRequest();
        let ajaxUrl = base_url + 'Productos/getProducto/' + id_producto;

        request.open("GET", ajaxUrl, true);
        request.send();

        request.onreadystatechange = function () {
            if (request.readyState == 4 && request.status == 200) {
                let objData = JSON.parse(request.responseText);

                if (objData.status) {
                    let prod = objData.data;
                    document.querySelector("#id_producto").value = prod.id_producto;
                    document.querySelector("#txtNombre").value = prod.nombre;
                    document.querySelector("#txtDescripcion").value = prod.descripcion;
                    document.querySelector("#listProveedor").value = prod.id_proveedor;
                    document.querySelector("#listEstanteria").value = prod.id_estanteria;
                    document.querySelector("#txtCodigo").value = prod.codigo;
                    document.querySelector("#txtPrecio").value = prod.precio;
                    document.querySelector("#listStatus").value = (prod.estado == 1 || prod.estado == true) ? 1 : 0;

                    // Bloquear edición directa de lote, costo y stock en modo edición
                    document.querySelector("#txtLote").value = "";
                    document.querySelector("#txtCosto").value = "";
                    document.querySelector("#txtStock").value = prod.stock;

                    document.querySelector("#txtLote").setAttribute("disabled", true);
                    document.querySelector("#txtCosto").setAttribute("disabled", true);
                    document.querySelector("#txtStock").setAttribute("disabled", true);
                    document.querySelector("#notaEdicionFifo").classList.remove("d-none");

                    let modalElement = document.querySelector('#modalFormProductos');
                    let modalForm = bootstrap.Modal.getOrCreateInstance(modalElement);
                    modalForm.show();
                } else {
                    swal("Error", objData.msg, "error");
                }
            }
        };
    }
});

// Abrir Modal para Nuevo Producto
function openModal() {
    document.querySelector('#id_producto').value = "";

    let modalHeader = document.querySelector('#modalFormProductos .modal-header');
    if (modalHeader.classList.contains('headerUpdate')) {
        modalHeader.classList.replace('headerUpdate', 'headerRegister');
    }

    let btnAction = document.querySelector('#btnActionForm');
    if (btnAction.classList.contains('btn-info')) {
        btnAction.classList.replace('btn-info', 'btn-primary');
    }

    document.querySelector('#btnText').innerHTML = "Guardar";
    document.querySelector('#titleModal').innerHTML = "Nuevo Producto";
    document.querySelector("#formProducto").reset();

    // Rehabilitar campos de inventario inicial para crear el primer lote FIFO
    document.querySelector('#txtCodigo').removeAttribute('readonly');
    document.querySelector('#txtLote').removeAttribute('disabled');
    document.querySelector('#txtCosto').removeAttribute('disabled');
    document.querySelector('#txtStock').removeAttribute('disabled');
    document.querySelector('#notaEdicionFifo').classList.add('d-none');

    let modalElement = document.querySelector('#modalFormProductos');
    let modalFormProductos = bootstrap.Modal.getOrCreateInstance(modalElement);
    modalFormProductos.show();
}