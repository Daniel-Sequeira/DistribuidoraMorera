<?php

class Roles extends Controllers{

public function __construct(){
   parent::__construct();
}

public function roles()
{
   $data['page_id'] = 3;
   $data['tag_page'] = "Roles Usuario";
   $data['page_title'] = "Roles Usuario <small> Distribuidora Morera</small>";
   $data['page_name'] = "roles";
   $this->views->getView($this,"roles",$data);
}

public function getRoles()
{
   $arrData = $this->model->selectRoles();
   for ($i=0; $i< count($arrData); $i++){
      if($arrData[$i]['estado'] == 1)
      { 
         $arrData[$i]['estado'] ='<span class= "badge badge-success">Activo</span>';
      }else{
         $arrData[$i]['estado'] ='<span class= "badge badge-danger">Inactivo</span>';
      }

      $arrData[$i]['opciones']= '<div class="text-center">
      <button class="btn btn-secondary btn-sm btnPermisosRol" rl="'.$arrData[$i]['id_rol'].'" title= "Permisos"><i class="fas 
      fa-key"></i></button>
      <button class="btn btn-primary btn-sm btnEditRol" rl="'.$arrData[$i]['id_rol'].'" title= "Editar"><i class="fas
      fa-pencil-alt"></i></button>
      </div>';

   }
   
   echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
   die();
}
//Un Rol
public function getRol(int $idrol)
{
    $intIdrol = intval($idrol);
    if($intIdrol > 0)
    {
        // Consultamos al modelo
        $arrData = $this->model->selectRol($intIdrol);
        
        if(empty($arrData)) {
            $arrResponse = array('status' => false, 'msg' => 'Datos no encontrados.');
        } else {
            $arrResponse = array('status' => true, 'data' => $arrData);
        }
        
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
    }
    die();
}

public function setRol(){
    $strRol = strClean($_POST['nombre_rol']);
    
    // id_rol oculto, Si está vacío, le asigna 0
    $idRol = isset($_POST['id_rol']) ? intval($_POST['id_rol']) : 0;
    if($idRol == 0) {
        // Si es 0, ES NUEVO
        $request_rol = $this->model->insertRol($strRol);
    } else {
        // Si tiene un número es EDITAR
        $intEstado = intval($_POST['listStatus']);
        //Administrador es 1 y no se puede deshabilitar
        if($idRol == 1 && $intEstado == 0) {
            $arrResponse = array('status' => false, 'msg' => 'Por seguridad, el rol de Administrador no puede ser deshabilitado.');
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
            die();
        }
        $request_rol = $this->model->updateRol($idRol, $strRol, $intEstado);
    }
    if($request_rol === 'exist') {
        $arrResponse = array('status' => false, 'msg' => 'Atención el Rol ya existe.');
    } else if ($request_rol > 0) {
        $arrResponse = array('status' => true, 'msg' => 'Datos guardados correctamente.');
    } else {
        $arrResponse = array('status' => false, 'msg' => 'No es posible almacenar datos.');
    }
    
    echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
    die();
}







}

?>