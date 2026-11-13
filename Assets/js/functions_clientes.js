document.addEventListener('DOMContentLoaded', function(){
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
                        //tableClientes.ajax.reload();
                    }else{
                        swal("Error",objData.msg, "error");
                    }
                }
            }
        }
    });
}, false); 


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
