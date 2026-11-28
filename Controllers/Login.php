<?php
/** @var $arrData */
class Login extends Controllers{

public function __construct(){
   session_start();
   if(isset($_SESSION['login'])){
      header('location: '.base_url().'dashboard');
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
             $strPassword = $_POST['txtPassword'];
             $requestUser = $this->model->loginUser($strUsuario,$strPassword);
             If(empty($requestUser)){
                $arrResponse = array("status" => false, "msg" => 'Usuario o contraseña incorrectos.');       
             }else{
               
                if($requestUser['estado'] == 1){
                  session_regenerate_id(true);// Proteger de session fixation
                   $_SESSION['idUser'] = $requestUser['id_usuario'];
                   $_SESSION['login'] = true;
                   $_SESSION['timeout'] = true;
                   $_SESSION['inicio'] = time();
                   $arrData =$this->model->sessionlogin($_SESSION['idUser']);
                   sessionUser($_SESSION['idUser']);
                    $_SESSION['userData'] = $arrData;
                   $arrResponse = array("status" => true, "msg" => 'ok');
             }else{
                $arrResponse = array("status" => false, "msg" => 'Cuenta inactiva.');       
             }
           } 
        }
        //sleep(4);
        echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);   
     }
   die();
   }

   public function resetPass(){
  if ($_POST) {
        if (empty($_POST['txtEmailReset'])) {
            $arrResponse = array('status' => false, 'msg' => 'Error de datos');
        } else {
            $strEmail = strtolower(strClean($_POST['txtEmailReset']));
            $arrData = $this->model->getUserEmail($strEmail);

            if (empty($arrData)) {
                $arrResponse = array('status' => false, 'msg' => 'El usuario no existe.');
            } else {
                // Generar el token (o tomar el existente) y actualizar la BD
                $token = token(); // O la función que genere tokens en tu sistema (ej: bin2hex(random_bytes(32)))
                $requestPass = $this->model->setTokenUser($arrData['id_usuario'], $token);

                if ($requestPass) {
                    //DEFINIR LA VARIABLE $url_recovery
                    $url_recovery = BASE_URL . 'login/confirmUser/' . $strEmail . '/' . $token;

                    //Preparar el arreglo de datos para la plantilla del correo
                    $dataEmail = array(
                        'asunto'        => 'Recuperar cuenta - ' . NOMBRE_REMITENTE,
                        'email'         => $strEmail,
                        'nombreUsuario' => $arrData['nombre_completo'],
                        'url_recovery'  => $url_recovery
                    );

                    // Enviar el correo usando tu helper
                    $sendMail = sendEmail($dataEmail, 'email_cambioPassword');

                    if ($sendMail) {
                        $arrResponse = array(
                            'status' => true,
                            'msg'    => 'Se ha enviado un correo a tu cuenta de correo electrónico para cambiar tu contraseña.'
                        );
                    } else {
                        $arrResponse = array(
                            'status' => false,
                            'msg'    => 'No fue posible enviar el correo, intenta más tarde.'
                        );
                    }
                } else {
                    $arrResponse = array(
                        'status' => false,
                        'msg'    => 'No fue posible realizar la solicitud, intenta más tarde.'
                    );
                }
            }
        }
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
    }
    die();
}
     
 public function confirmUser(string $params)
    {
        if (empty($params)) {
            header('Location: ' . base_url());
            exit;
        }

        $arrParams = explode(',', $params);
        $strEmail = strClean($arrParams[0] ?? '');
        $strToken = strClean($arrParams[1] ?? '');

        $arrResponse = $this->model->getUsuario($strEmail, $strToken);
        if (empty($arrResponse)) {
            header('Location: ' . base_url());
            exit;
        }

        $data['tag_page'] = "Cambiar contraseña";
        $data['page_name'] = "cambiar_contrasenia";
        $data['page_title'] = "Cambiar Contraseña";
        $data['email'] = $strEmail;
        $data['token'] = $strToken;
        $data['id_Usuario'] = $arrResponse['id_usuario'];
        $data['page_functions_js'] = "functions_login.js";
        $this->views->getView($this, "cambiar_password", $data);
        die();
    }

 public function setPassword(){
   if(empty($_POST['id_Usuario']) || empty($_POST['txtEmail']) || empty($_POST['txtToken']) || 
      empty($_POST['txtPassword']) || empty($_POST['txtPasswordConfirm'])){
      $arrResponse = array("status" => false, "msg" => 'Error de datos');
   }else{
      $intIdUsuario = intval($_POST['id_Usuario']);
      $strEmail = strClean($_POST['txtEmail']);
      $strToken = strClean($_POST['txtToken']);
      $strPassword = $_POST['txtPassword'];
      $strPasswordConfirm = $_POST['txtPasswordConfirm'];

      if ($strPassword !== $strPasswordConfirm) {
                $arrResponse = ["status" => false, "msg" => 'Las contraseñas no son iguales.'];
            } elseif (strlen($strPassword) < 8) {
                $arrResponse = ["status" => false, "msg" => 'La contraseña debe tener al menos 8 caracteres.'];
            } else {
                $arrResponseUser = $this->model->getUsuario($strEmail, $strToken);
                if (empty($arrResponseUser)) {
                    $arrResponse = ["status" => false, "msg" => 'El enlace ha expirado o no es válido.'];
                } else {
                    // Encriptación con Bcrypt/Argon2
                    $strPasswordHash = password_hash($strPassword, PASSWORD_DEFAULT);
                    $requestPass = $this->model->insertPassword($intIdUsuario, $strPasswordHash);

                    if ($requestPass) {
                        $arrResponse = ["status" => true, "msg" => 'Contraseña actualizada correctamente.'];
                    } else {
                        $arrResponse = ["status" => false, "msg" => 'No es posible realizar el proceso.'];
                    }
                }
            } 
        }
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }
}










?>