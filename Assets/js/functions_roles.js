var tableRoles;
document.addEventListener('DOMContentLoaded', function(){
    tableRoles = $('#tableRoles').dataTable({
        "aProcessing":true,
        "aServerSide":true,
        "language":{
            "url": "//cdn.datatables.net/plug-ins/1.10.20//i18n/Spanish.json"
        },
        "ajax":{
            "url":""+base_url+"/Roles/getRoles",
            "dataSrc":""
        },
        "columns":[
            {"data":"id_rol"},
            {"data":"nombre_rol"}
        ],
        "responsive": true,
        "bDestroy": true,
        "iDisplayLength":5,
        "order":[[0,"asc"]]
    });
});
$('#tableRoles').DataTable();

function openModal(){
    $('#modalFormRol').modal('show');
}