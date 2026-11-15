<?php

class Clientes extends Controllers{

public function __construct(){
   sessionStart();
   parent::__construct();
   if(empty($_SESSION['login'])){
      header('location: '.base_url().'/login');
   }  
   getPermisos(4);
}

public function Clientes()
{
    if(EMPTY($_SESSION['permisosMod']['r'])){
        header("Location:".base_url().'dashboard');
        die();
    }
   $data['tag_page'] = "Clientes";
   $data['page_title'] = "Clientes <small>Distribuidora Morera</small>";
   $data['page_name'] = "clientes";
   $data['page_functions_js'] = "functions_clientes.js";
   $this->views->getView($this,"clientes",$data);
}

public function setCliente(){
if($_SESSION['permisosMod']['r']){
    //dep($_POST);
    //die();
if($_POST){
        if(empty($_POST['txtNombreRazonSocial']) || empty($_POST['txtIdentificacion'])|| empty($_POST['txtTelefono']) ||empty($_POST['txtEmail'])||empty($_POST['txtDireccion'])) {
            $arrResponse = array("status" => false, "msg" => 'Datos incorrectos');
        } else {
            // Capturar el ID oculto del formulario
            $idCliente = intval($_POST['id_cliente']); 
            $strNombre = ucwords(strCLean($_POST['txtNombreRazonSocial']));
            $strIdentificacion = strClean($_POST['txtIdentificacion']);
            $strTelefono = intval($_POST['txtTelefono']);
            $strEmail = strtolower(strClean($_POST['txtEmail']));
            $strDireccion = ($_POST['txtDireccion']);
            // Capturar el estado (si viene en el form, sino por defecto 1)
            $intStatus = isset($_POST['listStatus']) ? intval($_POST['listStatus']) : 1;
            
            $request_user = "";
            $option = 0;
            if($idCliente == 0) {
                if($_SESSION['permisosMod']['w']){
                $request_user = $this->model->insertCliente($strNombre, $strIdentificacion, $strTelefono, $strEmail, $strDireccion,$intStatus);
                $option = 1;
                }
            } else {
                // Llamada método updateCliente del modelo
               if($_SESSION['permisosMod']['u']){
                $request_user = $this->model->updateCliente($idCliente, $strNombre, $strIdentificacion,$strTelefono , $strEmail, $strDireccion, $intStatus);
                $option = 2;
                }
            }

            // Respuestas compara tipo dato exacto 
            if($request_user === 'exist') {
                $arrResponse = array('status' => false, 'msg' => 'Atención el cliente (correo o identificación) ya existe.');
            } else if($request_user !== false && $request_user !== 0) {
                $mensaje = ($option == 1) ? 'Datos guardados correctamente.' : 'Datos actualizados correctamente.';
                $arrResponse = array('status' => true, 'msg' => $mensaje);
            } else {
                $arrResponse = array('status' => false, 'msg' => 'No es posible almacenar datos.');
            }
        }
        
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
    }
 }
    die();
    
}
//Obtener todos los clientes
public function getClientes()
{
    if($_SESSION['permisosMod']['r']){
   $arrData = $this->model->selectClientes();
   $canView = $_SESSION['permisosMod']['r'];
   $canEdit = $_SESSION['permisosMod']['u'];
   foreach ($arrData as &$cliente) {
            $id = $cliente['id_cliente'];
            $esActivo = filter_var($cliente['estado'], FILTER_VALIDATE_BOOLEAN);
            $cliente['estado'] = ($cliente['estado'] == 1)
                ? '<span class="badge badge-success">Activo</span>'
                : '<span class="badge badge-danger">Inactivo</span>';

            $btnView = $canView ? '<button class="btn btn-info btn-sm btnViewCliente" cl="'.$id.'" title="Ver cliente"><i class="fas fa-eye"></i></button> ' : '';
            $btnEdit = $canEdit ? '<button class="btn btn-primary btn-sm btnEditCliente" cl="'.$id.'" title="Editar cliente"><i class="fas fa-pencil-alt"></i></button>' : '';

            $cliente['opciones'] = '<div class="text-center">' . $btnView . $btnEdit . '</div>';
        }
   
   echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
}
   die();
}
//Obtener un cliente
public function getCliente( $id_cliente)
{
    if($_SESSION['permisosMod']['r']){
    $idcliente = intval($id_cliente);
        if($idcliente > 0) {
           $arrData = $this->model->selectCliente($idcliente);
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







}

?>