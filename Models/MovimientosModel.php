<?php
class MovimientosModel extends Pgsql
{
    private $intIdMovimiento;
    private $intIdProducto;
    private $intIdLote;
    private $strTipoMovimiento;
    private $intCantidad;
    private $decCostoUnitario;
    private $decPrecioUnitario;
    private $strMotivo;
    private $intIdUsuario;
    private $intIdCliente;

    public function __construct()
    {
        parent::__construct();
    }

    
     //Listar todos los movimientos para el DataTable (incluye estado e id_cliente)
     
    public function selectMovimientos()
    {
        $sql = "SELECT 
                    m.id_movimiento,
                    TO_CHAR(m.fecha_movimiento, 'DD/MM/YYYY HH24:MI') AS fecha_movimiento,
                    m.tipo_movimiento,
                    p.nombre AS producto,
                    l.codigo_lote AS lote,
                    m.cantidad,
                    m.costo_unitario,
                    m.precio_unitario,
                    m.estado,
                    m.id_cliente,
                    COALESCE(c.nombre_razon_social, 'N/A (Ingreso)') AS cliente,
                    u.nombre_completo AS usuario,
                    m.motivo
                FROM movimientos_inventario m
                INNER JOIN productos p ON m.id_producto = p.id_producto
                INNER JOIN producto_lotes l ON m.id_lote = l.id_lote
                INNER JOIN usuarios u ON m.id_usuario = u.id_usuario
                LEFT JOIN clientes c ON m.id_cliente = c.id_cliente
                ORDER BY m.id_movimiento DESC";
        return $this->select_all($sql);
    }

    
     //Obtener detalle de un movimiento específico (Modal Ver)
     
    public function selectMovimiento(int $idMovimiento)
    {
        $this->intIdMovimiento = $idMovimiento;
        $sql = "SELECT 
                    m.id_movimiento,
                    TO_CHAR(m.fecha_movimiento, 'DD/MM/YYYY HH24:MI:SS') AS fecha_movimiento,
                    m.tipo_movimiento,
                    p.codigo AS codigo_producto,
                    p.nombre AS producto,
                    l.codigo_lote AS lote,
                    TO_CHAR(l.fecha_ingreso, 'DD/MM/YYYY') AS fecha_ingreso_lote,
                    m.cantidad,
                    m.costo_unitario,
                    m.precio_unitario,
                    m.estado,
                    COALESCE(c.nombre_razon_social, 'No aplica (Entrada de inventario)') AS cliente,
                    u.nombre_completo AS usuario,
                    COALESCE(m.motivo, 'Sin observaciones') AS motivo
                FROM movimientos_inventario m
                INNER JOIN productos p ON m.id_producto = p.id_producto
                INNER JOIN producto_lotes l ON m.id_lote = l.id_lote
                INNER JOIN usuarios u ON m.id_usuario = u.id_usuario
                LEFT JOIN clientes c ON m.id_cliente = c.id_cliente
                WHERE m.id_movimiento = $this->intIdMovimiento";
        return $this->select($sql);
    }

    
      //Registrar ENTRADA directa (Estado 0: Completado)
     
    public function insertMovimientoEntrada(
        int $idProducto,
        int $idLote,
        int $cantidad,
        float $costoUnitario,
        string $motivo,
        int $idUsuario
    ) {
        $this->intIdProducto = $idProducto;
        $this->intIdLote = $idLote;
        $this->intCantidad = $cantidad;
        $this->decCostoUnitario = $costoUnitario;
        $this->strMotivo = $motivo;
        $this->intIdUsuario = $idUsuario;

        $sqlLote = "SELECT id_lote FROM producto_lotes 
                    WHERE id_lote = $this->intIdLote AND id_producto = $this->intIdProducto AND estado = 1";
        $loteData = $this->select($sqlLote);

        if (empty($loteData)) {
            return "lote_invalido";
        }

        try {
            $this->beginTransaction();

            $queryInsert = "INSERT INTO movimientos_inventario (
                                id_producto, id_lote, tipo_movimiento, cantidad, 
                                costo_unitario, precio_unitario, motivo, id_usuario, id_cliente, estado
                            ) VALUES (?, ?, 'ENTRADA', ?, ?, 0.00, ?, ?, NULL, 0) RETURNING id_movimiento";

            $arrData = array(
                $this->intIdProducto,
                $this->intIdLote,
                $this->intCantidad,
                $this->decCostoUnitario,
                $this->strMotivo,
                $this->intIdUsuario
            );

            $requestInsert = $this->insert($queryInsert, $arrData);

            // Sumar directo a cantidad_disponible en producto_lotes
            $sqlUpdateLote = "UPDATE producto_lotes 
                              SET cantidad_disponible = cantidad_disponible + ? 
                              WHERE id_lote = ?";
            $this->update($sqlUpdateLote, array($this->intCantidad, $this->intIdLote));

            $this->commit();
            return $requestInsert;
        } catch (Exception $e) {
            $this->rollBack();
            return 0;
        }
    }

    
     //Agregar artículo a lista de Ventas (Estado 1: Recibido por ventas) y CONGELAR cantidad
     
    public function insertItemSalidaVentas(
        int $idProducto,
        int $idLote,
        int $cantidad,
        float $precioUnitario,
        string $motivo,
        int $idUsuario,
        int $idCliente
    ) {
        $this->intIdProducto = $idProducto;
        $this->intIdLote = $idLote;
        $this->intCantidad = $cantidad;
        $this->decPrecioUnitario = $precioUnitario;
        $this->strMotivo = $motivo;
        $this->intIdUsuario = $idUsuario;
        $this->intIdCliente = $idCliente;

        // Validar existencia libre real (disponible - reservada)
        $sqlLote = "SELECT (cantidad_disponible - cantidad_reservada) AS libre 
                    FROM producto_lotes 
                    WHERE id_lote = $this->intIdLote AND id_producto = $this->intIdProducto AND estado = 1";
        $loteData = $this->select($sqlLote);

        if (empty($loteData)) {
            return "lote_invalido";
        }
        if (intval($loteData['libre']) < $this->intCantidad) {
            return "stock_insuficiente";
        }

        try {
            $this->beginTransaction();

            // Insertar en estado 1 (Recibido por ventas)
            $queryInsert = "INSERT INTO movimientos_inventario (
                                id_producto, id_lote, tipo_movimiento, cantidad, 
                                costo_unitario, precio_unitario, motivo, id_usuario, id_cliente, estado
                            ) VALUES (?, ?, 'SALIDA', ?, 0.00, ?, ?, ?, ?, 1) RETURNING id_movimiento";

            $arrData = array(
                $this->intIdProducto,
                $this->intIdLote,
                $this->intCantidad,
                $this->decPrecioUnitario,
                $this->strMotivo,
                $this->intIdUsuario,
                $this->intIdCliente
            );

            $requestInsert = $this->insert($queryInsert, $arrData);

            // Congelar cantidad en producto_lotes para evitar sobreventa
            $sqlFreeze = "UPDATE producto_lotes 
                          SET cantidad_reservada = cantidad_reservada + ? 
                          WHERE id_lote = ?";
            $this->update($sqlFreeze, array($this->intCantidad, $this->intIdLote));

            $this->commit();
            return $requestInsert;
        } catch (Exception $e) {
            $this->rollBack();
            return 0;
        }
    }

    
    //Listar artículos pendientes (Estado 1) de un cliente
     
    public function selectListaVentasCliente(int $idCliente)
    {
        $this->intIdCliente = $idCliente;
        $sql = "SELECT 
                    m.id_movimiento,
                    m.id_lote,
                    p.codigo,
                    p.nombre AS producto,
                    l.codigo_lote AS lote,
                    m.cantidad,
                    m.precio_unitario,
                    (m.cantidad * m.precio_unitario) AS subtotal,
                    (l.cantidad_disponible - l.cantidad_reservada + m.cantidad) AS max_permitido
                FROM movimientos_inventario m
                INNER JOIN productos p ON m.id_producto = p.id_producto
                INNER JOIN producto_lotes l ON m.id_lote = l.id_lote
                WHERE m.id_cliente = $this->intIdCliente 
                  AND m.tipo_movimiento = 'SALIDA' 
                  AND m.estado = 1
                ORDER BY m.id_movimiento ASC";
        return $this->select_all($sql);
    }

    
     //Editar cantidad de un ítem en Estado 1 (Recalcula cantidad_reservada)
     
    public function updateCantidadSalidaVentas(int $idMovimiento, int $nuevaCantidad)
    {
        if ($nuevaCantidad <= 0) {
            return "cantidad_invalida";
        }

        $this->intIdMovimiento = $idMovimiento;
        $sql = "SELECT m.cantidad, m.id_lote, l.cantidad_disponible, l.cantidad_reservada 
                FROM movimientos_inventario m
                INNER JOIN producto_lotes l ON m.id_lote = l.id_lote
                WHERE m.id_movimiento = $this->intIdMovimiento AND m.estado = 1";
        $mov = $this->select($sql);

        if (empty($mov)) {
            return "no_editable";
        }

        $cantidadAnterior = intval($mov['cantidad']);
        $diferencia = $nuevaCantidad - $cantidadAnterior;
        $libreReal = intval($mov['cantidad_disponible']) - intval($mov['cantidad_reservada']);

        if ($diferencia > $libreReal) {
            return "stock_insuficiente";
        }

        try {
            $this->beginTransaction();

            $this->update("UPDATE movimientos_inventario SET cantidad = ? WHERE id_movimiento = ?", array($nuevaCantidad, $this->intIdMovimiento));
            $this->update("UPDATE producto_lotes SET cantidad_reservada = cantidad_reservada + ? WHERE id_lote = ?", array($diferencia, $mov['id_lote']));

            $this->commit();
            return "ok";
        } catch (Exception $e) {
            $this->rollBack();
            return "error";
        }
    }

    
      //Remover ítem de la lista de Ventas en Estado 1 (Libera cantidad_reservada)
    public function deleteItemSalidaVentas(int $idMovimiento)
    {
        $this->intIdMovimiento = $idMovimiento;
        $sql = "SELECT cantidad, id_lote 
                FROM movimientos_inventario 
                WHERE id_movimiento = $this->intIdMovimiento AND estado = 1";
        $mov = $this->select($sql);

        if (empty($mov)) {
            return false;
        }

        try {
            $this->beginTransaction();

            $this->update("UPDATE producto_lotes SET cantidad_reservada = cantidad_reservada - ? WHERE id_lote = ?", array($mov['cantidad'], $mov['id_lote']));
            $this->delete("DELETE FROM movimientos_inventario WHERE id_movimiento = $this->intIdMovimiento");

            $this->commit();
            return true;
        } catch (Exception $e) {
            $this->rollBack();
            return false;
        }
    }

    
     //Procesar lista de Ventas: Estado 1 -> Estado 2 (En Proceso para Bodega) 
    public function procesarPedidoVentas(int $idCliente)
    {
        $this->intIdCliente = $idCliente;
        $sql = "UPDATE movimientos_inventario 
                SET estado = 2 
                WHERE id_cliente = ? AND tipo_movimiento = 'SALIDA' AND estado = 1";
        return $this->update($sql, array($this->intIdCliente));
    }

   
    //Avanzar estados de Bodega / Entrega (2 -> 3 Enviado | 3 -> 4 Entregado)
     
    public function avanzarEstadoMovimiento(int $idMovimiento, int $nuevoEstado)
    {
        $this->intIdMovimiento = $idMovimiento;
        $sql = "SELECT id_movimiento, tipo_movimiento, id_producto, id_lote, cantidad, estado 
                FROM movimientos_inventario 
                WHERE id_movimiento = $this->intIdMovimiento";
        $mov = $this->select($sql);

        if (empty($mov)) {
            return false;
        }

        $estadoActual = intval($mov['estado']);
        $tipoMov      = trim($mov['tipo_movimiento']);

        try {
            $this->beginTransaction();

            // Estado 2 (En Proceso) -> Estado 3 (Enviado): Descuenta disponible y libera reserva
            if ($tipoMov === 'SALIDA' && $estadoActual === 2 && $nuevoEstado === 3) {
                $this->update("UPDATE movimientos_inventario SET estado = 3 WHERE id_movimiento = ?", array($this->intIdMovimiento));
                $this->update(
                    "UPDATE producto_lotes 
                     SET cantidad_disponible = cantidad_disponible - ?, 
                         cantidad_reservada = cantidad_reservada - ? 
                     WHERE id_lote = ?",
                    array($mov['cantidad'], $mov['cantidad'], $mov['id_lote'])
                );
            }
            // Estado 3 (Enviado) -> Estado 4 (Entregado)
            elseif ($tipoMov === 'SALIDA' && $estadoActual === 3 && $nuevoEstado === 4) {
                $this->update("UPDATE movimientos_inventario SET estado = 4 WHERE id_movimiento = ?", array($this->intIdMovimiento));
            }
            // Estado 7 (Pendiente Devuelto) -> Estado 8 (Devuelto): Solo actualiza el estado, NO suma al stock
            elseif ($tipoMov === 'ENTRADA' && $estadoActual === 7 && $nuevoEstado === 8) {
                $this->update("UPDATE movimientos_inventario SET estado = 8 WHERE id_movimiento = ?", array($this->intIdMovimiento));
            } else {
                $this->rollBack();
                return false;
            }

            $this->commit();
            return true;
        } catch (Exception $e) {
            $this->rollBack();
            return false;
        }
    }

    
     //Registrar Devolución Parcial (Estado 5) o Total (Estado 6) desde Entregado (Estado 4)
     
    public function registrarDevolucion(int $idMovimiento, int $cantDevolver, string $motivoDevolucion, int $idUsuario)
    {
        $this->intIdMovimiento = $idMovimiento;
        $sql = "SELECT id_movimiento, id_producto, id_lote, cantidad, precio_unitario, id_cliente, estado, motivo 
                FROM movimientos_inventario 
                WHERE id_movimiento = $this->intIdMovimiento AND tipo_movimiento = 'SALIDA' AND estado = 4";
        $mov = $this->select($sql);

        if (empty($mov)) {
            return "estado_invalido";
        }

        $cantOriginal = intval($mov['cantidad']);
        if ($cantDevolver <= 0 || $cantDevolver > $cantOriginal) {
            return "cantidad_invalida";
        }

        $nuevoEstadoSalida = ($cantDevolver === $cantOriginal) ? 6 : 5; 
        $notaMotivo = substr(trim(($mov['motivo'] ?? '') . " | Dev. ($cantDevolver unds): $motivoDevolucion"), 0, 100);

        try {
            $this->beginTransaction();

            //Marcar la salida original como Devolución Parcial (5) o Total (6)
            $this->update(
                "UPDATE movimientos_inventario SET estado = ?, motivo = ? WHERE id_movimiento = ?",
                array($nuevoEstadoSalida, $notaMotivo, $this->intIdMovimiento)
            );

            //Insertar movimiento de devolución en estado 7 (Pendiente Devuelto)
            $motivoEntrada = substr("Devolución Salida #$this->intIdMovimiento: $motivoDevolucion", 0, 100);
            $idDevolucion = $this->insert(
                "INSERT INTO movimientos_inventario (
                    id_producto, id_lote, tipo_movimiento, cantidad, costo_unitario, precio_unitario, motivo, id_usuario, id_cliente, estado
                 ) VALUES (?, ?, 'ENTRADA', ?, 0.00, 0.00, ?, ?, ?, 7) RETURNING id_movimiento",
                array($mov['id_producto'], $mov['id_lote'], $cantDevolver, $motivoEntrada, $idUsuario, $mov['id_cliente'])
            );

            $this->commit();
            return [
                'status'        => 'ok',
                'id_devolucion' => $idDevolucion
            ];
        } catch (Exception $e) {
            $this->rollBack();
            return "error";
        }
    }

    //Info para reporte devolucion
    public function selectReporteDevolucion(int $idMovimiento)
    {
        $this->intIdMovimiento = $idMovimiento;
        $sqlBase = "SELECT id_movimiento, tipo_movimiento, motivo FROM movimientos_inventario WHERE id_movimiento = $this->intIdMovimiento";
        $movBase = $this->select($sqlBase);

        if (empty($movBase)) {
            return [];
        }

        $idEntradaDev = 0;
        $idSalidaOrig = 0;

        if (trim($movBase['tipo_movimiento']) === 'ENTRADA') {
            $idEntradaDev = intval($movBase['id_movimiento']);
            if (preg_match('/Devolución Salida #(\d+):/', $movBase['motivo'] ?? '', $matches)) {
                $idSalidaOrig = intval($matches[1]);
            }
        } else {
            $idSalidaOrig = intval($movBase['id_movimiento']);
            $sqlFindEnt = "SELECT id_movimiento FROM movimientos_inventario 
                           WHERE tipo_movimiento = 'ENTRADA' 
                             AND motivo LIKE 'Devolución Salida #$idSalidaOrig:%' 
                           ORDER BY id_movimiento DESC LIMIT 1";
            $entRow = $this->select($sqlFindEnt);
            $idEntradaDev = intval($entRow['id_movimiento'] ?? 0);
        }

        if ($idEntradaDev <= 0) {
            return [];
        }

        $sql = "SELECT 
                    dev.id_movimiento AS id_devolucion,
                    TO_CHAR(dev.fecha_movimiento, 'DD/MM/YYYY HH24:MI:SS') AS fecha_devolucion,
                    dev.cantidad AS cantidad_devuelta,
                    dev.estado AS estado_devolucion,
                    dev.motivo AS motivo_raw,
                    u_dev.nombre_completo AS usuario_devolucion,
                    p.codigo AS codigo_producto,
                    p.nombre AS producto,
                    l.codigo_lote AS lote,
                    COALESCE(c.identificacion, 'N/A') AS identificacion_cliente,
                    COALESCE(c.nombre_razon_social, 'Cliente no especificado') AS cliente,
                    sal.id_movimiento AS id_salida_origen,
                    TO_CHAR(sal.fecha_movimiento, 'DD/MM/YYYY HH24:MI') AS fecha_salida,
                    COALESCE(sal.cantidad, dev.cantidad) AS cantidad_original,
                    (COALESCE(sal.cantidad, dev.cantidad) - dev.cantidad) AS cantidad_conservada,
                    COALESCE(sal.precio_unitario, 0.00) AS precio_unitario,
                    (dev.cantidad * COALESCE(sal.precio_unitario, 0.00)) AS monto_devuelto,
                    COALESCE(sal.estado, 6) AS estado_salida
                FROM movimientos_inventario dev
                INNER JOIN productos p ON dev.id_producto = p.id_producto
                INNER JOIN producto_lotes l ON dev.id_lote = l.id_lote
                INNER JOIN usuarios u_dev ON dev.id_usuario = u_dev.id_usuario
                LEFT JOIN clientes c ON dev.id_cliente = c.id_cliente
                LEFT JOIN movimientos_inventario sal ON sal.id_movimiento = $idSalidaOrig
                WHERE dev.id_movimiento = $idEntradaDev";

        $data = $this->select($sql);
        if (!empty($data)) {
            $data['motivo_devolucion'] = preg_replace('/^Devolución Salida #\d+:\s*/', '', $data['motivo_raw'] ?? '');
        }
        return $data;
    }

    
      //Métodos auxiliares para llenar los selects del modal
    public function selectProductosActivos()
    {
        $sql = "SELECT id_producto, codigo, nombre 
                FROM productos 
                WHERE estado::text IN ('1', 'true', 't') 
                ORDER BY nombre ASC";
        return $this->select_all($sql);
    }

    public function selectLotesPorProducto(int $idProducto)
    {
        $this->intIdProducto = $idProducto;
        $sql = "SELECT 
                    id_lote, 
                    codigo_lote, 
                    cantidad_disponible,
                    cantidad_reservada,
                    (cantidad_disponible - cantidad_reservada) AS stock_libre,
                    costo_unitario,
                    precio_venta_sugerido,
                    TO_CHAR(fecha_ingreso, 'DD/MM/YYYY') AS fecha_ingreso
                FROM producto_lotes 
                WHERE id_producto = $this->intIdProducto AND estado = 1 
                ORDER BY fecha_ingreso ASC";
        return $this->select_all($sql);
    }

    public function selectClientesActivos()
    {
        $sql = "SELECT id_cliente, identificacion, nombre_razon_social 
                FROM clientes 
                WHERE estado::text IN ('1', 'true', 't') 
                ORDER BY nombre_razon_social ASC";
        return $this->select_all($sql);
    }









    
}
?>