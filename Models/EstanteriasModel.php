<?php
class EstanteriasModel extends Pgsql {
    
       public $intIdEstanteria;
       public $strCodigoEstanteria; 
       public $strDescripcion; 
       public $intStatus; 
       public $strPortada;


    public function __construct(){
       parent::__construct();
    }

    public function insertEstanteria(string $codigo_estanteria,string $descripcion, string $imagen, int $status)
  {
     $return = 0;
     $this -> strCodigoEstanteria = $codigo_estanteria;
     $this -> strDescripcion = $descripcion;
     $this -> intStatus = $status;
     $this -> strPortada = $imagen;
     //Comprobar si existe
     $sql = "SELECT * FROM estanterias WHERE codigo_estanteria = '{$codigo_estanteria}' ";
     $request = $this->select_all($sql);

     if(empty($request))
      {
        $query_insert = "INSERT INTO estanterias(codigo_estanteria, descripcion, imagen, estado) VALUES(?,?,?,?)";
        $arrData = array( $this -> strCodigoEstanteria,
                          $this -> strDescripcion,
                          $this -> strPortada,
                          $this -> intStatus);
        $request_insert = $this->insert($query_insert, $arrData);
        $return = $request_insert;
      }else{
      $return = "exist";
      }
      return $return;

  }

  //Obtener todas estanterias
    public function selectEstanterias(){
        $sql = "SELECT * FROM estanterias";
        $request = $this->select_all($sql);
        return $request;
    }

  






}

?>