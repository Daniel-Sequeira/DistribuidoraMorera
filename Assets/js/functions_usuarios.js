document.addEventListener('DOMContentLoaded', function(){
    
    //INICIALIZAR SELECT2
    $('#listRolid').select2({
        theme: 'bootstrap-5',
        dropdownParent: $('#modalFormUsuario') 
    });

    // EVENTO AL ABRIR EL MODAL
    var modalFormUsuario = document.getElementById('modalFormUsuario');
    if(modalFormUsuario){
        modalFormUsuario.addEventListener('shown.bs.modal', function () {
            rolesUsuario();
        });
    }

    // Captura evento submit del formulario
    var tableUsuarios;
    document.addEventListener('submit', function(e) {
    
   if(e.target && e.target.id === 'formUsuario') {
            e.preventDefault();
            console.log("Formulario enviado, validando datos...");

            var strRol = $('#listRolid').val(); 
            var strIdentificacion = document.querySelector('#txtIdentificacion').value;
            var strNombre = document.querySelector('#txtNombre').value;
            var strEmail = document.querySelector('#txtEmail').value;
            var strPassword = document.querySelector('#txtPassword').value;

            if(strIdentificacion == '' || strRol == '' || strNombre == '' || strEmail == '') {
                swal("Atención", "Todos los campos son obligatorios.", "error");
                return false;
            }

            var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            var ajaxUrl = base_url + '/Usuarios/setUsuario';
            
            //formulario actual
            var formData = new FormData(e.target);

            request.open("POST", ajaxUrl, true);
            request.send(formData);

            request.onreadystatechange = function(){
                if(request.readyState == 4 && request.status == 200) {
                    //console.log("Respuesta exitosa: ", request.responseText);
                    var objData = JSON.parse(request.responseText);
                    if(objData.status)
                    {
                        $('#modalFormUsuario').modal("hide");
                        formUsuario.reset();
                        swal("Usuarios", objData.msg, "success");
                        tableUsuarios.api().ajax.reload(function(){

                        });
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
    var ajaxUrl = base_url + '/Roles/getSelectRoles';
    var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
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

    // Apertura del modal en Bootstrap 5 (Vanilla JS)
    let modalElement = document.querySelector('#modalFormUsuario');
    let modalFormRol = bootstrap.Modal.getOrCreateInstance(modalElement);
    modalFormRol.show();
}