<?php

class Usuarios extends Controllers{

public function __construct(){
   parent::__construct();
}

public function Usuarios()
{
   $data['tag_page'] = "Usuarios";
   $data['page_title'] = "Usuarios <small>Distribuidora Morera</small>";
   $data['page_name'] = "usuarios";
   $this->views->getView($this,"usuarios",$data);
}

public function setUsuario (){
   if($_POST){
     
      if(empty($_POST['listRolid']) || empty($_POST['txtIdentificacion'])|| empty($_POST['txtNombre']) ||empty($_POST['txtEmail']))
         {
            $arrResponse = array("status" => false, "msg" => 'Datos incorrectos');
         }else{
            $intRol = intval(strClean($_POST['listRolid']));
            $strIdentificacion = strClean($_POST['txtIdentificacion']);
            $strNombre = ucwords(strClean($_POST['txtNombre']));
            $strEmail = strtolower(strClean($_POST['txtEmail']));
            $strPassword = empty($_POST['txtPassword'])? hash("SHA256",passGenerator()) : hash("SHA256",
            $_POST['txtPassword']);

            $request_user = $this->model->insertUsuario($intRol,$strIdentificacion,$strNombre,$strEmail, $strPassword);
            if($request_user == 'exist') //COmprueba que existe
               {
                  $arrResponse = array('status' => false, 'msg' => 'Atención el usuario ya existe.');
               }else if($request_user > 0){//Si es mayor a 0 devolvio iD insertado
                    $arrResponse = array('status' => true, 'msg' => 'Datos guardados correctamente.');
               }else{
                   $arrResponse = array('status' => false, 'msg' => 'No es posible almacenar datos.');
               }
         }
         echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);

   }
   die();
}

public function getUsuarios()
{
   $arrData = $this->model->selectUsuarios();
   for ($i=0; $i< count($arrData); $i++){
      if($arrData[$i]['estado'] == 1)
      { 
         $arrData[$i]['estado'] ='<span class= "badge badge-success">Activo</span>';
      }else{
         $arrData[$i]['estado'] ='<span class= "badge badge-danger">Inactivo</span>';
      }

      $arrData[$i]['opciones']= '<div class="text-center">
      <button class="btn btn-secondary btn-sm btnViewUsuario" us="'.$arrData[$i]['id_usuario'].'" title= "Ver usuario"><i class="fas 
      fa-key"></i></button>
      <button class="btn btn-secondary btn-sm btnEditUsuario" us="'.$arrData[$i]['id_usuario'].'" title= "Editar usuario"><i class="fas 
      fa-pencil-alt"></i></button>
      </div>';

   }
   
   echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
   die();
}







}

?>