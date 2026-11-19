<?php
class ProveedoresModel extends Pgsql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function selectProveedores()
    {
        $sql = "SELECT 
                    id_proveedor,
                    nombre_razon_social,
                    identificacion,
                    telefono,
                    correo,
                    estado
                FROM proveedores
                ORDER BY id_proveedor ASC";
        return $this->select_all($sql);
    }

    public function selectProveedor(int $idproveedor)
    {
        $sql = "SELECT 
                    id_proveedor,
                    nombre_razon_social,
                    identificacion,
                    telefono,
                    correo,
                    direccion,
                    estado,
                    TO_CHAR(creado_en, 'DD/MM/YYYY HH24:MI') AS creado_en
                FROM proveedores
                WHERE id_proveedor = $idproveedor";
        return $this->select($sql);
    }

    public function insertProveedor(
        string $nombre,
        string $identificacion,
        string $telefono,
        string $correo,
        string $direccion,
        int $estado
    ) {
        $sqlCheck = "SELECT id_proveedor FROM proveedores WHERE identificacion = '$identificacion'";
        $requestCheck = $this->select_all($sqlCheck);
        if (!empty($requestCheck)) {
            return 'exist';
        }

        // PostgreSQL requiere literales 'true' o 'false' para columnas boolean vía PDO
        $boolEstado = ($estado == 1) ? 'true' : 'false';
        $telVal     = !empty($telefono) ? $telefono : null;
        $correoVal  = !empty($correo) ? $correo : null;
        $dirVal     = !empty($direccion) ? $direccion : null;

        $query = "INSERT INTO proveedores (nombre_razon_social, identificacion, telefono, correo, direccion, estado)
                  VALUES (?, ?, ?, ?, ?, ?)";
        $arrData = array($nombre, $identificacion, $telVal, $correoVal, $dirVal, $boolEstado);
        return $this->insert($query, $arrData);
    }

    public function updateProveedor(
        int $idProveedor,
        string $nombre,
        string $identificacion,
        string $telefono,
        string $correo,
        string $direccion,
        int $estado
    ) {
        $sqlCheck = "SELECT id_proveedor FROM proveedores WHERE identificacion = '$identificacion' AND id_proveedor != $idProveedor";
        $requestCheck = $this->select_all($sqlCheck);
        if (!empty($requestCheck)) {
            return 'exist';
        }

        $boolEstado = ($estado == 1) ? 'true' : 'false';
        $telVal     = !empty($telefono) ? $telefono : null;
        $correoVal  = !empty($correo) ? $correo : null;
        $dirVal     = !empty($direccion) ? $direccion : null;

        $sql = "UPDATE proveedores 
                SET nombre_razon_social = ?, identificacion = ?, telefono = ?, correo = ?, direccion = ?, estado = ?
                WHERE id_proveedor = $idProveedor";
        $arrData = array($nombre, $identificacion, $telVal, $correoVal, $dirVal, $boolEstado);
        return $this->update($sql, $arrData);
    }
}
?>