<?php 
class Estanterias extends Controllers{

public function __construct(){
   sessionStart();
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
      if(empty($_POST['txtCodigo_estanteria']) || empty($_POST['txtDescripcion']) || empty($_POST['listStatus']))
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
           
            $option = 0;
           if($nombre_foto != ''){
            $imgPortada = 'img_'.md5(date('d-m-Y H:m:s')).'.jpg';
           }
           if($intIdEstanteria == 0)
            {   //crear
               $request_estanteria = $this->model->insertEstanteria($strCodigoEstanteria,$strDescripcion,$imgPortada,$intStatus);
               $option = 1;
            }else{
               //actualizar
               $request_estanteria = $this->model->updateEstanteria( $intIdEstanteria,$strCodigoEstanteria,$strDescripcion,$intStatus);
               $option = 2;
            }
            if($request_estanteria > 0)
               {
                  if($option == 1)
                     {
                        $arrResponse = array ('status' => true, 'msg'=> 'Datos guardados correctamente.');
                        if($nombre_foto != ''){uploadImage($foto,$imgPortada);}
                     }else{
                        $arrResponse = array ('status' => true, 'msg'=> 'Datos actualizados correctamente.');
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




}
?>