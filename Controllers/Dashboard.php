<?php

class Dashboard extends Controllers{

public function __construct(){
   parent::__construct();
   session_start();
   if(empty($_SESSION['login'])){
      header('location: '.base_url().'/login');
   }  
   getPermisos(1);
}

public function dashboard()
{
   $data['page_id'] = 2;
   $data['tag_page'] = "Dashboard Distribuidora Morera";
   $data['page_title'] = "Dashboard Distribuidora Morera";
   $data['page_name'] = "dashboard";
   
   $this->views->getView($this,"dashboard",$data);
}








}

?>