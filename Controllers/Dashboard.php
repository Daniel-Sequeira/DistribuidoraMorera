<?php

class Dashboard extends Controllers{

public function __construct(){
   sessionStart();
   parent::__construct();
   //session_start();
   //session_regenerate_id(true);
   if(empty($_SESSION['login'])){
      header('location: '.base_url().'login');
   }  
   getPermisos(1);
}

public function dashboard()
{
   $data['page_id'] = 2;
   $data['tag_page'] = "Dashboard Distribuidora Morera";
   $data['page_title'] = "Dashboard Distribuidora Morera";
   $data['page_name'] = "dashboard";
   $data['page_functions_js'] = "functions_dashboard.js";
   $this->views->getView($this,"dashboard",$data);
}








}

?>