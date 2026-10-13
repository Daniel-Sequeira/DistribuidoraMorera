<?php 
class Views 
{

function getView($controller, $view, $data="")
{
    $controllerName = get_class($controller);
    if($controllerName == "Home"){
        $view = 'Views/'.$view.".php";
    }else{
        $view = 'Views/'.$controllerName."/".$view.".php";
    }
    require_once($view);

}



}


?>