<?php 
class Estanterias extends Controllers{

public function __construct(){
   sessionStart();
   session_regenerate_id(true);
   parent::__construct();
   if(empty($_SESSION['login'])){
      header('location: '.base_url().'/login');
   }  
   getPermisos(6);
}

public function Estanterias()
{
    if(EMPTY($_SESSION['permisosMod']['r'])){
        header("Location:".base_url().'dashboard');
        die();
    }
   $data['tag_page'] = "Estanterias";
   $data['page_title'] = "Estanterías";
   $data['page_name'] = "estanterias";
   $data['page_functions_js'] = "functions_estanterias.js";
   $this->views->getView($this,"estanterias",$data);
}

public function setEstanteria(){
   if($_POST){
     //dep($_POST);
      //dep($_FILES);
      //exit();
      if(empty($_POST['txtCodigo_estanteria']) || empty($_POST['txtDescripcion']) || !isset($_POST['listStatus']) || $_POST['listStatus'] === '')
         {
            $arrResponse = array("status" => false, "msg" => 'Datos incorrectos.');
         }else{
           $intIdEstanteria = intval($_POST['id_estanteria']);
           $strCodigoEstanteria = strClean($_POST['txtCodigo_estanteria']);
           $strDescripcion = strClean($_POST['txtDescripcion']);
           $intStatus = intval($_POST['listStatus']);

           $foto         = $_FILES['foto'];
           $nombre_foto  = $foto['name'];
           $type         = $foto['type'];
           $url_temp     = $foto['tmp_name'];
           $fecha        = date('ymd');
           $hora         = date('Hms');
           $imgPortada   = 'portada_estanteria.png';
           $request_estanteria = "";
           
            $option = 0;
           if($nombre_foto != ''){
            $imgPortada = 'img_'.md5(date('d-m-Y H:m:s')).'.jpg';
           }
           if($intIdEstanteria == 0)
            {   //crear
              if(EMPTY($_SESSION['permisosMod']['w'])){
                  $request_estanteria = $this->model->insertEstanteria($strCodigoEstanteria,$strDescripcion,$imgPortada,$intStatus);
                  $option = 1;
              }
            }else{
               //actualizar
               if(EMPTY($_SESSION['permisosMod']['u'])){
                  If($nombre_foto == ''){
                     if($_POST['foto_actual'] != 'portada_estanteria.png'&& $_POST['foto_remove'] == 0){
                        $imgPortada = $_POST['foto_actual'];
                     }
                  }
                  $request_estanteria = $this->model->updateEstanteria($intIdEstanteria,$strCodigoEstanteria,$strDescripcion,$imgPortada,$intStatus);
                  $option = 2;
               }
            }
            if($request_estanteria > 0) 
               {
                  if($option == 1) //Si es 1 Crea si es 2 actualiza
                     {
                        $arrResponse = array ('status' => true, 'msg'=> 'Datos guardados correctamente.');
                        if($nombre_foto != ''){uploadImage($foto,$imgPortada);}
                     }else{
                        $arrResponse = array ('status' => true, 'msg'=> 'Datos actualizados correctamente.');
                        if($nombre_foto != ''){uploadImage($foto,$imgPortada);} //Si actualizamos foto
                        //No enviamos imagen, remove 1 elimina foto, y la actual no es la imagen por defecto 
                        if(($nombre_foto == '' && $_POST['foto_remove'] == 1 && $_POST['foto_actual'] != 'portada_estanteria.png')
                           //Nombre diferente a vacio envia foto, y es diferente a la imagen por defecto 
                            || ($nombre_foto != '' && $_POST['foto_actual'] != 'portada_estanteria.png')){
                              //Cualquiera de las condiciones elimina la foto actual
                            deleteFile($_POST['foto_actual']);
                        }
                     }
               }else if ($request_estanteria == 'exist'){
                  $arrResponse = array ('status' => false, 'msg'=> 'Atención La estanteria ya existe.');
               }else{
                   $arrResponse = array ('status' => false, 'msg'=> 'No es posible almacenar datos.');
               }
           } 
           echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
        }
    die();  
}

//Obtener todas los Estanterias
public function getEstanterias()
{
    if($_SESSION['permisosMod']['r']){
   $arrData = $this->model->selectEstanterias();
   $canView = $_SESSION['permisosMod']['r'];
   $canEdit = $_SESSION['permisosMod']['u'];
   foreach ($arrData as &$estanteria) {
            $id = $estanteria['id_estanteria'];
            $esActivo = filter_var($estanteria['estado'], FILTER_VALIDATE_BOOLEAN);
            $estanteria['estado'] = ($estanteria['estado'] == 1)
                ? '<span class="badge badge-success">Activo</span>'
                : '<span class="badge badge-danger">Inactivo</span>';

            $btnView = $canView ? '<button class="btn btn-info btn-sm btnViewEstanteria" cl="'.$id.'" title="Ver estanteria"><i class="fas fa-eye"></i></button> ' : '';
            $btnEdit = $canEdit ? '<button class="btn btn-primary btn-sm btnEditEstanteria" cl="'.$id.'" title="Editar estanteria"><i class="fas fa-pencil-alt"></i></button>' : '';

            $estanteria['opciones'] = '<div class="text-center">' . $btnView . $btnEdit . '</div>';
        }
   
   echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
}
   die();
}

//Una estanteria
public function getEstanteria(int $id_estanteria)
{
  if($_SESSION['permisosMod']['r']){
    $intIdEstanteria = intval($id_estanteria);
    if($intIdEstanteria > 0)
    {
        // Consultamos al modelo
        $arrData = $this->model->selectEstanteria($intIdEstanteria);
        
        if(empty($arrData)) {
            $arrResponse = array('status' => false, 'msg' => 'Datos no encontrados.');
        } else {
            $arrData['url_portada'] = media().'images/uploads/'. $arrData['imagen'];
            $arrResponse = array('status' => true, 'data' => $arrData);
        }
        
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
    }
  }
    die();
}




}
?>