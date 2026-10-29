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








}

?>