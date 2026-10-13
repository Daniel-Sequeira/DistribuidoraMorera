<?php

class Home extends Controllers{

public function __construct(){
   parent::__construct();
}

public function home()
{
   $data['page_id'] = 1;
   $data['tag_page'] = "Home";
   $data['page_title'] = "Distribuidora Morera";
   $data['page_name'] = "home";
   $data['page_content'] = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.";
   $this->views->getView($this,"home",$data);
}

public function insertar()
{
   $data = $this->model->setUser();
   print_r($data);
}


}

?>