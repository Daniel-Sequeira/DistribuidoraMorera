<?php
/** @var $arrData */
class Login extends Controllers{

public function __construct(){
    session_start();
   if(isset($_SESSION['login'])){
      header('location: '.base_url().'/dashboard');
   }  
   parent::__construct();
}

public function login()
{
   $data['tag_page'] = "Login";
   $data['page_title'] = "Distribuidora Morera";
   $data['page_name'] = "login";
   $data['page_functions_js'] = "functions_login.js";
   $this->views->getView($this,"login",$data);
}

public function loginUser()
{
   if($_POST){
        if(empty($_POST['txtEmail']) || empty($_POST['txtPassword'])){
             $arrResponse = array("status" => false, "msg" => 'Error de datos');
        }else{
             $strUsuario = strtolower(strClean($_POST['txtEmail']));
             $strPassword = hash("SHA256",$_POST['txtPassword']);
             $requestUser = $this->model->loginUser($strUsuario,$strPassword);
             If(empty($requestUser)){
                $arrResponse = array("status" => false, "msg" => 'Usuario o contraseña incorrectos.');       
             }else{
                $arrData = $requestUser;
                if($arrData['estado'] == 1){
                   $_SESSION['idUser'] = $arrData['id_usuario'];
                   $_SESSION['login'] = true;
                   $arrData =$this->model->sessionlogin($_SESSION['idUser']);
                   $_SESSION['userData'] = $arrData;
                   $arrResponse = array("status" => true, "msg" => 'ok');
             }else{
                $arrResponse = array("status" => false, "msg" => 'Cuenta inactiva.');       
             }
           } 
        }
        echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);   
     }
   die();
   }

   public function resetPass(){
   if($_POST){
      if(empty($_POST['txtEmailReset'])){
            $arrResponse = array("status" => false, "msg" => 'Error de datos');
         }else{
            $token = token();
            $strEmail = strtolower(strClean($_POST['txtEmailReset']));
            $arrResponse = $this->model->getUserEmail($strEmail);
            if(empty($arrResponse)){
               $arrResponse = array("status" => false, "msg" => 'Usuario no encontrado.');
            }else{
              $token = token();
              $strEmail =strtolower(strClean($_POST['txtEmailReset']));
              $arrData = $this->model->getUserEmail($strEmail);
              if(empty($arrData)){
                 $arrResponse = array("status" => false, "msg" => 'Usuario no encontrado.');
            }else{
               $idusuario = $arrData['id_usuario'];
               $nombreUsuario = $arrData['nombre_completo'];

               $url_recovery = base_url().'/login/confirmUser/'.$strEmail.'/'.$token;
               $requestUpdate =$this->model->setTokenUser($idusuario,$token); 

               if($requestUpdate){
                  $arrResponse = array('status' =>true,
                  'msg' => 'Se ha enviado un correo a tu cuenta de correo 
                  electrónico para cambiar tu contraseña.');
               }else{
                  $arrResponse = array('status' =>false,
                  'msg' => 'No es posible realizar el proceso, 
                  intenta más tarde.');
               }
            }
         }
         echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
         }   
      die();
    }
     
 }



}

?>