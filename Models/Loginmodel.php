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

    public function loginUser(string $usuario, string $passwordPlano): array|false
    {
        $this->strUsuario = $usuario;
        $sql = "SELECT id_usuario, password_hash, estado FROM usuarios 
        WHERE correo = ? AND estado = '1' 
        LIMIT 1";
       
        $user = $this->select($sql, [$this->strUsuario]);

        if(empty($user)){
            return false;
        }
        $hashGuardado = $user['password_hash'];
        $passwordValida = false;

        //Verificar con password_verify()
        if (password_verify($passwordPlano, $hashGuardado)) {
            $passwordValida = true;
             //Si hay cambio en algoritmo de cifrado por defecto en futura versión
        if (password_needs_rehash($hashGuardado, PASSWORD_DEFAULT)) {
                $nuevoHash = password_hash($passwordPlano, PASSWORD_DEFAULT);
                $this->actualizarHash((int)$user['id_usuario'], $nuevoHash);
            }
        }
        elseif (strlen($hashGuardado) === 64 && hash_equals($hashGuardado, hash("SHA256", $passwordPlano))) {
            $passwordValida = true;
            $nuevoHash = password_hash($passwordPlano, PASSWORD_DEFAULT);
            $this->actualizarHash((int)$user['id_usuario'], $nuevoHash);
        }

        if (!$passwordValida) {
            return false;
        }
        //Hash no retorna a controlador
        unset($user['password_hash']);
        return $user;
    }

    private function actualizarHash(int $idUsuario, string $nuevoHash): void
    {
        $sql = "UPDATE usuarios SET password_hash = ? WHERE id_usuario = ?";
        $this->update($sql, [$nuevoHash, $idUsuario]);
    }

    public function sessionLogin(int $iduser){
        $this->intIdUsuario = $iduser;
        $sql = "SELECT u.id_usuario, u.identificacion, u.nombre_completo, u.correo, u.estado, r.id_rol, r.nombre_rol
        FROM usuarios u
        INNER JOIN roles r ON u.id_rol = r.id_rol
        WHERE u.id_usuario = ?";
        $request = $this->select($sql,[$this->intIdUsuario]);
        $_SESSION['userData'] = $request;
        return $request;
    }

    public function getUserEmail(string $strEmail){
       $this->strUsuario = $strEmail;
    
    $sql = "SELECT id_usuario, nombre_completo, correo, estado 
            FROM usuarios 
            WHERE correo = ? AND estado != '0'";
            
    $arrData = array($this->strUsuario);
    $request = $this->select($sql, $arrData);
    return $request;
    }

    public function setTokenUser(int $idusuario, string $token){
        $this->intIdUsuario = $idusuario;
        $this->strToken = $token;
        // El token será válido por 1 hora
        $expira = date('Y-m-d H:i:s', strtotime('+5 minutes'));
        $sql = "UPDATE usuarios SET token = ?, token_expira = ? WHERE id_usuario = ?";
        return $this->update($sql, [$this->strToken, $expira, $this->intIdUsuario]);
    }

    public function getUsuario(string $email, string $token){
        $this->strUsuario = $email;
        $this->strToken = $token;
        $ahora = date('Y-m-d H:i:s');
        $sql = "SELECT id_usuario FROM usuarios WHERE 
        correo = ? AND token = ? AND estado = '1' AND token_expira >= ?"; 
        
        return $this->select($sql, [$this->strUsuario, $this->strToken, $ahora]);

    }

    public function insertPassword(int $idusuario, string $passwordHash){
        $this->intIdUsuario = $idusuario;
        $this->strPassword = $passwordHash;
        $sql = "UPDATE usuarios SET password_hash = ?, token = NULL, token_expira = NULL WHERE id_usuario = ?";
        return $this->update($sql, [$this->strPassword, $this->intIdUsuario]);
    }



  






}

?>