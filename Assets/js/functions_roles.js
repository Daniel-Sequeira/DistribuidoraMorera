var tableRoles;
document.addEventListener('DOMContentLoaded', function(){
    tableRoles = $('#tableRoles').dataTable({
        "aProcessing":true,
        "aServerSide":true,
        "language":{
            "url": "https://cdn.datatables.net/plug-ins/1.10.20//i18n/Spanish.json"
        },
        "ajax":{
            "url":""+base_url+"/Roles/getRoles",
            "dataSrc":""
        },
        "columns":[
            {"data":"id_rol"},
            {"data":"nombre_rol"},
            {"data":"estado"},
            {"data":"opciones"}
        ],
        "responsive": true,
        "bDestroy": true,
        "iDisplayLength":10,
        "order":[[0,"asc"]]
    });

    //Nuevo Rol
    var formRol = document.querySelector("#formRol");
    formRol.onsubmit = function(e){
        e.preventDefault();
        var strNombre = document.querySelector('#nombreRolInput').value;
        if(strNombre == '')
        {
            swal("Atención", "El nuevo Rol debe tener un nombre", "error");
            return false;
        }
        var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
        var ajaxUrl = base_url + '/Roles/setRol';
        var formData = new FormData(formRol);
        request.open("POST",ajaxUrl,true);
        request.send(formData);
        request.onreadystatechange = function(){
            if(request.readyState == 4 && request.status == 200) {
                var objData = JSON.parse(request.responseText); 

                if(objData.status)
                {
                    $('#modalFormRol').modal("hide");
                    formRol.reset();
                    swal("Roles de usuario", objData.msg,"success");
                    $('#tableRoles').DataTable().ajax.reload().savePermisos();
                }else{
                    swal("Error", objData.msg, "error");
                }
            }
            
        }
    }
});
$('#tableRoles').DataTable();

function openModal() { 
    document.querySelector('#id_rol').value = ""; 

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
    document.querySelector('#titleModal').innerHTML = "Nuevo Rol";
    document.querySelector("#formRol").reset(); 

    document.querySelector('#divEstado').style.display = "none";
    document.querySelector('#nombreRolInput').removeAttribute('readonly');

    // Apertura del modal en Bootstrap 5 (Vanilla JS)
    let modalElement = document.querySelector('#modalFormRol');
    let modalFormRol = bootstrap.Modal.getOrCreateInstance(modalElement);
    modalFormRol.show();
}
//Levantar modal permisos
document.addEventListener('click', function(e) {
    let btn = e.target.closest('.btnPermisosRol'); 
    
    if(btn) {
        e.preventDefault(); 
        let idrol = btn.getAttribute('rl');
        let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
        let ajaxUrl = base_url + '/Permisos/getPermisosRol/' + idrol;
        
        request.open("GET", ajaxUrl, true);
        request.send();
        
        request.onreadystatechange = function(){
            if(request.readyState == 4 && request.status == 200) {
                
                //HTML del modal en el contenedor
                document.querySelector('#contentAjax').innerHTML = request.responseText;
                
                //Busca el modal por su ID 
                let modalElement = document.querySelector('#modalPermisos');
                
                if (modalElement) {
                    //Inicializar y mostrar
                    let modalPermisos = bootstrap.Modal.getOrCreateInstance(modalElement);
                    modalPermisos.show();
                    document.querySelector('#formPermisos').addEventListener('submit',savePermisos,false);
                }
            }
        }
    }
});

// Función para EDITAR ROL (Delegación de eventos)
document.addEventListener('click', function(e) {
    
    let btn = e.target.closest('.btnEditRol'); 
    if(btn) {
        e.preventDefault(); 
        document.querySelector('#titleModal').innerHTML = "Actualizar Rol";
        document.querySelector('#btnText').innerHTML = "Actualizar";
        document.querySelector('.modal-header').classList.replace('headerRegister', 'headerUpdate');
        document.querySelector('#btnActionForm').classList.replace('btn-primary', 'btn-info');
        
        var idrol = btn.getAttribute('rl');
        document.querySelector('#id_rol').value = idrol;
        
        document.querySelector('#divEstado').style.display = "block";
        
        // Campo nombre no modificar
        document.querySelector('#nombreRolInput').setAttribute('readonly', 'readonly');
        
        // Consulta BD ---
        var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
        var ajaxUrl = base_url + '/Roles/getRol/' + idrol; 
        
        request.open("GET", ajaxUrl, true);
        request.send();
        
        request.onreadystatechange = function(){
            if(request.readyState == 4 && request.status == 200) {
                var objData = JSON.parse(request.responseText);
                
                if(objData.status) {
                    // El nombre (que estará bloqueado)
                    document.querySelector('#nombreRolInput').value = objData.data.nombre_rol;
                    
                    // El estado seleccionado en la BD
                    document.querySelector('#listStatus').value = objData.data.estado;
                    
                    // Mostramos el modal
                    $('#modalFormRol').modal('show');
                } else {
                    swal("Error", objData.msg, "error");
                }
            }
        }
    }
});

function savePermisos(event){
    event.preventDefault();
    var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    var ajaxUrl = base_url +'/Permisos/setPermisos';
    var formElement = document.querySelector("#formPermisos");
    var formData = new FormData(formElement);
    request.open("POST",ajaxUrl,true);
    request.send(formData);

    request.onreadystatechange = function(){
        if(request.readyState == 4 && request.status ==200){
            var objData = JSON.parse(request.responseText);
            if(objData.status){
                swal("Permisos de usuario", objData.msg, "success");
            }else{
                swal("Error", objData.msg, "error");
            }
        }
    }
}