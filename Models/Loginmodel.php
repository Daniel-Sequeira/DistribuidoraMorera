<?php

class LoginModel extends Pgsql 
{
    private $intIdUsuario;
    private $strUsuario;
    private $strPassword;
    private $strToken;


    public function __construct(){
       parent::__construct();
    }

    public function loginUser(string $usuario, string $password){
        $this->strUsuario = $usuario;
        $this->strPassword = $password;
        $sql = "SELECT id_usuario, estado FROM usuarios WHERE correo = '$this->strUsuario' 
        AND password_hash = '$this->strPassword' and estado != '0'";
       
        $request = $this->select($sql);
        return $request;
    }

  






}

?>