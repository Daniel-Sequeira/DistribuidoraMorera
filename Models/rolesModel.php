<?php
require_once(__DIR__ . "/../Libraries/Core/Pgsql.php");
class RolesModel extends Pgsql {

  public $strRol;

    public function __construct(){
       parent::__construct();
    }

  public function selectRoles()
  {
    $sql = "SELECT * FROM roles";
    $request = $this->select_all($sql);
    return $request;
  }
  //Un rol
  public function selectRol(int $idrol)
{
    // rol por su ID
    $sql = "SELECT * FROM roles WHERE id_rol = $idrol";
    $request = $this->select($sql); 
    
    return $request;
}

  public function insertRol(string $rol)
  {
     $return = "";
     $sql = "SELECT * FROM roles WHERE nombre_rol = '{$rol}' ";
     $request = $this->select_all($sql);

     if(empty($request))
      {
        $query_insert = "INSERT INTO roles(nombre_rol) VALUES(?)";
        $arrData = array($rol);
        $request_insert = $this->insert($query_insert, $arrData);
        $return = $request_insert;
      }else{
      $return = "exist";
      }
      return $return;

  }

  public function updateRol(int $idrol, string $rol, int $estado)
{
    //Validar que no exista rol con el mismo nombre (excluyendo el rol actual)
    $sql = "SELECT * FROM roles WHERE nombre_rol = '{$rol}' AND id_rol != $idrol ";
    $request = $this->select_all($sql);

    if(empty($request))
    {
        // Si no existe actualizar
        $sql = "UPDATE roles SET nombre_rol = ?, estado = ? WHERE id_rol = $idrol ";
        $arrData = array($rol, $estado);
        $request_update = $this->update($sql, $arrData);
        $return = $request_update;
    }else{
        // Si el nombre ya lo tiene otro rol, devolvemos 'exist'
        $return = "exist";
    }
    return $return;
}






}

?>