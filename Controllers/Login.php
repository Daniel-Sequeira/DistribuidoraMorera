<?php
/** @var $arrData */
class Login extends Controllers{

public function __construct(){
    session_start();
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
}

?>