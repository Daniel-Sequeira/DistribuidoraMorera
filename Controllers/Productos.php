<?php

class Productos extends Controllers{

public function __construct(){
   parent::__construct();
}

public function home()
{
   $data['page_id'] = 2;
   $data['tag_page'] = "Productos";
   $data['page_title'] = "Inventario de Productos";
   $data['page_name'] = "productos";
   $this->views->getView($this,"productos",$data);
}








}

?>