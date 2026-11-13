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






}
?>