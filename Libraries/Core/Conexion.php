<?php

	class Conexion{
		
		private $conect;

		public function __construct(){
			$connectionString = "pgsql:host=" . DB_HOST . ";dbname=" . DB_NAME;
            try{
				$this->conect = new PDO($connectionString,DB_USER, DB_PASS);
				$this->conect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			}catch(PDOException $e){
				$this->conect = null;
			    echo "ERROR CONEXION " . $e->getMessage();
			}
		}
		
		public function connect(){
			return $this->conect;
		}
	}

?>