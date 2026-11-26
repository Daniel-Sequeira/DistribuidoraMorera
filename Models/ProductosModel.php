<?php
class ProductosModel extends Pgsql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function selectProductos()
    {
        $sql = "SELECT 
                    p.id_producto,
                    p.codigo,
                    p.nombre,
                    pv.nombre_razon_social AS proveedor,
                    e.codigo_estanteria AS estanteria,
                    p.stock,
                    p.precio,
                    p.estado
                FROM productos p
                INNER JOIN proveedores pv ON p.id_proveedor = pv.id_proveedor
                INNER JOIN estanterias e ON p.id_estanteria = e.id_estanteria
                ORDER BY p.id_producto ASC";
        return $this->select_all($sql);
    }

    public function selectProducto(int $idproducto)
    {
        $sql = "SELECT 
                    p.id_producto,
                    p.id_estanteria,
                    p.id_proveedor,
                    p.codigo,
                    p.nombre,
                    p.descripcion,
                    p.precio,
                    p.stock,
                    TO_CHAR(p.creado_en, 'DD/MM/YYYY HH24:MI') AS creado_en,
                    p.estado,
                    pv.nombre_razon_social AS proveedor,
                    CONCAT(e.codigo_estanteria, ' - ', e.descripcion) AS estanteria
                FROM productos p
                INNER JOIN proveedores pv ON p.id_proveedor = pv.id_proveedor
                INNER JOIN estanterias e ON p.id_estanteria = e.id_estanteria
                WHERE p.id_producto = $idproducto";
        return $this->select($sql);
    }

    public function selectLotesActivos(int $idproducto)
    {
        $sql = "SELECT 
                    id_lote,
                    codigo_lote,
                    cantidad_inicial,
                    cantidad_disponible,
                    costo_unitario,
                    TO_CHAR(fecha_ingreso, 'DD/MM/YYYY HH24:MI') AS fecha_ingreso,
                    TO_CHAR(fecha_vencimiento, 'YYYY-MM-DD') AS fecha_vencimiento
                FROM producto_lotes
                WHERE id_producto = $idproducto AND cantidad_disponible > 0
                ORDER BY fecha_vencimiento ASC NULLS LAST, fecha_ingreso ASC, id_lote ASC";
        return $this->select_all($sql);
    }

    public function insertProductoFIFO(
        int $idEstanteria,
        int $idProveedor,
        string $codigo,
        string $nombre,
        string $descripcion,
        float $precio,
        int $stock,
        int $estado,
        string $codigoLote,
        float $costoUnitario,
        int $idUsuario,
        ?string $fechaVencimiento = null
    ) {
        $codigo = trim($codigo);
        $codigoLote = trim($codigoLote);

        $sqlCheck = "SELECT id_producto FROM productos WHERE codigo = ?";
        $requestCheck = $this->select($sqlCheck, [$codigo]);
        if (!empty($requestCheck)) {
            return 'exist';
        }

        try {
            $this->beginTransaction();

            // 1. Insertar producto maestro
            $queryProd = "INSERT INTO productos (id_estanteria, id_proveedor, codigo, nombre, descripcion, precio, stock, estado)
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $arrProd = [$idEstanteria, $idProveedor, $codigo, $nombre, $descripcion, $precio, $stock, $estado];
            $idProducto = $this->insert($queryProd, $arrProd);

            if ($idProducto <= 0) {
                $this->rollBack();
                return 0;
            }

            // 2. Insertar primer lote en cola FIFO
            $queryLote = "INSERT INTO producto_lotes (id_producto, codigo_lote, cantidad_inicial, cantidad_disponible, costo_unitario, precio_venta_sugerido, fecha_vencimiento, estado)
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $arrLote = [
                $idProducto,
                $codigoLote,
                $stock,
                $stock,
                $costoUnitario,
                $precio,
                $fechaVencimiento,
                $estado
            ];
            $idLote = $this->insert($queryLote, $arrLote);

            if ($idLote <= 0) {
                $this->rollBack();
                return 0;
            }

            // 3. Registrar movimiento inicial de ENTRADA
            $queryMov = "INSERT INTO movimientos_inventario (id_producto, id_lote, id_usuario, id_cliente, tipo_movimiento, cantidad, costo_unitario, precio_unitario, motivo)
                         VALUES (?, ?, ?, NULL, 'ENTRADA', ?, ?, ?, ?)";
            $arrMov = [$idProducto, $idLote, $idUsuario, $stock, $costoUnitario, $precio, 'Inventario inicial'];
            $this->insert($queryMov, $arrMov);

            $this->commit();
            return $idProducto;

        } catch (Throwable $e) {
            $this->rollBack();
            throw $e;
        }
    }


    public function updateProducto(
        int $idProducto,
        int $idEstanteria,
        int $idProveedor,
        string $codigo,
        string $nombre,
        string $descripcion,
        float $precio,
        int $estado
    ) {
        $sqlCheck = "SELECT id_producto FROM productos WHERE codigo = '$codigo' AND id_producto != $idProducto";
        $requestCheck = $this->select_all($sqlCheck);
        if (!empty($requestCheck)) {
            return 'exist';
        }

        $sql = "UPDATE productos 
                SET id_estanteria = ?, id_proveedor = ?, codigo = ?, nombre = ?, descripcion = ?, precio = ?, estado = ?
                WHERE id_producto = $idProducto";
        $arrData = array($idEstanteria, $idProveedor, $codigo, $nombre, $descripcion, $precio, $estado);
        return $this->update($sql, $arrData);
    }

    public function selectProveedoresActivos()
    {
        // estado::text IN ('1','true','t') evita errores en PostgreSQL tanto si la columna es INT como BOOLEAN
        $sql = "SELECT id_proveedor, nombre_razon_social FROM proveedores WHERE estado::text IN ('1', 'true', 't') ORDER BY nombre_razon_social ASC";
        return $this->select_all($sql);
    }

    public function selectEstanteriasActivas()
    {
        $sql = "SELECT id_estanteria, codigo_estanteria, descripcion 
                FROM estanterias 
                WHERE estado::text IN ('1', 'true', 't', 'Activo') 
                ORDER BY codigo_estanteria ASC";
        return $this->select_all($sql);
    }
}
?>