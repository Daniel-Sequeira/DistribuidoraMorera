<?php
require_once(__DIR__ . "/../Libraries/Core/Pgsql.php");
class homeModel extends Pgsql {

    public function __construct(){
       parent::__construct();
    }

    public function setUser()
    {
        $query_insert = "INSERT INTO usuarios(id_rol,nombre_completo,correo, password_hash) VALUES(?,?,?,?)";
        $passHash = password_hash('123456', PASSWORD_DEFAULT);
        $arrData = array(1, 'Daniel Sequeira', 'danielsequeira22@gmail.com', $passHash);
        $request_insert = $this->insert($query_insert, $arrData);
        return $request_insert;     
    }
    
    public function getUser($id)
    {
        $sql = "SELECT * FROM usuarios WHERE id_usuario = $id";
        $request = $this->select($sql);
        return $request;
    }

    public function updateUser(int $id, string $nombre_completo)
    {
        $sql = "UPDATE usuarios SET nombre_completo = ? WHERE id_usuario = $id";
        $arrData = array($nombre_completo);
        $request = $this->update($sql, $arrData);
        return $request;
    }
    //Eliminacion logica de usuarios
    public function deleteUser(int $id, string $estado)
    {
        $sql = "UPDATE usuarios SET estado = ? WHERE id_usuario = $id";
        $arrData = array($estado);
        $request = $this->update($sql, $arrData);
        return $request;
    }   
    







}

?>