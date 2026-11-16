let tableEstanterias;
document.addEventListener('DOMContentLoaded',function(){
tableEstanterias = $('#tableEstanterias').DataTable({
        "aProcessing":true,
        "aServerSide":false,
        "language":{
            "url": "https://cdn.datatables.net/plug-ins/1.10.20//i18n/Spanish.json"
        },
        "ajax":{
            "url":""+base_url+"Estanterias/getEstanterias",
            "dataSrc":""
        },
        "columns":[
            {"data":"id_estanteria"},
            {"data":"codigo_estanteria"},
            {"data":"descripcion"},
            {"data":"estado"},
            {"data":"opciones"}
        ],
       
        "responsive": true,
        "bDestroy": true,
        "iDisplayLength":5,
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
     // Filtro por Estado (Columna 4)
    document.addEventListener('change', function (e) {
        if (e.target && e.target.id === 'filtroEstado') {
            let valor = e.target.value;
            // Busca coincidencia exacta ("Activo" o "Inactivo") ignorando las etiquetas HTML 
            tableEstanterias.column(3).search(valor ? '^' + valor + '$' : '', true, false).draw();
        }
    });



    if(document.querySelector("#foto")){
    var foto = document.querySelector("#foto");
    foto.onchange = function(e) {
        var uploadFoto = document.querySelector("#foto").value;
        var fileimg = document.querySelector("#foto").files;
        var nav = window.URL || window.webkitURL;
        var contactAlert = document.querySelector('#form_alert');
        if(uploadFoto !=''){
            var type = fileimg[0].type;
            var name = fileimg[0].name;
            if(type != 'image/jpeg' && type != 'image/jpg' && type != 'image/png'){
                contactAlert.innerHTML = '<p class="errorArchivo">El archivo no es válido.</p>';
                if(document.querySelector('#img')){
                    document.querySelector('#img').remove();
                }
                document.querySelector('.delPhoto').classList.add("notBlock");
                foto.value="";
                return false;
            }else{  
                    contactAlert.innerHTML='';
                    if(document.querySelector('#img')){
                        document.querySelector('#img').remove();
                    }
                    document.querySelector('.delPhoto').classList.remove("notBlock");
                    var objeto_url = nav.createObjectURL(this.files[0]);
                    document.querySelector('.prevPhoto div').innerHTML = "<img id='img' src="+objeto_url+">";
                }
        }else{
            alert("No selecciono foto");
            if(document.querySelector('#img')){
                document.querySelector('#img').remove();
            }
        }
    }
}

if(document.querySelector(".delPhoto")){
    var delPhoto = document.querySelector(".delPhoto");
    delPhoto.onclick = function(e) {
        document.querySelector("#foto_remove").value = 1;
        removePhoto();
    }
}
  //Nueva Estanteria
    var formEstanteria = document.querySelector("#formEstanteria");
    formEstanteria.onsubmit = function(e){
        e.preventDefault();
        var intIdEstanteria = document.querySelector('#id_estanteria').value;
        var strCodigoEstanteria = document.querySelector('#txtCodigo_estanteria').value;
        var strDescripcion = document.querySelector('#txtDescripcion').value;
        var intStatus = document.querySelector('#listStatus').value;
        if(strCodigoEstanteria == ''|| strDescripcion == ''|| intStatus == '')
        {
            swal("Atención", "La nueva estantería debe tener un nombre", "error");
            return false;
        }
        var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
        var ajaxUrl = base_url + 'Estanterias/setEstanteria';
        var formData = new FormData(formEstanteria);
        request.open("POST",ajaxUrl,true);
        request.send(formData);
        request.onreadystatechange = function(){
            if(request.readyState == 4 && request.status == 200) {
                var objData = JSON.parse(request.responseText); 

               if(objData.status)
            {
                $('#modalFormEstanterias').modal('hide');
                formEstanteria.reset();
                swal("Estanterías", objData.msg, "success");
                removePhoto();
                tableEstanterias.api().ajax.reload();
                }else{
                    swal("Error", objData.msg, "error");
                }
            }
            return false;
        }
    }

},false);

//Ver Una estanteria
document.addEventListener('click', function(e) {
    //btnViewEstanteria
    let btnView = e.target.closest('.btnViewEstanteria');
    if(btnView) {
        e.preventDefault(); 
        
        let id_estanteria = btnView.getAttribute("cl");
        let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
        let ajaxUrl = base_url + 'Estanterias/getEstanteria/' + id_estanteria;
        
        request.open("GET", ajaxUrl, true);
        request.send();

        request.onreadystatechange = function() {
            if (request.readyState == 4 && request.status == 200) {
                let objData = JSON.parse(request.responseText);
                
                if(objData.status) {
                    document.querySelector("#celIdEstanteria").innerHTML = objData.data.id_estanteria;
                    document.querySelector("#celCodigo_estanteria").innerHTML = objData.data.codigo_estanteria;
                    document.querySelector("#celDescripcion").innerHTML = objData.data.descripcion;
                    document.querySelector("#imgEstanteria").src = objData.data.url_portada;
                    // Convertir el estado a texto visual
                    let estadoEstanteria = (objData.data.estado == true || objData.data.estado == 1) ? 
                                        '<span class="badge bg-success">Activo</span>' : 
                                        '<span class="badge bg-danger">Inactivo</span>';
                    document.querySelector("#celEstado").innerHTML = estadoEstanteria;
                    //Mostrar el modal en pantalla (Bootstrap 5)
                    let modalElement = document.querySelector('#modalViewEstanteria');
                    let modalView = bootstrap.Modal.getOrCreateInstance(modalElement);
                    modalView.show();
                } else {
                    swal("Error", objData.msg, "error");
                }
            }
        }
    }
});

//Editar Una estanteria
document.addEventListener('click', function(e) {
    //btnEditEstanteria
    let btnView = e.target.closest('.btnEditEstanteria');
    if(btnView) {
        e.preventDefault(); 
         //Cambiar apariencia del modal a "Actualizar"
        document.querySelector('#titleModal').innerHTML = "Actualizar Estantería";
        document.querySelector('#btnText').innerHTML = "Actualizar";
        document.querySelector('.modal-header').classList.replace('headerRegister', 'headerUpdate');
        document.querySelector('#btnActionForm').classList.replace('btn-primary', 'btn-info');
        
        let id_estanteria = btnView.getAttribute("cl");
        let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
        let ajaxUrl = base_url + 'Estanterias/getEstanteria/' + id_estanteria;
        request.open("GET", ajaxUrl, true);
        request.send();

        request.onreadystatechange = function() {
            if (request.readyState == 4 && request.status == 200) {
                let objData = JSON.parse(request.responseText);
                
                if(objData.status) {
                    document.querySelector("#id_estanteria").value = objData.data.id_estanteria;
                    document.querySelector('#foto_remove').value = 0;

                    document.querySelector("#txtCodigo_estanteria").value = objData.data.codigo_estanteria;
                    document.querySelector("#txtDescripcion").value = objData.data.descripcion;
                    document.querySelector("#foto_actual").value = objData.data.imagen;

                    if(objData.data.status == 1){
                        document.querySelector("#listStatus").value = 1;
                    }else{
                        document.querySelector("#listStatus").value = 0;
                    }
                    //Si existe la imagen la asigna y si no lo crea y asigna
                    if(document.querySelector('#img')){
                        document.querySelector('#img').src= objData.data.url_portada;
                    }else{
                        document.querySelector('.prevPhoto div').innerHTML = "<img id='img' src='"+ objData.data.url_portada+"'>";
                    }
                    //Si es imagen por defecto delPhoto no se muestra, de lo contrario permite eliminar foto.
                    if(objData.data.portada == 'portada_estanteria.png'){
                         document.querySelector('.delPhoto').classList.add("notBlock");
                    }else{
                        document.querySelector('.delPhoto').classList.remove("notBlock");
                    }
                    $('#modalFormEstanterias').modal('show');
                } else {
                    swal("Error", objData.msg, "error");
                }
            }
        }
    }
});



function removePhoto(){
    document.querySelector('#foto').value ="";
    document.querySelector('.delPhoto').classList.add("notBlock");
    if(document.querySelector('#img')){
      document.querySelector('#img').remove();
    }
    
}


initValidations();
function openModal() { 
    document.querySelector('#id_estanteria').value = ""; 
    document.querySelector('#foto_actual').value = "";
    document.querySelector('#foto_remove').value = 0;
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
    document.querySelector('#titleModal').innerHTML = "Nuevo Estante";
    document.querySelector("#formEstanteria").reset(); 

    document.querySelector('#txtCodigo_estanteria').removeAttribute('readonly');

    // Apertura del modal en Bootstrap 5
    let modalElement = document.querySelector('#modalFormEstanterias');
    let modalFormEstanteria = bootstrap.Modal.getOrCreateInstance(modalElement);
    modalFormEstanteria.show();
    removePhoto();
}
