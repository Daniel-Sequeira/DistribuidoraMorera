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
    //Todos los clientes
    public function selectClientes(){
        $sql = "SELECT id_cliente,nombre_razon_social,identificacion,telefono,correo,direccion,
        estado
        FROM clientes";
        $request = $this->select_all($sql);
        return $request;
    }
    //Un cliente
   public function selectCliente(int $id_cliente)
{
  $this->intIdCliente = $id_cliente;
  $sql = "SELECT id_cliente,nombre_razon_social,identificacion,telefono, correo,direccion, estado,
    TO_CHAR(creado_en,'DD-MM-YYYY') as creado_en
    FROM clientes 
    WHERE id_cliente = $this->intIdCliente";
 $request = $this->select($sql); 
 return $request;
}

//Actualizar CLiente
public function updateCliente(int $idCliente,string $nombre,string $identificacion, string $telefono, string $email, string $direccion, int $status)
{
    $this->intIdCliente      = $idCliente;
    $this->strNombre         = $nombre;
    $this->strIdentificacion = $identificacion;
    $this->strTelefono       = $telefono;
    $this->strEmail          = $email;
    $this->strDireccion      = $direccion;
    $this->intStatus         = $status;

    // Verificar que la identificación o el correo no pertenezcan a OTRO cliente
    $sql = "SELECT * FROM clientes 
            WHERE (correo = '{$this->strEmail}' OR identificacion = '{$this->strIdentificacion}') 
            AND id_cliente != {$this->intIdCliente}";
    $request = $this->select_all($sql);

    // Si $request está vacío, significa que el correo y la identificación están libres
    if (empty($request)) {
        $sql = "UPDATE clientes 
                SET nombre_razon_social = ?,identificacion = ?, telefono = ?, correo = ?, direccion = ?, estado = ? 
                WHERE id_cliente = ?";
        $arrData = array(
             $this->strNombre,
            $this->strIdentificacion,
            $this->strTelefono,
            $this->strEmail,
            $this->strDireccion,
            $this->intStatus,
            $this->intIdCliente
        );

        $request = $this->update($sql, $arrData);
    } else {
        // Se encontró a otro cliente con esos datos
        $request = "exist";
    }

    return $request;
}






}
?>