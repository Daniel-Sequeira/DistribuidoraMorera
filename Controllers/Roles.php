<?php

class Roles extends Controllers{

public function __construct(){
   parent::__construct();
}

public function roles()
{
   $data['page_id'] = 3;
   $data['tag_page'] = "Roles Usuario";
   $data['page_title'] = "Roles Usuario <small> Distribuidora Morera</small>";
   $data['page_name'] = "roles";
  
   
   $this->views->getView($this,"roles",$data);
}








}

?>