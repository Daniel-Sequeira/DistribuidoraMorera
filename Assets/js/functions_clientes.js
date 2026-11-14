
let tableClientes;
document.addEventListener('DOMContentLoaded', function(){
      
    tableClientes = $('#tableClientes').DataTable({
        "aProcessing":true,
        "aServerSide":false,
        "language":{
            "url": "https://cdn.datatables.net/plug-ins/1.10.20//i18n/Spanish.json"
        },
        "ajax":{
            "url":""+base_url+"Clientes/getClientes",
            "dataSrc":""
        },
        "columns":[
            {"data":"id_cliente"},
            {"data":"nombre_razon_social"},
            {"data":"identificacion"},
            {"data":"telefono"},
            {"data":"correo"},
            {"data":"direccion"},
            {"data":"estado"},
            {"data":"opciones"}
        ],
       
        "responsive": true,
        "bDestroy": true,
        "iDisplayLength":10,
        "order":[[0,"asc"]],
        "dom": 'Bfrtip',
        "buttons": [
            {
                "extend": "copyHtml5",
                "text": "<i class='fa fa-copy'></i> Copiar",
                "titleAttr":"Copiar",
                "className": "btn btn-secondary"
            },
            {
                "extend": "excelHtml5",
                "text": "<i class='fa fa-file-excel-o'></i> Excel",
                "titleAttr":"Exportar a Excel",
                "className": "btn btn-success"
            },
            {
                "extend": "csvHtml5",
                "text": "<i class='fa fa-file-text-o'></i> CSV",
                "titleAttr":"Exportar a CSV",
                "className": "btn btn-info"
            },
            {
                "extend": "pdfHtml5",
                "text": "<i class='fa fa-file-pdf-o'></i> PDF",
                "titleAttr":"Exportar a PDF",
                "className": "btn btn-danger"
            }
        ]
     
    });
     // Filtro por Estado (Columna 5)
    document.addEventListener('change', function (e) {
        if (e.target && e.target.id === 'filtroEstado') {
            let valor = e.target.value;
            // Busca coincidencia exacta ("Activo" o "Inactivo") ignorando las etiquetas HTML 
            tableClientes.column(6).search(valor ? '^' + valor + '$' : '', true, false).draw();
        }
    });



// Captura evento submit del formulario Guardar Cliente
    document.addEventListener('submit', function(e) {
    
   if(e.target && e.target.id === 'formCliente') {
            e.preventDefault();
            console.log("Formulario enviado, validando datos...");

            let strNombre = document.querySelector('#txtNombreRazonSocial').value;
            let strIdentificacion = document.querySelector('#txtIdentificacion').value;
            let strTelefono =document.querySelector('#txtTelefono').value;
            let strEmail = document.querySelector('#txtEmail').value;
            let strDireccion = document.querySelector('#txtDireccion').value;

            if(strNombre == '' || strIdentificacion == '' || strTelefono == '' ||   strEmail == ''||strDireccion == '') {
                swal("Atención", "Todos los campos son obligatorios.", "error");
                return false;
            }
            //Comprobar si los imputs no pasan validacion (invalid)
            let elementsInvalid = document.querySelectorAll('#formCliente .is-invalid');
            if(elementsInvalid.length > 0) {
                swal("Atención", "Por favor, verifique los campos en rojo.", "error");
                return false;
            }

            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url + 'Clientes/setCliente';
            
            //formulario actual
            let formData = new FormData(e.target);

            request.open("POST", ajaxUrl, true);
            request.send(formData);

            request.onreadystatechange = function(){
                if(request.readyState == 4 && request.status == 200) {
                    let objData = JSON.parse(request.responseText);
                    if(objData.status){
                        $('#modalFormCliente').modal("hide");
                        document.querySelector("#formCliente").reset();
                        swal("Clientes", objData.msg, "success");
                        tableClientes.ajax.reload();
                    }else{
                        swal("Error",objData.msg, "error");
                    }
                }
            }
        }
    });
}, false); 
//Ver Un CLiente
document.addEventListener('click', function(e) {
    //btnViewCliente
    let btnView = e.target.closest('.btnViewCliente');
    if(btnView) {
        e.preventDefault(); 
        
        let id_cliente = btnView.getAttribute("cl");
        let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
        let ajaxUrl = base_url + 'Clientes/getCliente/' + id_cliente;
        
        request.open("GET", ajaxUrl, true);
        request.send();

        request.onreadystatechange = function() {
            if (request.readyState == 4 && request.status == 200) {
                let objData = JSON.parse(request.responseText);
                
                if(objData.status) {
                    document.querySelector("#celIdentificacion").innerHTML = objData.data.identificacion;
                    document.querySelector("#celNombre").innerHTML = objData.data.nombre_razon_social;
                    document.querySelector("#celTelefono").innerHTML = objData.data.telefono;
                    document.querySelector("#celEmail").innerHTML = objData.data.correo;
                    document.querySelector("#celDireccion").innerHTML = objData.data.direccion;
                    // Convertir el estado a texto visual
                    let estadoUsuario = (objData.data.estado == true || objData.data.estado == 1) ? 
                                        '<span class="badge bg-success">Activo</span>' : 
                                        '<span class="badge bg-danger">Inactivo</span>';
                    document.querySelector("#celEstado").innerHTML = estadoUsuario;
                    document.querySelector("#celFechaRegistro").innerHTML = objData.data.creado_en;
                    //Mostrar el modal en pantalla (Bootstrap 5)
                    let modalElement = document.querySelector('#modalViewCliente');
                    let modalView = bootstrap.Modal.getOrCreateInstance(modalElement);
                    modalView.show();
                } else {
                    swal("Error", objData.msg, "error");
                }
            }
        }
    }
});

//Editar un cliente (incluye eliminacion logica/ Delete)
// ---- btn edit Cliente ---
document.addEventListener('click', function(e) {
    
    let btnEdit = e.target.closest('.btnEditCliente'); 
    if(btnEdit) {
        e.preventDefault(); 
        
        //Cambiar apariencia del modal a "Actualizar"
        document.querySelector('#titleModal').innerHTML = "Actualizar Cliente";
        document.querySelector('#btnText').innerHTML = "Actualizar";
        document.querySelector('.modal-header').classList.replace('headerRegister', 'headerUpdate');
        document.querySelector('#btnActionForm').classList.replace('btn-primary', 'btn-info');
        
        //Obtener el ID del usuario del atributo 'us' y asignarlo al input oculto
        let id_cliente = btnEdit.getAttribute('cl');
        document.querySelector('#id_cliente').value = id_cliente;
        
        //Mostrar el div del estado
        document.querySelector('#divEstado').style.display = "block";
        //Consulta BD
        let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
        let ajaxUrl = base_url + 'Clientes/getCliente/' + id_cliente; 
        
        request.open("GET", ajaxUrl, true);
        request.send();
        
        request.onreadystatechange = function(){
            if(request.readyState == 4 && request.status == 200) {
                let objData = JSON.parse(request.responseText);
                
                if(objData.status) {
                    //Asignar los valores a los inputs de texto
                    document.querySelector("#txtIdentificacion").value = objData.data.identificacion;
                    document.querySelector("#txtNombreRazonSocial").value = objData.data.nombre_razon_social;
                    document.querySelector("#txtTelefono").value = objData.data.telefono;
                    document.querySelector("#txtEmail").value = objData.data.correo;
                    document.querySelector("#txtDireccion").value = objData.data.direccion;
                    // El estado seleccionado en la BD 
                    document.querySelector('#listStatus').value = objData.data.estado
                   //Mostrar modal
                   let modalElement = document.querySelector('#modalFormCliente');
                    let modalForm = bootstrap.Modal.getOrCreateInstance(modalElement);
                    modalForm.show();
                    
                } else {
                    swal("Error", objData.msg, "error");
                }
            }
        }
    }
});

initValidations();
function openModal() { 
    document.querySelector('#id_cliente').value = ""; 

    // Ajuste de clases e interfaz
    let modalHeader = document.querySelector('.modal-header');
    if (modalHeader.classList.contains('headerUpdate')) {
        modalHeader.classList.replace('headerUpdate', 'headerRegister');
    }

    let btnAction = document.querySelector('#btnActionForm');
    if (btnAction.classList.contains('btn-info')) {
        btnAction.classList.replace('btn-info', 'btn-primary');
    }

    document.querySelector('#btnText').innerHTML = "Guardar";
    document.querySelector('#titleModal').innerHTML = "Nuevo Cliente";
    document.querySelector("#formCliente").reset(); 

    document.querySelector('#divEstado').style.display = "none";
    document.querySelector('#txtNombreRazonSocial').removeAttribute('readonly');

    // Apertura del modal en Bootstrap 5
    let modalElement = document.querySelector('#modalFormCliente');
    let modalFormCLiente = bootstrap.Modal.getOrCreateInstance(modalElement);
    modalFormCLiente.show();
}
