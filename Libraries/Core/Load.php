<?php
/**
 * @var string $controller
 * @var string $method
 * @var string $params
 */
//Load
$controllerFile = "Controllers/".$controller.".php";
if(file_exists($controllerFile)){
    require_once($controllerFile);
    $controllerInstance = new $controller();

    if(method_exists($controllerInstance, $method)){
        $controllerInstance->{$method}($params);
    }else{
       require_once("Controllers/Errors.php");
       $error = new Errors();
    }

}else{
    require_once("Controllers/Errors.php");
    $error = new Errors();
}

?>