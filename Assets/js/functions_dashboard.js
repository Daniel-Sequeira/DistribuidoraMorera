document.addEventListener('DOMContentLoaded', function () {
    const tablaAlertas = document.querySelector('#tableAlertasProductos tbody');
    if (!tablaAlertas) return;

    const INTERVALO_MS = 30000;
    let intervaloAlertas = null;
    const urlBase = base_url.replace(/\/+$/, '');

    function escaparHTML(texto) {
        if (texto === null || texto === undefined) return '';
        return String(texto)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function abrirModalLotes() {
        const modalEl = document.getElementById('modalLotesProducto');
        if (!modalEl) return;

        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        } else if (typeof $ !== 'undefined') {
            $('#modalLotesProducto').modal('show');
        }
    }

    async function cargarLotesProducto(idProducto) {
        const elCodigo = document.getElementById('loteProdCodigo');
        const elNombre = document.getElementById('loteProdNombre');
        const elStock  = document.getElementById('loteProdStock');
        const tbodyLotes = document.getElementById('tbodyDetalleLotes');

        elCodigo.textContent = '...';
        elNombre.textContent = 'Cargando información...';
        elStock.textContent  = '0';
        tbodyLotes.innerHTML = `
            <tr>
                <td colspan="5" class="text-center py-3">
                    <i class="fas fa-spinner fa-spin me-1"></i> Consultando lotes activos...
                </td>
            </tr>
        `;

        abrirModalLotes();

        try {
            const response = await fetch(`${urlBase}/dashboard/getLotesProducto/${idProducto}`, {
                method: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (!response.ok) throw new Error('Error de red al consultar lotes');

            const res = await response.json();
            if (!res.status) {
                tbodyLotes.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center text-danger">${escaparHTML(res.msg)}</td>
                    </tr>
                `;
                return;
            }

            elCodigo.textContent = res.producto.codigo;
            elNombre.textContent = res.producto.nombre;
            elStock.textContent  = res.producto.stock;

            const lotes = res.lotes || [];
            if (lotes.length === 0) {
                tbodyLotes.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center text-muted py-3">
                            Este producto no tiene lotes activos con existencias disponibles.
                        </td>
                    </tr>
                `;
                return;
            }

            let htmlLotes = '';
            lotes.forEach((lote) => {
                htmlLotes += `
                    <tr>
                        <td><strong>${escaparHTML(lote.codigo_lote)}</strong></td>
                        <td class="text-center">${escaparHTML(lote.cantidad_disponible)}</td>
                        <td class="text-center">${escaparHTML(lote.cantidad_reservada)}</td>
                        <td class="text-center">${escaparHTML(lote.fecha_vencimiento)}</td>
                        <td class="text-center">${lote.estado_badge}</td>
                    </tr>
                `;
            });

            tbodyLotes.innerHTML = htmlLotes;
        } catch (error) {
            console.error('Error al cargar lotes:', error);
            tbodyLotes.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-danger">
                        No se pudieron cargar los lotes del producto.
                    </td>
                </tr>
            `;
        }
    }

    async function actualizarTablaAlertas() {
        try {
            const response = await fetch(`${urlBase}/dashboard/getProductosAlerta`, {
                method: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (!response.ok) return;

            const resultado = await response.json();
            if (!resultado.status) return;

            const productos = resultado.data || [];

            if (productos.length === 0) {
                tablaAlertas.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center">Sin alertas de stock o caducidad.</td>
                    </tr>
                `;
                return;
            }

            let filasHTML = '';
            productos.forEach((prod) => {
                filasHTML += `
                    <tr>
                        <td>${escaparHTML(prod.id_producto)}</td>
                        <td>${escaparHTML(prod.codigo)}</td>
                        <td>${escaparHTML(prod.nombre)}</td>
                        <td>${escaparHTML(prod.proveedor)}</td>
                        <td class="text-center"><strong>${escaparHTML(prod.stock)}</strong></td>
                        <td>${prod.estado_badge}</td>
                        <td class="text-center">
                            <button type="button" 
                                class="btn btn-info btn-sm btnVerLotes" 
                                data-id="${escaparHTML(prod.id_producto)}" 
                                title="Ver lotes activos">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });

            tablaAlertas.innerHTML = filasHTML;
        } catch (error) {
            console.error('Error al actualizar alertas de productos:', error);
        }
    }

    // Delegación de eventos para el botón "Ver lotes" (persiste tras recargas AJAX)
    tablaAlertas.addEventListener('click', function (e) {
        const btn = e.target.closest('.btnVerLotes');
        if (!btn) return;

        const idProducto = btn.getAttribute('data-id');
        if (idProducto) {
            cargarLotesProducto(idProducto);
        }
    });

    function iniciarMonitoreo() {
        if (intervaloAlertas) clearInterval(intervaloAlertas);
        intervaloAlertas = setInterval(actualizarTablaAlertas, INTERVALO_MS);
    }

    iniciarMonitoreo();

    document.addEventListener('visibilitychange', function () {
        if (document.hidden) {
            clearInterval(intervaloAlertas);
        } else {
            actualizarTablaAlertas();
            iniciarMonitoreo();
        }
    });
});