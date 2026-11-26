<?php

class Dashboard extends Controllers{

public function __construct(){
   sessionStart();
   parent::__construct();
   //session_start();
   //session_regenerate_id(true);
   if(empty($_SESSION['login'])){
      header('location: '.base_url().'login');
   }  
   getPermisos(1);
}

public function dashboard()
{
   $data['page_id'] = 2;
   $data['tag_page'] = "Dashboard Distribuidora Morera";
   $data['page_title'] = "Dashboard Distribuidora Morera";
   $data['page_name'] = "dashboard";
   $data['page_functions_js'] = "functions_dashboard.js";
   $data['usuarios'] = $this->model->cantUsuarios();
   $data['clientes'] = $this->model->cantClientes();
   $data['productos'] = $this->model->cantProductos();
   $data['movimientos'] = $this->model->cantMovimientosPend();
   $data['ultimosMovimientos'] = $this->model->ultimosMovimientos();
   $data['productosAlerta'] = $this->model->selectProductosAlerta();
  
   $anio = date('Y');
   $mes = date ('m');
   $data['tipoMovMes'] = $this-> model->selectTipoMovMes($anio,$mes);
   $data['versusMov'] = $this->model->selectVersusDia($anio, $mes);

  
   $this->views->getView($this,"dashboard",$data);
}

public function getProductosAlerta()
{
    if (!empty($_SESSION['permisos'][5]['r'])) {
        $arrData = $this->model->selectProductosAlerta();
        $arrResponse = [
            'status' => true,
            'data'   => $arrData
        ];
    } else {
        $arrResponse = [
            'status' => false,
            'msg'    => 'Sin permisos para consultar productos.'
        ];
    }

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
    die();
}

public function getLotesProducto($idProducto)
{
    if (empty($_SESSION['permisos'][5]['r'])) {
        $arrResponse = ['status' => false, 'msg' => 'Sin permisos para consultar lotes.'];
    } else {
        $intIdProducto = intval($idProducto);
        if ($intIdProducto <= 0) {
            $arrResponse = ['status' => false, 'msg' => 'ID de producto no válido.'];
        } else {
            $producto = $this->model->selectProductoInfo($intIdProducto);
            if (empty($producto)) {
                $arrResponse = ['status' => false, 'msg' => 'Producto no encontrado.'];
            } else {
                $lotes = $this->model->selectLotesProducto($intIdProducto);
                $arrResponse = [
                    'status'   => true,
                    'producto' => $producto,
                    'lotes'    => $lotes
                ];
            }
        }
    }

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
    die();
}


}

?>