
<?php
class Productos extends Controllers
{
    public function __construct()
    {
        sessionStart();
        parent::__construct();
        if (empty($_SESSION['login'])) {
            header('Location: ' . base_url() . 'login');
            die();
        }
        getPermisos(5);
    }

    public function Productos()
    {
        if (empty($_SESSION['permisosMod']['r'])) {
            header("Location:" . base_url() . 'dashboard');
            die();
        }
        $data['tag_page'] = "Productos";
        $data['page_title'] = "Productos";
        $data['page_name'] = "productos";
        $data['page_functions_js'] = "functions_productos.js";
        $this->views->getView($this, "productos", $data);
    }

    public function getProductos()
    {
        if (!empty($_SESSION['permisosMod']['r'])) {
            $arrData = $this->model->selectProductos();

            for ($i = 0; $i < count($arrData); $i++) {
                $btnView = '';
                $btnEdit = '';

                // Formato de moneda
                $arrData[$i]['precio'] = '$' . number_format((float)$arrData[$i]['precio'], 2, '.', ',');

                // Etiqueta de estado (debe decir exactamente "Activo" o "Inactivo" para el filtro)
                if ($arrData[$i]['estado'] == 1 || $arrData[$i]['estado'] === true) {
                    $arrData[$i]['estado'] = '<span class="badge bg-success">Activo</span>';
                } else {
                    $arrData[$i]['estado'] = '<span class="badge bg-danger">Inactivo</span>';
                }

                // Botones de acción
                if (!empty($_SESSION['permisosMod']['r'])) {
                    $btnView = '<button class="btn btn-info btn-sm btnViewProducto" pr="' . $arrData[$i]['id_producto'] . '" title="Ver detalle y lotes"><i class="far fa-eye"></i></button>';
                }
                if (!empty($_SESSION['permisosMod']['u'])) {
                    $btnEdit = '<button class="btn btn-primary btn-sm btnEditProducto" pr="' . $arrData[$i]['id_producto'] . '" title="Editar producto"><i class="fas fa-pencil-alt"></i></button>';
                }

                $arrData[$i]['opciones'] = '<div class="text-center">' . $btnView . ' ' . $btnEdit . '</div>';
            }

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function getProducto($idproducto)
    {
        if (!empty($_SESSION['permisosMod']['r'])) {
            $intIdProducto = intval($idproducto);
            if ($intIdProducto > 0) {
                $arrData = $this->model->selectProducto($intIdProducto);
                if (empty($arrData)) {
                    $arrResponse = array('status' => false, 'msg' => 'Datos no encontrados.');
                } else {
                    // Adjuntar lotes activos ordenados por FIFO
                    $arrData['lotes'] = $this->model->selectLotesActivos($intIdProducto);
                    $arrResponse = array('status' => true, 'data' => $arrData);
                }
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
            }
        }
        die();
    }

    public function setProducto()
    {
        if ($_POST) {
            if (empty($_POST['txtNombre']) || empty($_POST['listProveedor']) || empty($_POST['listEstanteria']) || empty($_POST['txtCodigo']) || empty($_POST['txtPrecio'])) {
                $arrResponse = array("status" => false, "msg" => "Datos incompletos en el formulario.");
            } else {
                $idProducto   = intval($_POST['id_producto'] ?? 0);
                $strNombre    = strClean($_POST['txtNombre']);
                $strDesc      = strClean($_POST['txtDescripcion'] ?? '');
                $intProveedor = intval($_POST['listProveedor']);
                $intEstante   = intval($_POST['listEstanteria']);
                $strCodigo    = strClean($_POST['txtCodigo']);
                $decPrecio    = floatval($_POST['txtPrecio']);
                $intEstado    = intval($_POST['listStatus']);
                $idUsuario    = intval($_SESSION['idUser'] ?? $_SESSION['userData']['id_usuario'] ?? 1);

                if ($idProducto == 0) {
                    // CREAR PRODUCTO NUEVO + LOTE INICIAL + MOVIMIENTO ENTRADA
                    if (empty($_SESSION['permisosMod']['w'])) {
                        die();
                    }
                    $strLote  = strClean($_POST['txtLote'] ?? '');
                    $decCosto = floatval($_POST['txtCosto'] ?? 0);
                    $intStock = intval($_POST['txtStock'] ?? 0);

                    if (empty($strLote) || $intStock <= 0) {
                        $arrResponse = array("status" => false, "msg" => "Debe indicar un código de lote y cantidad inicial mayor a 0.");
                    } else {
                        $request = $this->model->insertProductoFIFO(
                            $intEstante,
                            $intProveedor,
                            $strCodigo,
                            $strNombre,
                            $strDesc,
                            $decPrecio,
                            $intStock,
                            $intEstado,
                            $strLote,
                            $decCosto,
                            $idUsuario
                        );

                        if ($request > 0) {
                            $arrResponse = array('status' => true, 'msg' => 'Producto y lote inicial registrados correctamente.');
                        } elseif ($request === 'exist') {
                            $arrResponse = array('status' => false, 'msg' => 'Atención: Ya existe un producto con ese código.');
                        } else {
                            $arrResponse = array("status" => false, "msg" => "No fue posible almacenar los datos.");
                        }
                    }
                } else {
                    // ACTUALIZAR DATOS DEL CATÁLOGO Y PRECIO VIGENTE (sin tocar lotes ni stock)
                    if (empty($_SESSION['permisosMod']['u'])) {
                        die();
                    }
                    $request = $this->model->updateProducto(
                        $idProducto,
                        $intEstante,
                        $intProveedor,
                        $strCodigo,
                        $strNombre,
                        $strDesc,
                        $decPrecio,
                        $intEstado
                    );

                    if ($request > 0) {
                        $arrResponse = array('status' => true, 'msg' => 'Producto actualizado correctamente.');
                    } elseif ($request === 'exist') {
                        $arrResponse = array('status' => false, 'msg' => 'Atención: Ese código ya pertenece a otro producto.');
                    } else {
                        $arrResponse = array("status" => false, "msg" => "No fue posible actualizar los datos.");
                    }
                }
            }
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function getSelectProveedores()
    {
        $htmlOptions = '<option value="">Seleccione un proveedor...</option>';
        $arrData = $this->model->selectProveedoresActivos();
        if (count($arrData) > 0) {
            for ($i = 0; $i < count($arrData); $i++) {
                $htmlOptions .= '<option value="' . $arrData[$i]['id_proveedor'] . '">' . $arrData[$i]['nombre_razon_social'] . '</option>';
            }
        }
        echo $htmlOptions;
        die();
    }

   public function getSelectEstanterias()
    {
        $htmlOptions = '<option value="">Seleccione una estantería...</option>';
        $arrData = $this->model->selectEstanteriasActivas();
        if (!empty($arrData) && count($arrData) > 0) {
            for ($i = 0; $i < count($arrData); $i++) {
                $desc = !empty($arrData[$i]['descripcion']) ? ' - ' . $arrData[$i]['descripcion'] : '';
                $htmlOptions .= '<option value="' . $arrData[$i]['id_estanteria'] . '">' . $arrData[$i]['codigo_estanteria'] . $desc . '</option>';
            }
        }
        echo $htmlOptions;
        die();
    }
}
?>

