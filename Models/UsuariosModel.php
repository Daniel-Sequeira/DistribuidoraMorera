<?php
require_once(__DIR__ . "/../Libraries/Core/Pgsql.php");
class UsuariosModel extends Pgsql {
    private $intRol;
    private $strIdentificacion;
    private $strNombre;
    private $strEmail;
    private $Password;
    private $strToken;

   

    public function __construct(){
       parent::__construct();
    }

    public function insertUsuario(int $rol,string $identificacion,string $nombre,string $email, string $password){
        $this->intRol= $rol;
        $this->strIdentificacion= $identificacion;
        $this-> strNombre = $nombre;
        $this->strEmail = $email;
        $this->Password = $password;
        $return = 0;

        $sql = "SELECT * FROM  usuarios WHERE
        correo = '{$this->strEmail}' or identificacion = '{$this-> strIdentificacion}' ";
        $request =$this->select_all($sql);

        if(empty($request))
            {
                $query_insert = "INSERT INTO usuarios(id_rol,identificacion,nombre_completo, correo,password_hash) 
                VALUES(?,?,?,?,?)";
                $arrData = array($this->intRol,
                                $this->strIdentificacion,
                                $this-> strNombre,
                                $this->strEmail,
                                $this->Password);
                $request_insert = $this->insert($query_insert,$arrData);
                $return = $request_insert;
            }else{
                $return = "exist";
            }
            return $return;

    }
}

