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





  






}

?>