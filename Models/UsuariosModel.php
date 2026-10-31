<?php
require_once(__DIR__ . "/../Libraries/Core/Pgsql.php");

class UsuariosModel extends Pgsql {
    private $intIdUsuario;
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

    public function selectUsuarios(){
        $sql = "SELECT p.id_usuario, p.id_rol,p.identificacion,p.nombre_completo,p.correo,p.estado,
        r.nombre_rol
        FROM usuarios p
        INNER JOIN roles r
        ON p.id_rol = r.id_rol 
        WHERE p.estado != '0' ";
        $request = $this->select_all($sql);
        return $request;
    }

    //Un usuario
   public function selectUsuario(int $id_usuario)
{
  $this->intIdUsuario = $id_usuario;
  $sql = "SELECT p.id_usuario,p.identificacion,p.nombre_completo, p.correo, r.id_rol, r.nombre_rol, p.estado,
    TO_CHAR(p.creado_en,'DD-MM-YYYY') as creado_en
    FROM usuarios p
    INNER JOIN roles r
    ON p.id_rol = r.id_rol
    WHERE p.id_usuario = $this->intIdUsuario";
 $request = $this->select($sql); 
 return $request;
}

//Actualizar usuario
public function updateUsuario(int $idUsuario, string $identificacion, string $nombre, string $email, string $password, int $rolid, int $status) {
    
    // VALIDACIÓN: Buscar si el correo o la identificación ya existen, 
    // pero no sean del usuario que estamos editando actualmente (!= $idUsuario)
    $sql = "SELECT * FROM usuarios 
            WHERE (correo = '{$email}' OR identificacion = '{$identificacion}') 
            AND id_usuario != $idUsuario";
            
    $request = $this->select_all($sql);

    // Si $request está vacío, significa que el correo y la identificación están libres
    if(empty($request)) {
        
        if($password != "") {
            $sql = "UPDATE usuarios SET identificacion=?, nombre_completo=?, correo=?, password_hash=?, id_rol=?, estado=? 
                    WHERE id_usuario = ?";
            $arrData = array($identificacion, $nombre, $email, $password, $rolid, $status, $idUsuario);
        } else {
            $sql = "UPDATE usuarios SET identificacion=?, nombre_completo=?, correo=?, id_rol=?, estado=? 
                    WHERE id_usuario = ?";
            $arrData = array($identificacion, $nombre, $email, $rolid, $status, $idUsuario);
        }
        
        $request = $this->update($sql, $arrData);
        
    } else {
        // Se encontró a otro usuario con esos datos
        $request = "exist"; 
    }
    
    return $request;
}






}

