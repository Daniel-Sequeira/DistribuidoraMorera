<?php
require_once(__DIR__ . "/../Libraries/Core/Pgsql.php");
class DashboardModel extends Pgsql {

    public function __construct(){
       parent::__construct();
    }

    public function cantUsuarios(){
        $sql ="SELECT COUNT(*) AS total FROM usuarios WHERE estado IS TRUE";
        $request = $this->select($sql);
        $total = $request['total'];
        return $total;
    }

    public function cantClientes(){
        $sql ="SELECT COUNT(*) AS total FROM clientes WHERE estado IS TRUE";
        $request = $this->select($sql);
        $total = $request['total'];
        return $total;
    }

     public function cantProductos(){
        $sql ="SELECT COUNT(*) AS total FROM productos WHERE estado = 1;";
        $request = $this->select($sql);
        $total = $request['total'];
        return $total;
    }

      public function cantMovimientosPend(){
        $sql ="SELECT COUNT(*) AS total FROM movimientos_inventario WHERE estado = 2;";
        $request = $this->select($sql);
        $total = $request['total'];
        return $total;
    }

  public function ultimosMovimientos()
{
    $sql = "SELECT 
                m.id_movimiento, 
                c.nombre_razon_social, 
                u.nombre_completo,
                m.estado,
                CASE m.estado
                    WHEN 0 THEN '<span class=\"badge bg-secondary\">Completado</span>'
                    WHEN 1 THEN '<span class=\"badge bg-warning text-dark\">Recibido por ventas</span>'
                    WHEN 2 THEN '<span class=\"badge bg-primary\">En Proceso (Bodega)</span>'
                    WHEN 3 THEN '<span class=\"badge bg-info text-dark\">Enviado</span>'
                    WHEN 4 THEN '<span class=\"badge bg-success\">Entregado</span>'
                    WHEN 5 THEN '<span class=\"badge bg-danger\">Devolución Parcial</span>'
                    WHEN 6 THEN '<span class=\"badge bg-dark\">Devolución Total</span>'
                    ELSE '<span class=\"badge bg-light text-dark\">Desconocido</span>'
                END AS estado_badge
            FROM movimientos_inventario m
            INNER JOIN clientes c ON m.id_cliente = c.id_cliente
            INNER JOIN usuarios u ON m.id_usuario = u.id_usuario
            ORDER BY m.id_movimiento DESC 
            LIMIT 10";
    $request = $this->select_all($sql);
    return $request;
}

public function selectTipoMovMes(int $anio, int $mes)
{
    $intAnio = intval($anio);
    $intMes  = intval($mes);

    $sql = "WITH movimientos_mes AS (
    SELECT 
        CASE 
            WHEN m.tipo_movimiento = 'ENTRADA' AND m.estado IN (7, 8) THEN 'DEVOLUCION'
            ELSE m.tipo_movimiento
        END AS tipo_movimiento,
        m.cantidad,
        CASE 
            WHEN m.tipo_movimiento = 'ENTRADA' AND m.estado IN (7, 8) 
                THEN m.cantidad * COALESCE(sal.precio_unitario, 0)
            WHEN m.tipo_movimiento = 'ENTRADA' 
                THEN m.cantidad * m.costo_unitario
            ELSE m.cantidad * m.precio_unitario
        END AS subtotal
    FROM movimientos_inventario m
    LEFT JOIN movimientos_inventario sal 
        ON sal.id_movimiento = COALESCE(
            m.id_movimiento_origen,
            NULLIF(SUBSTRING(m.motivo FROM '^Devolución Salida #([0-9]+):'), '')::INT
        )
    WHERE EXTRACT(YEAR FROM m.fecha_movimiento) = $intAnio
      AND EXTRACT(MONTH FROM m.fecha_movimiento) = $intMes
)
SELECT 
    tipo_movimiento,
    COUNT(*) AS total_movimientos,
    COALESCE(SUM(cantidad), 0) AS total_unidades,
    COALESCE(SUM(subtotal), 0) AS total_monto
FROM movimientos_mes
GROUP BY tipo_movimiento
ORDER BY tipo_movimiento ASC";
    $request = $this->select_all($sql);
    $meses = Meses();
    return [
        'anio'  => $intAnio,
        'mes'   => $meses[intval($intMes-1)],
        'datos' => $request
    ];
}

public function selectVersusDia(int $anio, int $mes)
{
    $intAnio = intval($anio);
    $intMes  = intval($mes);

    $sql = "WITH dias_mes AS (
                SELECT generate_series(
                    MAKE_DATE($intAnio, $intMes, 1),
                    (MAKE_DATE($intAnio, $intMes, 1) + INTERVAL '1 month - 1 day')::date,
                    INTERVAL '1 day'
                )::date AS fecha
            )
            SELECT 
                EXTRACT(DAY FROM d.fecha)::INT AS dia,
                TO_CHAR(d.fecha, 'DD/MM/YYYY') AS fecha_formato,
                COUNT(m.id_movimiento) AS total_recibidos,
                COUNT(m.id_movimiento) FILTER (WHERE m.estado = 4) AS total_entregas,
                COALESCE(SUM(m.cantidad), 0) AS unidades_recibidas,
                COALESCE(SUM(m.cantidad) FILTER (WHERE m.estado = 4), 0) AS unidades_entregadas
            FROM dias_mes d
            LEFT JOIN movimientos_inventario m 
                ON m.fecha_movimiento::date = d.fecha
                AND m.tipo_movimiento = 'SALIDA'
            GROUP BY d.fecha
            ORDER BY d.fecha ASC";

    $request = $this->select_all($sql);

    return [
        'anio'  => $intAnio,
        'mes'   => $intMes,
        'datos' => $request
    ];
}


  






}

?>