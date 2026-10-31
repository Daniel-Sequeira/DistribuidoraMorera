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

public function setUsuario() {
    if($_POST){
        if(empty($_POST['listRolid']) || empty($_POST['txtIdentificacion'])|| empty($_POST['txtNombre']) ||empty($_POST['txtEmail'])) {
            $arrResponse = array("status" => false, "msg" => 'Datos incorrectos');
        } else {
            // Capturar el ID oculto del formulario
            $idUsuario = intval($_POST['id_usuario']); 
            
            $intRol = intval(strClean($_POST['listRolid']));
            $strIdentificacion = strClean($_POST['txtIdentificacion']);
            $strNombre = ucwords(strClean($_POST['txtNombre']));
            $strEmail = strtolower(strClean($_POST['txtEmail']));
            
            // Capturar el estado (si viene en el form, sino por defecto 1)
            $intStatus = isset($_POST['listStatus']) ? intval($_POST['listStatus']) : 1;
            
            $request_user = "";

            if($idUsuario == 0) {
                // Crear nuevo
                $strPassword = empty($_POST['txtPassword']) ? hash("SHA256", passGenerator()) : hash("SHA256", $_POST['txtPassword']);
                $request_user = $this->model->insertUsuario($intRol, $strIdentificacion, $strNombre, $strEmail, $strPassword, $intStatus);
                $option = 1;
                
            } else {
                // Actualizar
                // Si está vacío al editar, enviamos "" para que el Modelo NO toque la clave
                $strPassword = empty($_POST['txtPassword']) ? "" : hash("SHA256", $_POST['txtPassword']);
                
                // Llamada método updateUsuario del modelo
                $request_user = $this->model->updateUsuario($idUsuario, $strIdentificacion, $strNombre, $strEmail, $strPassword, $intRol, $intStatus);
                $option = 2;
            }

            // Respuestas compara tipo dato exacto 
            if($request_user === 'exist') {
                $arrResponse = array('status' => false, 'msg' => 'Atención el usuario (correo o identificación) ya existe.');
            } else if($request_user !== false && $request_user !== 0) {
                $mensaje = ($option == 1) ? 'Datos guardados correctamente.' : 'Datos actualizados correctamente.';
                $arrResponse = array('status' => true, 'msg' => $mensaje);
            } else {
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
      <button class="btn btn-info btn-sm btnViewUsuario" us="'.$arrData[$i]['id_usuario'].'" title= "Ver usuario"><i class="fas 
      fa-eye"></i></button>
      <button class="btn btn-primary btn-sm btnEditUsuario" us="'.$arrData[$i]['id_usuario'].'" title= "Editar usuario"><i class="fas 
      fa-pencil-alt"></i></button>
      </div>';

   }
   
   echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
   die();
}

//Un Usuario
public function getUsuario(int $id_usuario)
{
    $idusuario = intval($id_usuario);
        if($idusuario > 0) {
           $arrData = $this->model->selectUsuario($idusuario);
           if(empty($arrData))
            {
               $arrResponse = array('status' => false, 'msg' => 'Datos no encontrados.');
            }else{
                $arrResponse = array('status' => true, 'data' => $arrData);
            }
            echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
        }
    die();
}








}

?>