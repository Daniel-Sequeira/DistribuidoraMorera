document.addEventListener('DOMContentLoaded', function () {
    const formRestaurar = document.getElementById('formRestaurarBD');
    if (!formRestaurar) return;

    const btnSubmit = document.getElementById('btnEjecutarRestauracion');
    const urlBase = base_url.replace(/\/+$/, '');

    function mostrarAlerta(titulo, mensaje, tipo) {
        if (typeof Swal !== 'undefined') {
            return Swal.fire({ title: titulo, text: mensaje, icon: tipo });
        } else if (typeof swal !== 'undefined') {
            return new Promise((resolve) => {
                swal({
                    title: titulo,
                    text: mensaje,
                    type: tipo,
                    confirmButtonText: 'Aceptar'
                }, function () {
                    resolve(true);
                });
            });
        } else {
            alert(`${titulo}: ${mensaje}`);
            return Promise.resolve(true);
        }
    }

    function confirmarRestauracion() {
        const texto = 'Se sobrescribirán los datos actuales de la base de datos. ¿Deseas continuar?';

        if (typeof Swal !== 'undefined') {
            return Swal.fire({
                title: '¿Confirmar restauración?',
                text: texto,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Sí, restaurar',
                cancelButtonText: 'Cancelar'
            }).then((res) => res.isConfirmed);
        } else if (typeof swal !== 'undefined') {
            return new Promise((resolve) => {
                swal({
                    title: '¿Confirmar restauración?',
                    text: texto,
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Sí, restaurar',
                    cancelButtonText: 'Cancelar',
                    closeOnConfirm: true
                }, function (isConfirm) {
                    resolve(Boolean(isConfirm));
                });
            });
        }

        return Promise.resolve(confirm(texto));
    }

    formRestaurar.addEventListener('submit', async function (e) {
        e.preventDefault();

        const inputArchivo = document.getElementById('archivo_sql');
        if (!inputArchivo.files || inputArchivo.files.length === 0) {
            mostrarAlerta('Archivo requerido', 'Selecciona un archivo .sql antes de continuar.', 'warning');
            return;
        }

        const confirmado = await confirmarRestauracion();
        if (!confirmado) return;

        const textoOriginal = btnSubmit.innerHTML;
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Restaurando...';

        try {
            const formData = new FormData(formRestaurar);
            const response = await fetch(`${urlBase}/backup/restaurar`, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            const rawText = await response.text();
            let res;
            try {
                res = JSON.parse(rawText);
            } catch (parseErr) {
                console.error('Respuesta no JSON del servidor:', rawText);
                mostrarAlerta('Error del servidor', 'El servidor devolvió una respuesta inesperada. Revisa la consola (F12).', 'error');
                return;
            }

            if (res.status) {
                formRestaurar.reset();
                await mostrarAlerta('¡Restauración exitosa!', res.msg, 'success');
                window.location.reload();
            } else {
                mostrarAlerta('Error al restaurar', res.msg, 'error');
            }
        } catch (error) {
            console.error('Error en restauración:', error);
            mostrarAlerta('Error de conexión', 'No se pudo completar la petición al servidor.', 'error');
        } finally {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = textoOriginal;
        }
    });
});