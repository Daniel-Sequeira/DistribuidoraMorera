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

    document.querySelector('#divEstado').style.display = "none";
    document.querySelector('#txtNombre').removeAttribute('readonly');

    // Apertura del modal en Bootstrap 5 (Vanilla JS)
    let modalElement = document.querySelector('#modalFormUsuario');
    let modalFormRol = bootstrap.Modal.getOrCreateInstance(modalElement);
    modalFormRol.show();
}