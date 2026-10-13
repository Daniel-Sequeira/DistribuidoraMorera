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









}
?>