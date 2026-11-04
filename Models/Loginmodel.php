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

    public function sessionLogin(int $iduser){
        $this->intIdUsuario = $iduser;
        $sql = "SELECT u.id_usuario, u.identificacion, u.nombre_completo, u.correo, u.estado, r.id_rol, r.nombre_rol
        FROM usuarios u
        INNER JOIN roles r ON u.id_rol = r.id_rol
        WHERE u.id_usuario = '$this->intIdUsuario'";
        $request = $this->select($sql);
        return $request;

    }

    public function getUserEmail(string $strEmail){
        $this->strUsuario = $strEmail;
        $sql = "SELECT id_usuario, nombre_completo, correo, estado FROM usuarios WHERE 
        correo = '$this->strUsuario' AND estado != '0'";
        $request = $this->select($sql);
        return $request;
    }

    public function setTokenUser(int $idusuario, string $token){
        $this->intIdUsuario = $idusuario;
        $this->strToken = $token;
        $sql = "UPDATE usuarios SET token = ? WHERE id_usuario = $this->intIdUsuario";
        $arrData = array($this->strToken);
        $request = $this->update($sql,$arrData);
        return $request;
    }   

    public function getUsuario(string $email, string $token){
        $this->strUsuario = $email;
        $this->strToken = $token;
        $sql = "SELECT id_usuario FROM usuarios WHERE 
        correo = '$this->strUsuario' AND token = '$this->strToken' AND estado = '1'";
        $request = $this->select($sql);   
        return $request; 

    }

    public function insertPassword(int $idusuario, string $password){
        $this->intIdUsuario = $idusuario;
        $this->strPassword = $password;
        $sql = "UPDATE usuarios SET password_hash = ?, token = ? WHERE id_usuario = $this->intIdUsuario";
        $arrData = array($this->strPassword,"");
        $request = $this->update($sql,$arrData);
        return $request;
    }



  






}

?>