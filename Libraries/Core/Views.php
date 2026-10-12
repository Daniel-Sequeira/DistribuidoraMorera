<?php 
class Views 
{

function getView($controller, $view)
{
    $controllerName = get_class($controller);
    if($controllerName == "Home"){
        $view = VIEWS.$view.".php";
    }else{
        $view = VIEWS."/".$controllerName."/".$view.".php";
    }
    require_once($view);

}



}


?>