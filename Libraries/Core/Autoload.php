<?php 
    spl_autoload_register(function($class){
    //echo LIBS.'Core/'.$class.".php"; Concatena busca carpetas y la clase que se le pasa por parametro.
        if(file_exists(LIBS.'Core/'.$class.".php")){
            require_once (LIBS.'Core/'.$class.".php");
    
        }
    });






?>