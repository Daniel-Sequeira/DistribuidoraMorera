let tableProveedores;

document.addEventListener('DOMContentLoaded', function () {
    tableProveedores = $('#tableProveedores').DataTable({
        "aProcessing": true,
        "aServerSide": false,
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "ajax": {
            "url": base_url + "Proveedores/getProveedores",
            "dataSrc": ""
        },
        "columns": [
            { "data": "id_proveedor" },
            { "data": "identificacion" },
            { "data": "nombre_razon_social" },
            { "data": "telefono" },
            { "data": "correo" },
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

    // Filtro por Estado (Columna índice 5)
    document.addEventListener('change', function (e) {
        if (e.target && e.target.id === 'filtroEstado') {
            let valor = e.target.value;
            tableProveedores.column(5).search(valor ? '^' + valor + '$' : '', true, false).draw();
        }
    });

    // Guardar o Actualizar Proveedor
    let formProveedor = document.querySelector("#formProveedor");
    if (formProveedor) {
        formProveedor.onsubmit = function (e) {
            e.preventDefault();

            let strIdentificacion = document.querySelector('#txtIdentificacion').value.trim();
            let strNombre = document.querySelector('#txtNombre').value.trim();
            let intStatus = document.querySelector('#listStatus').value;

            if (strIdentificacion === '' || strNombre === '' || intStatus === '') {
                swal("Atención", "La identificación y el nombre o razón social son obligatorios.", "error");
                return false;
            }

            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url + 'Proveedores/setProveedor';
            let formData = new FormData(formProveedor);

            request.open("POST", ajaxUrl, true);
            request.send(formData);

            request.onreadystatechange = function () {
                if (request.readyState == 4 && request.status == 200) {
                    let objData = JSON.parse(request.responseText);

                    if (objData.status) {
                        let modalElement = document.querySelector('#modalFormProveedores');
                        let modalForm = bootstrap.Modal.getOrCreateInstance(modalElement);
                        modalForm.hide();

                        formProveedor.reset();
                        swal("Proveedores", objData.msg, "success");
                        tableProveedores.ajax.reload(null, false);
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

// Ver Detalle de Proveedor
document.addEventListener('click', function (e) {
    let btnView = e.target.closest('.btnViewProveedor');
    if (btnView) {
        e.preventDefault();

        let id_proveedor = btnView.getAttribute("pr");
        let request = new XMLHttpRequest();
        let ajaxUrl = base_url + 'Proveedores/getProveedor/' + id_proveedor;

        request.open("GET", ajaxUrl, true);
        request.send();

        request.onreadystatechange = function () {
            if (request.readyState == 4 && request.status == 200) {
                let objData = JSON.parse(request.responseText);

                if (objData.status) {
                    let prov = objData.data;
                    document.querySelector("#celIdProveedor").innerHTML = prov.id_proveedor;
                    document.querySelector("#celIdentificacion").innerHTML = prov.identificacion;
                    document.querySelector("#celNombre").innerHTML = prov.nombre_razon_social;
                    document.querySelector("#celTelefono").innerHTML = prov.telefono || 'No registrado';
                    document.querySelector("#celCorreo").innerHTML = prov.correo || 'No registrado';
                    document.querySelector("#celDireccion").innerHTML = prov.direccion || 'No registrada';
                    document.querySelector("#celFechaRegistro").innerHTML = prov.creado_en;

                    let estadoProveedor = (prov.estado === true || prov.estado === 't' || prov.estado == 1)
                        ? '<span class="badge bg-success">Activo</span>'
                        : '<span class="badge bg-danger">Inactivo</span>';
                    document.querySelector("#celEstado").innerHTML = estadoProveedor;

                    let modalElement = document.querySelector('#modalViewProveedor');
                    let modalView = bootstrap.Modal.getOrCreateInstance(modalElement);
                    modalView.show();
                } else {
                    swal("Error", objData.msg, "error");
                }
            }
        };
    }
});

// Editar Proveedor
document.addEventListener('click', function (e) {
    let btnEdit = e.target.closest('.btnEditProveedor');
    if (btnEdit) {
        e.preventDefault();

        document.querySelector('#titleModal').innerHTML = "Actualizar Proveedor";
        document.querySelector('#btnText').innerHTML = "Actualizar";

        let modalHeader = document.querySelector('#modalFormProveedores .modal-header');
        if (modalHeader.classList.contains('headerRegister')) {
            modalHeader.classList.replace('headerRegister', 'headerUpdate');
        }

        let btnAction = document.querySelector('#btnActionForm');
        if (btnAction.classList.contains('btn-primary')) {
            btnAction.classList.replace('btn-primary', 'btn-info');
        }

        let id_proveedor = btnEdit.getAttribute("pr");
        let request = new XMLHttpRequest();
        let ajaxUrl = base_url + 'Proveedores/getProveedor/' + id_proveedor;

        request.open("GET", ajaxUrl, true);
        request.send();

        request.onreadystatechange = function () {
            if (request.readyState == 4 && request.status == 200) {
                let objData = JSON.parse(request.responseText);

                if (objData.status) {
                    let prov = objData.data;
                    document.querySelector("#id_proveedor").value = prov.id_proveedor;
                    document.querySelector("#txtIdentificacion").value = prov.identificacion;
                    document.querySelector("#txtNombre").value = prov.nombre_razon_social;
                    document.querySelector("#txtTelefono").value = prov.telefono || '';
                    document.querySelector("#txtCorreo").value = prov.correo || '';
                    document.querySelector("#txtDireccion").value = prov.direccion || '';
                    document.querySelector("#listStatus").value = (prov.estado === true || prov.estado === 't' || prov.estado == 1) ? 1 : 0;

                    let modalElement = document.querySelector('#modalFormProveedores');
                    let modalForm = bootstrap.Modal.getOrCreateInstance(modalElement);
                    modalForm.show();
                } else {
                    swal("Error", objData.msg, "error");
                }
            }
        };
    }
});

// Abrir Modal para Nuevo Proveedor
function openModal() {
    document.querySelector('#id_proveedor').value = "";

    let modalHeader = document.querySelector('#modalFormProveedores .modal-header');
    if (modalHeader.classList.contains('headerUpdate')) {
        modalHeader.classList.replace('headerUpdate', 'headerRegister');
    }

    let btnAction = document.querySelector('#btnActionForm');
    if (btnAction.classList.contains('btn-info')) {
        btnAction.classList.replace('btn-info', 'btn-primary');
    }

    document.querySelector('#btnText').innerHTML = "Guardar";
    document.querySelector('#titleModal').innerHTML = "Nuevo Proveedor";
    document.querySelector("#formProveedor").reset();

    let modalElement = document.querySelector('#modalFormProveedores');
    let modalFormProveedores = bootstrap.Modal.getOrCreateInstance(modalElement);
    modalFormProveedores.show();
}