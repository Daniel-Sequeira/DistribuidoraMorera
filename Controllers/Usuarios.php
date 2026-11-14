<?php

class Usuarios extends Controllers{

public function __construct(){
   sessionStart();
   parent::__construct();
   if(empty($_SESSION['login'])){
      header('location: '.base_url().'/login');
   }  
   getPermisos(2);
}

public function Usuarios()
{
    if(EMPTY($_SESSION['permisosMod']['r'])){
        header("Location:".base_url().'dashboard');
        die();
    }
   $data['tag_page'] = "Usuarios";
   $data['page_title'] = "Usuarios <small>Distribuidora Morera</small>";
   $data['page_name'] = "usuarios";
   $data['page_functions_js'] = "functions_usuarios.js";
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
            $option = 0;
            if($idUsuario == 0) {
                // Crear nuevo
                $strPassword = empty($_POST['txtPassword']) ? hash("SHA256", passGenerator()) : hash("SHA256", $_POST['txtPassword']);
                if($_SESSION['permisosMod']['w']){
                $request_user = $this->model->insertUsuario($intRol, $strIdentificacion, $strNombre, $strEmail, $strPassword, $intStatus);
                $option = 1;
                }
            } else {
                // Actualizar
                // Si está vacío al editar, enviamos "" para que el Modelo NO toque la clave
                $strPassword = empty($_POST['txtPassword']) ? "" : hash("SHA256", $_POST['txtPassword']);
                
                // Llamada método updateUsuario del modelo
                if($_SESSION['permisosMod']['u']){
                $request_user = $this->model->updateUsuario($idUsuario, $strIdentificacion, $strNombre, $strEmail, $strPassword, $intRol, $intStatus);
                $option = 2;
                }
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
    if($_SESSION['permisosMod']['r']){
   $arrData = $this->model->selectUsuarios();
   $canView = $_SESSION['permisosMod']['r'];
   $canEdit = $_SESSION['permisosMod']['u'];
   foreach ($arrData as &$user) {
            $id = $user['id_usuario'];

            $user['estado'] = ($user['estado'] == 1)
                ? '<span class="badge badge-success">Activo</span>'
                : '<span class="badge badge-danger">Inactivo</span>';

            $btnView = $canView ? '<button class="btn btn-info btn-sm btnViewUsuario" us="'.$id.'" title="Ver usuario"><i class="fas fa-eye"></i></button> ' : '';
            $btnEdit = $canEdit ? '<button class="btn btn-primary btn-sm btnEditUsuario" us="'.$id.'" title="Editar usuario"><i class="fas fa-pencil-alt"></i></button>' : '';

            $user['opciones'] = '<div class="text-center">' . $btnView . $btnEdit . '</div>';
        }
   
   echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
    }
   die();
}

//Un Usuario
public function getUsuario( $id_usuario)
{
    if($_SESSION['permisosMod']['r']){
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
    }
    die();
}

public function perfil(){
   $data['tag_page'] = "Perfil";
   $data['page_title'] = "Perfil de usuario";
   $data['page_name'] = "perfil";
   $data['page_functions_js'] = "functions_usuarios.js";
   $this->views->getView($this,"perfil",$data);
}

public function putPerfil(){
   
   if($_POST){
    if(empty($_POST['txtIdentificacion']) || empty($_POST['txtNombre']))
        {
           $arrResponse = array("status" => false, "msg" => 'Datos Incorrectos.');
        } else{
            $idUsuario = isset($_SESSION['idUser']) ? intval($_SESSION['idUser']):0;
            if($idUsuario === 0) {
               echo json_encode(['status' => false, 'msg' => 'Error: ID de sesión no válido.']);
               die();
           }
            $strIdentificacion = strClean($_POST['txtIdentificacion']);
            $strNombre = strClean($_POST['txtNombre']);
            $strPassword = "";
            if(!empty($_POST['txtPassword'])){
                $strPassword = hash("SHA256",$_POST['txtPassword']);
            }
            $request_user = $this->model->updatePerfil($idUsuario, $strIdentificacion,$strNombre,$strPassword);
            if($request_user)
                {
                    sessionUser($_SESSION['idUser']);
                    $arrResponse = array('status' => true, 'msg'=>'Datos Actualizados Correctamente.');
        
                }else{
                    $arrResponse = array('status' => false, 'msg'=>'No es posible almacenar datos.');
                }

        }
        echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
   
   }
    die();
    
}










}

?>