let tableUsuarios;
document.addEventListener('DOMContentLoaded', function(){
      
    tableUsuarios = $('#tableUsuarios').DataTable({
        "aProcessing":true,
        "aServerSide":true,
        "language":{
            "url": "https://cdn.datatables.net/plug-ins/1.10.20//i18n/Spanish.json"
        },
        "ajax":{
            "url":""+base_url+"/Usuarios/getUsuarios",
            "dataSrc":""
        },
        "columns":[
            {"data":"id_usuario"},
            {"data":"id_rol"},
            {"data":"identificacion"},
            {"data":"nombre_completo"},
            {"data":"correo"},
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

     //INICIALIZAR SELECT2
    $('#listRolid').select2({
        theme: 'bootstrap-5',
        dropdownParent: $('#modalFormUsuario'), 
        width: '90%'
    });

    // Cargar Roles y Validaciones al abrir modal
            rolesUsuario();
            initValidations();
     

    //Delegacion de eventos clic en botones que son dinamicos
    document.addEventListener('click', function(e) {
    //btnViewUsuario
    let btnView = e.target.closest('.btnViewUsuario');
    if(btnView) {
        e.preventDefault(); 
        
        let id_usuario = btnView.getAttribute("us");
        let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
        let ajaxUrl = base_url + '/Usuarios/getUsuario/' + id_usuario;
        
        request.open("GET", ajaxUrl, true);
        request.send();

        request.onreadystatechange = function() {
            if (request.readyState == 4 && request.status == 200) {
                let objData = JSON.parse(request.responseText);
                
                if(objData.status) {
                    document.querySelector("#celRol").innerHTML = objData.data.nombre_rol;
                    document.querySelector("#celIdentificacion").innerHTML = objData.data.identificacion;
                    document.querySelector("#celNombre").innerHTML = objData.data.nombre_completo;
                    document.querySelector("#celEmail").innerHTML = objData.data.correo;
                    
                    // Convertir el estado a texto visual
                    let estadoUsuario = (objData.data.estado == true || objData.data.estado == 1) ? 
                                        '<span class="badge bg-success">Activo</span>' : 
                                        '<span class="badge bg-danger">Inactivo</span>';
                    document.querySelector("#celEstado").innerHTML = estadoUsuario;
                    document.querySelector("#celFechaRegistro").innerHTML = objData.data.creado_en;
                    //Mostrar el modal en pantalla (Bootstrap 5)
                    let modalElement = document.querySelector('#modalViewUsuario');
                    let modalView = bootstrap.Modal.getOrCreateInstance(modalElement);
                    modalView.show();
                } else {
                    swal("Error", objData.msg, "error");
                }
            }
        }
    }
});

 // ---- btn edit usuario ---
document.addEventListener('click', function(e) {
    
    let btnEdit = e.target.closest('.btnEditUsuario'); 
    if(btnEdit) {
        e.preventDefault(); 
        
        //Cambiar apariencia del modal a "Actualizar"
        document.querySelector('#titleModal').innerHTML = "Actualizar Usuario";
        document.querySelector('#btnText').innerHTML = "Actualizar";
        document.querySelector('.modal-header').classList.replace('headerRegister', 'headerUpdate');
        document.querySelector('#btnActionForm').classList.replace('btn-primary', 'btn-info');
        
        //Obtener el ID del usuario del atributo 'us' y asignarlo al input oculto
        let id_usuario = btnEdit.getAttribute('us');
        document.querySelector('#id_usuario').value = id_usuario;
        
        //Mostrar el div del estado
        document.querySelector('#divEstado').style.display = "block";
        //Consulta BD
        let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
        let ajaxUrl = base_url + '/Usuarios/getUsuario/' + id_usuario; 
        
        request.open("GET", ajaxUrl, true);
        request.send();
        
        request.onreadystatechange = function(){
            if(request.readyState == 4 && request.status == 200) {
                let objData = JSON.parse(request.responseText);
                
                if(objData.status) {
                    //Asignar los valores a los inputs de texto
                    document.querySelector('#txtIdentificacion').value = objData.data.identificacion;
                    document.querySelector('#txtNombre').value = objData.data.nombre_completo;
                    document.querySelector('#txtEmail').value = objData.data.correo;
                    
                    // Asignar el select del Rol y actualizar Select2 visualmente
                    $('#listRolid').val(objData.data.id_rol).trigger('change');
                    
                    // El estado seleccionado en la BD 
                    document.querySelector('#listStatus').value = objData.data.estado;
                    
                    // Limpiar el campo de contraseña por seguridad
                    document.querySelector('#txtPassword').value = "";
                    
                    // Mostramos el modal usando jQuery 
                    $('#modalFormUsuario').modal('show');
                } else {
                    swal("Error", objData.msg, "error");
                }
            }
        }
    }
});

     
// Captura evento submit del formulario para guardar usuario
    document.addEventListener('submit', function(e) {
    
   if(e.target && e.target.id === 'formUsuario') {
            e.preventDefault();
            console.log("Formulario enviado, validando datos...");

            let strRol = $('#listRolid').val(); 
            let strIdentificacion = document.querySelector('#txtIdentificacion').value;
            let strNombre = document.querySelector('#txtNombre').value;
            let strEmail = document.querySelector('#txtEmail').value;
            let strPassword = document.querySelector('#txtPassword').value;

            if(strIdentificacion == '' || strRol == '' || strNombre == '' || strEmail == '') {
                swal("Atención", "Todos los campos son obligatorios.", "error");
                return false;
            }
            //Comprobar si los imputs no pasan validacion (invalid)
            let elementsInvalid = document.querySelectorAll('#formUsuario .is-invalid');
            if(elementsInvalid.length > 0) {
                swal("Atención", "Por favor, verifique los campos en rojo.", "error");
                return false;
            }

            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url + '/Usuarios/setUsuario';
            
            //formulario actual
            let formData = new FormData(e.target);

            request.open("POST", ajaxUrl, true);
            request.send(formData);

            request.onreadystatechange = function(){
                if(request.readyState == 4 && request.status == 200) {
                    let objData = JSON.parse(request.responseText);
                    if(objData.status){
                        $('#modalFormUsuario').modal("hide");
                        document.querySelector("#formUsuario").reset();
                        swal("Usuarios", objData.msg, "success");
                        tableUsuarios.ajax.reload();
                    }else{
                        swal("Error",objData.msg, "error");
                    }
                }
            }
        }
    });

}); 

// FUNCIONES EXTERNAS 

function rolesUsuario(){
    let ajaxUrl = base_url + '/Roles/getSelectRoles';
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    request.open("GET", ajaxUrl, true);
    request.send();

    request.onreadystatechange = function(){
        if(request.readyState == 4 && request.status == 200){
            $('#listRolid').html(request.responseText);
            $('#listRolid').trigger('change'); 
        }
    }
}

function openModal() { 
    document.querySelector('#id_usuario').value = ""; 

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
    document.querySelector('#titleModal').innerHTML = "Nuevo Usuario";
    document.querySelector("#formUsuario").reset(); 
    
    // Resetear el select2 visualmente
    $('#listRolid').val('').trigger('change');

    document.querySelector('#divEstado').style.display = "none";
    document.querySelector('#txtNombre').removeAttribute('readonly');

    // Apertura del modal en Bootstrap 5
    let modalElement = document.querySelector('#modalFormUsuario');
    let modalFormRol = bootstrap.Modal.getOrCreateInstance(modalElement);
    modalFormRol.show();
}

function openModalPerfil(){
   $('#modalFormPerfil').modal('show');
   if(typeof fntValidText === 'function') { fntValidText(); }
   if(typeof fntValidNumber === 'function') { fntValidNumber(); }
   if(typeof fntValidEmail === 'function') { fntValidEmail(); }
}

// Actualizar usuario desde modal perfil
    document.addEventListener('submit', function(e) {
   if(e.target && e.target.id === 'formPerfil') {
            e.preventDefault(); 
            let strIdentificacion = document.querySelector('#txtIdentificacion').value;
            let strNombre = document.querySelector('#txtNombre').value;
            let strPassword = document.querySelector('#txtPassword').value;
            let strPasswordConfirm = document.querySelector('#txtPasswordCOnfirm').value;
            if(strIdentificacion == '' || strNombre == '') {
                swal("Atención", "Todos los campos son obligatorios.", "error");
                return false;
            }
            if(strPassword !="" || strPasswordConfirm != "")
            {
                if(strPassword != strPasswordConfirm){
                    swal("Atencion", "Las contraseñas no coinciden", "info");
                    return false;
                }
                if(strPassword.length < 6){
                    swal("Atencion", "La contraseña debe tener un minimo de 6 caracteres.","info")
                    return false;
                }
            }
            //Comprobar si los imputs no pasan validacion (invalid)
            let elementsInvalid = document.querySelectorAll('#formPerfil .is-invalid');
            if(elementsInvalid.length > 0) {
                swal("Atención", "Por favor, verifique los campos en rojo.", "error");
                return false;
            }

            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url + '/Usuarios/putPerfil';
            let formData = new FormData(e.target);

            request.open("POST", ajaxUrl, true);
            request.send(formData);
            request.onreadystatechange = function(){
                if(request.readyState != 4) return;
                if(request.status == 200) {
                    let objData = JSON.parse(request.responseText);
                    if(objData.status){
                        $('#modalFormPerfil').modal("hide");
                       swal({
                        title:"",
                        text: objData.msg,
                        type: "success",
                        confirmButtonText: "Aceptar",
                        closeOnConfirm: false,
                       }, function(isConfirm){
                        if(isConfirm){
                            location.reload();
                        }
                    });
                }else{
                    swal("Error", objData.msg,"error");
                }
            }
        }
    }

}, false); 