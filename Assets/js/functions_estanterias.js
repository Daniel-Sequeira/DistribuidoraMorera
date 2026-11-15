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
                // tableEstanterias.api().ajax.reload();
                }else{
                    swal("Error", objData.msg, "error");
                }
            }
            return false;
        }
    }

},false);

function removePhoto(){
    document.querySelector('#foto').value ="";
    document.querySelector('.delPhoto').classList.add("notBlock");
    document.querySelector('#img').remove();
}


initValidations();
function openModal() { 
    document.querySelector('#id_estanteria').value = ""; 

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
}
