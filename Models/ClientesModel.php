<?php
require_once(__DIR__ . "/../Libraries/Core/Pgsql.php");

class ClientesModel extends Pgsql {
    private $intIdCliente;
    private $strNombre;
    private $strIdentificacion;
    private $strTelefono;
    private $strEmail;
    private $strDireccion;
    private $intStatus;
   

    public function __construct(){
       parent::__construct();
    }

    public function insertCliente(string $nombre ,string $identificacion,string $telefono ,string $email, string $direccion,int $Status){
        $this->strNombre= $nombre;
        $this->strIdentificacion= $identificacion;
        $this-> strTelefono = $telefono;
        $this->strEmail = $email;
        $this->strDireccion = $direccion;
        $this->intStatus = $Status;
        $return = 0;

        $sql = "SELECT * FROM  clientes WHERE
        correo = '{$this->strEmail}' or identificacion = '{$this-> strIdentificacion}' ";
        $request =$this->select_all($sql);

        if(empty($request))
            {
                $query_insert = "INSERT INTO clientes(nombre_razon_social,identificacion,telefono, correo,direccion,estado) 
                VALUES(?,?,?,?,?,?)";
                $arrData = array($this->strNombre,
                                $this->strIdentificacion,
                                $this->strTelefono,
                                $this->strEmail,
                                $this->strDireccion,
                                $this->intStatus);
                                
                $request_insert = $this->insert($query_insert,$arrData);
                $return = $request_insert;
            }else{
                $return = "exist";
            }
            return $return;

    }
}