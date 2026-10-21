<?php
require_once(__DIR__ . "/../Libraries/Core/Pgsql.php");
class rolesModel extends Pgsql {

    public function __construct(){
       parent::__construct();
    }

  public function selectRoles()
  {
    $sql = "SELECT * FROM roles";
    $request = $this->select_all($sql);
    return $request;
  }






}

?>