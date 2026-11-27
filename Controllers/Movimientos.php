<?php
class Movimientos extends Controllers
{
    public function __construct()
    {
        parent::__construct();
        session_start();
        if (empty($_SESSION['login'])) {
            header('Location: ' . base_url() . 'login');
            die();
        }
        getPermisos(8); 
    }

    public function Movimientos()
    {
        if (empty($_SESSION['permisosMod']['r'])) {
            header("Location:" . base_url() . 'dashboard');
        }
        $data['tag_page'] = "Movimientos de Inventario";
        $data['page_title'] = "MOVIMIENTOS DE INVENTARIO";
        $data['page_name'] = "movimientos";
        $data['page_functions_js'] = "functions_movimientos.js";
        $this->views->getView($this, "movimientos", $data);
    }

    
     // renderizar los badges de estado
    private function getBadgeEstado(int $estado): string
    {
        $badges = [
            0 => '<span class="badge bg-secondary">Completado</span>',
            1 => '<span class="badge bg-warning text-dark">Recibido por ventas</span>',
            2 => '<span class="badge bg-primary">En Proceso (Bodega)</span>',
            3 => '<span class="badge bg-info text-dark">Enviado</span>',
            4 => '<span class="badge bg-success">Entregado</span>',
            5 => '<span class="badge bg-danger">Devolución Parcial</span>',
            6 => '<span class="badge bg-dark">Devolución Total</span>',
            7 => '<span class="badge bg-warning text-dark">Pendiente Devuelto</span>',
            8 => '<span class="badge bg-success">Devuelto</span>',
        ];
        return $badges[$estado] ?? '<span class="badge bg-light text-dark">Desconocido</span>';
    }

    
    //Obtener movimientos con estado y botones dinámicos según etapa del flujo
    public function getMovimientos()
    {
        header('Content-Type: application/json; charset=utf-8');
        if ($_SESSION['permisosMod']['r']) {
            $arrData =$this->model->selectMovimientos();

            for ($i = 0; $i < count($arrData); $i++) {
                $idMov  = $arrData[$i]['id_movimiento'];
                $estado = intval($arrData[$i]['estado']);
                $tipo   = trim($arrData[$i]['tipo_movimiento']);
                // Badge visual del estado del flujo
                $arrData[$i]['estado_badge'] = $this->getBadgeEstado($estado);

                // Visual para ENTRADA / SALIDA
                if ($tipo === 'ENTRADA') {
                    if (in_array($estado, [7, 8])) {
                        $arrData[$i]['tipo_movimiento'] = '<span class="badge bg-warning text-dark"><i class="fa fa-undo"></i> ENTRADA (DEV.)</span>';
                    } else {
                        $arrData[$i]['tipo_movimiento'] = '<span class="badge bg-success"><i class="fa fa-arrow-down"></i> ENTRADA</span>';
                    }
                    $arrData[$i]['monto_unitario'] = SMONEY . formatMoney($arrData[$i]['costo_unitario']);
                } else {
                    $arrData[$i]['tipo_movimiento'] = '<span class="badge bg-danger"><i class="fa fa-arrow-up"></i> SALIDA</span>';
                    $arrData[$i]['monto_unitario']  = SMONEY . formatMoney($arrData[$i]['precio_unitario']);
                }
                // Botón Ver 
                $botones = '<button class="btn btn-info btn-sm btnViewMovimiento" cl="' . $idMov . '" title="Ver detalle"><i class="fa fa-eye"></i></button> ';

                // Botones de acción según el estado de la SALIDA
                if ($tipo === 'SALIDA' && !empty($_SESSION['permisosMod']['u'])) {
                    if ($estado === 1) {
                        $botones .= '<button class="btn btn-warning btn-sm btnEditarListaVentas" cl="' . $arrData[$i]['id_cliente'] . '" title="Editar lista de ventas / Procesar"><i class="fa fa-pencil"></i></button> ';
                    } elseif ($estado === 2) {
                        $botones .= '<button class="btn btn-primary btn-sm btnCambiarEstado" cl="' . $idMov . '" estado="3" title="Marcar como Enviado"><i class="fas fa-truck"></i></button> ';
                    } elseif ($estado === 3) {
                        $botones .= '<button class="btn btn-success btn-sm btnCambiarEstado" cl="' . $idMov . '" estado="4" title="Marcar como Entregado"><i class="fa fa-check-circle"></i></button> ';
                    } elseif ($estado === 4) {
                        $botones .= '<button class="btn btn-danger btn-sm btnDevolucion" cl="' . $idMov . '" cant="' . $arrData[$i]['cantidad'] . '" title="Registrar Devolución"><i class="fas fa-undo"></i></button> ';
                    }
                }
                // Botón para confirmar estado DEVUELTO (reintegra stock) en ENTRADA pendiente de devolución
                if ($tipo === 'ENTRADA' && $estado === 7 && !empty($_SESSION['permisosMod']['u'])) {
                    $botones .= '<button class="btn btn-success btn-sm btnConfirmarDevuelto" cl="' . $idMov . '" cant="' . $arrData[$i]['cantidad'] . '" title="Pasar a Devuelto "><i class="fa fa-check-square-o"></i></button> ';
                }

                // Botón para confirmar Devolucion  
                if ($tipo === 'ENTRADA' && $estado === 7 && !empty($_SESSION['permisosMod']['u'])) {
                    $botones .= '<button class="btn btn-success btn-sm btnConfirmarDevuelto" cl="' . $idMov . '" cant="' . $arrData[$i]['cantidad'] . '" title="Confirmar estado Devuelto"><i class="fa fa-check-square-o"></i></button> ';
                }
                // imprime reporte dev
                if (in_array($estado, [5, 6, 7, 8])) {
                    $botones .= '<button class="btn btn-secondary btn-sm btnReporteDevolucion" cl="' . $idMov . '" title="Ver / Imprimir Reporte de Devolución"><i class="fa fa-file-text-o"></i></button> ';
                }

                $arrData[$i]['opciones'] = '<div class="text-center">' . $botones . '</div>';
            }
            echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    
     //Obtener un movimiento para el modal Ver
     
    public function getMovimiento($id_movimiento)
    {
        if ($_SESSION['permisosMod']['r']) {
            $intIdMovimiento = intval($id_movimiento);
            if ($intIdMovimiento > 0) {
                $arrData = $this->model->selectMovimiento($intIdMovimiento);
                if (empty($arrData)) {
                    $arrResponse = array('status' => false, 'msg' => 'Datos no encontrados.');
                } else {
                    $arrData['estado_badge']   = $this->getBadgeEstado(intval($arrData['estado']));
                    $arrData['costo_formato']  = SMONEY . formatMoney($arrData['costo_unitario']);
                    $arrData['precio_formato'] = SMONEY . formatMoney($arrData['precio_unitario']);
                    $subtotal = ($arrData['tipo_movimiento'] === 'ENTRADA')
                        ? ($arrData['cantidad'] * $arrData['costo_unitario'])
                        : ($arrData['cantidad'] * $arrData['precio_unitario']);
                    $arrData['total_formato']  = SMONEY . formatMoney($subtotal);

                    $arrResponse = array('status' => true, 'data' => $arrData);
                }
                echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
            }
        }
        die();
    }

    
    //Registrar nueva ENTRADA (Estado 0) o agregar ítem a SALIDA en Ventas (Estado 1 - Congelado)
     
    public function setMovimiento()
    {
        if ($_POST) {
            if (empty($_SESSION['permisosMod']['w'])) {
                echo json_encode(['status' => false, 'msg' => 'No tienes permisos para registrar movimientos.'], JSON_UNESCAPED_UNICODE);
                die();
            }

            $strTipoMovimiento = strtoupper(strClean($_POST['listTipoMovimiento'] ?? ''));
            $intIdProducto     = intval($_POST['listProducto'] ?? 0);
            $intIdLote         = intval($_POST['listLote'] ?? 0);
            $intCantidad       = intval($_POST['txtCantidad'] ?? 0);
            $decCostoUnitario  = floatval($_POST['txtCostoUnitario'] ?? 0);
            $decPrecioUnitario = floatval($_POST['txtPrecioUnitario'] ?? 0);
            $strMotivo         = strClean($_POST['txtMotivo'] ?? '');
            $intIdCliente      = !empty($_POST['listCliente']) ? intval($_POST['listCliente']) : null;
            $intIdUsuario      = intval($_SESSION['idUser']);

            if (!in_array($strTipoMovimiento, ['ENTRADA', 'SALIDA'])) {
                echo json_encode(['status' => false, 'msg' => 'Tipo de movimiento inválido.'], JSON_UNESCAPED_UNICODE);
                die();
            }
            if ($intIdProducto <= 0 || $intIdLote <= 0) {
                echo json_encode(['status' => false, 'msg' => 'Debe seleccionar un producto y un lote válido.'], JSON_UNESCAPED_UNICODE);
                die();
            }
            if ($intCantidad <= 0) {
                echo json_encode(['status' => false, 'msg' => 'La cantidad debe ser mayor a 0.'], JSON_UNESCAPED_UNICODE);
                die();
            }

            if ($strTipoMovimiento === 'ENTRADA') {
                $request = $this->model->insertMovimientoEntrada(
                    $intIdProducto,
                    $intIdLote,
                    $intCantidad,
                    $decCostoUnitario,
                    $strMotivo,
                    $intIdUsuario
                );

                if ($request === "lote_invalido") {
                    $arrResponse = ['status' => false, 'msg' => 'El lote seleccionado no pertenece al producto.'];
                } elseif ($request > 0) {
                    $arrResponse = ['status' => true, 'tipo' => 'ENTRADA', 'msg' => 'Entrada de inventario registrada correctamente.'];
                } else {
                    $arrResponse = ['status' => false, 'msg' => 'No es posible almacenar la entrada.'];
                }
            } else {
                if (empty($intIdCliente)) {
                    echo json_encode(['status' => false, 'msg' => 'Para una SALIDA es obligatorio seleccionar un cliente.'], JSON_UNESCAPED_UNICODE);
                    die();
                }

                $request = $this->model->insertItemSalidaVentas(
                    $intIdProducto,
                    $intIdLote,
                    $intCantidad,
                    $decPrecioUnitario,
                    $strMotivo,
                    $intIdUsuario,
                    $intIdCliente
                );

                if ($request === "stock_insuficiente") {
                    $arrResponse = ['status' => false, 'msg' => 'Stock disponible insuficiente (considere las unidades ya congeladas).'];
                } elseif ($request === "lote_invalido") {
                    $arrResponse = ['status' => false, 'msg' => 'El lote seleccionado no pertenece al producto o está inactivo.'];
                } elseif ($request > 0) {
                    $arrResponse = ['status' => true, 'tipo' => 'SALIDA', 'msg' => 'Artículo agregado a la lista y cantidad congelada.'];
                } else {
                    $arrResponse = ['status' => false, 'msg' => 'No es posible agregar el artículo a la lista.'];
                }
            }

            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    
    //Obtener artículos en estado 1 (Recibido por ventas) de un cliente
    
    public function getListaVentas($id_cliente)
    {
        if ($_SESSION['permisosMod']['r']) {
            $intIdCliente = intval($id_cliente);
            $lista = ($intIdCliente > 0) ? $this->model->selectListaVentasCliente($intIdCliente) : [];
            echo json_encode($lista, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    
     //Editar cantidad de un ítem en estado 1
    public function editItemVentas()
    {
        if ($_POST) {
            if (empty($_SESSION['permisosMod']['u'])) {
                echo json_encode(['status' => false, 'msg' => 'No tienes permisos para editar cantidades.'], JSON_UNESCAPED_UNICODE);
                die();
            }

            $intIdMov    = intval($_POST['id_movimiento'] ?? 0);
            $intCantidad = intval($_POST['cantidad'] ?? 0);

            $res =$this->model->updateCantidadSalidaVentas($intIdMov,$intCantidad);

            if ($res === "ok") {
                $arrResponse = ['status' => true, 'msg' => 'Cantidad actualizada y reserva ajustada.'];
            } elseif ($res === "stock_insuficiente") {
                $arrResponse = ['status' => false, 'msg' => 'No hay suficiente stock libre en el lote para esa cantidad.'];
            } elseif ($res === "cantidad_invalida") {
                $arrResponse = ['status' => false, 'msg' => 'La cantidad debe ser mayor a 0.'];
            } else {
                $arrResponse = ['status' => false, 'msg' => 'El ítem ya fue procesado o no se pudo actualizar.'];
            }
              echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
     }
        die();
    }
    
     //Llenar selects iniciales del modal (Productos y Clientes activos)
     
    public function getSelectsIniciales()
    {
        if ($_SESSION['permisosMod']['r']) {
            $arrProductos = $this->model->selectProductosActivos();
            $arrClientes  = $this->model->selectClientesActivos();

            $arrResponse = [
                'productos' => $arrProductos,
                'clientes'  => $arrClientes
            ];
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    
     //Obtener lotes disponibles de un producto seleccionado
    public function getLotesProducto($id_producto)
    {
        if ($_SESSION['permisosMod']['r']) {
            $intIdProducto = intval($id_producto);
            $arrLotes = ($intIdProducto > 0) ? $this->model->selectLotesPorProducto($intIdProducto) : [];
            echo json_encode($arrLotes, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

   
    //Quitar ítem de la lista de ventas (Estado 1)
    public function delItemVentas()
    {
        if ($_POST) {
            if (empty($_SESSION['permisosMod']['d'])) {
                echo json_encode(['status' => false, 'msg' => 'No tienes permisos para eliminar ítems.'], JSON_UNESCAPED_UNICODE);
                die();
            }
            $intIdMov = intval($_POST['id_movimiento'] ?? 0);
            $request  = $this->model->deleteItemSalidaVentas($intIdMov);

            $arrResponse = $request
                ? ['status' => true, 'msg' => 'Artículo eliminado y reserva liberada.']
                : ['status' => false, 'msg' => 'No se pudo eliminar el artículo.'];

            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    
    //Procesar pedido de ventas (Estado 1 -> Estado 2)
    public function procesarVentas()
    {
        if ($_POST) {
            if (empty($_SESSION['permisosMod']['u'])) {
                echo json_encode(['status' => false, 'msg' => 'No tienes permisos para procesar pedidos.'], JSON_UNESCAPED_UNICODE);
                die();
            }
            $intIdCliente = intval($_POST['id_cliente'] ?? 0);
            $request      = $this->model->procesarPedidoVentas($intIdCliente);

            $arrResponse = $request
                ? ['status' => true, 'msg' => 'Pedido enviado a bodega correctamente.']
                : ['status' => false, 'msg' => 'No se pudo procesar el pedido.'];

            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    
     //Avanzar estado de bodega 2 -> 3 Enviado  3 -> 4 Entregado 7 -> 8 Devuelto sin sumar al stock 
    
    public function cambiarEstado()
    {
        if ($_POST) {
            if (empty($_SESSION['permisosMod']['u'])) {
                echo json_encode(['status' => false, 'msg' => 'No tienes permisos para actualizar el estado.'], JSON_UNESCAPED_UNICODE);
                die();
            }
            $intIdMov    = intval($_POST['id_movimiento'] ?? 0);
            $nuevoEstado = intval($_POST['estado'] ?? 0);
            $request     = $this->model->avanzarEstadoMovimiento($intIdMov, $nuevoEstado);

            if ($request) {
                $msg = 'Estado actualizado correctamente.';
                if ($nuevoEstado === 3) {
                    $msg = 'Pedido marcado como ENVIADO y descontado del stock físico.';
                } elseif ($nuevoEstado === 4) {
                    $msg = 'Pedido marcado como ENTREGADO al cliente.';
                } elseif ($nuevoEstado === 8) {
                    $msg = 'Devolución completada en estado DEVUELTO (sin suma al stock disponible).';
                }
                $arrResponse = ['status' => true, 'msg' => $msg];
            } else {
                $arrResponse = ['status' => false, 'msg' => 'No fue posible actualizar el estado.'];
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

   
     //Registrar devolución parcial o total (Estados 5 y 6)
    public function setDevolucion()
    {
        if ($_POST) {
            if (empty($_SESSION['permisosMod']['u'])) {
                echo json_encode(['status' => false, 'msg' => 'No tienes permisos para registrar devoluciones.'], JSON_UNESCAPED_UNICODE);
                die();
            }
            $intIdMov    = intval($_POST['id_movimiento_dev'] ?? 0);
            $intCantidad = intval($_POST['txtCantidadDevolver'] ?? 0);
            $strMotivo   = strClean($_POST['txtMotivoDevolucion'] ?? '');
            $intUsuario  = intval($_SESSION['idUser']);

            $res = $this->model->registrarDevolucion($intIdMov, $intCantidad, $strMotivo, $intUsuario);

            if (is_array($res) && $res['status'] === "ok") {
                $reporte = $this->model->selectReporteDevolucion(intval($res['id_devolucion']));
                if (!empty($reporte)) {
                    $reporte['precio_formato'] = SMONEY . formatMoney($reporte['precio_unitario']);
                    $reporte['total_formato']  = SMONEY . formatMoney($reporte['monto_devuelto']);
                }
                $arrResponse = [
                    'status'  => true,
                    'msg'     => 'Devolución registrada. Confirme la recepción física en bodega para completar el estado Devuelto.',
                    'reporte' => $reporte
                ];
            } elseif ($res === "cantidad_invalida") {
                $arrResponse = ['status' => false, 'msg' => 'La cantidad a devolver debe estar entre 1 y el total entregado.'];
            } elseif ($res === "estado_invalido") {
                $arrResponse = ['status' => false, 'msg' => 'Solo se pueden devolver movimientos en estado Entregado.'];
            } else {
                $arrResponse = ['status' => false, 'msg' => 'No se pudo registrar la devolución.'];
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    //Datos para reporte devolucion
    public function getReporteDevolucion($id_movimiento)
    {
        if ($_SESSION['permisosMod']['r']) {
            $intIdMov = intval($id_movimiento);
            if ($intIdMov > 0) {
                $reporte = $this->model->selectReporteDevolucion($intIdMov);
                if (empty($reporte)) {
                    $arrResponse = ['status' => false, 'msg' => 'No se encontró reporte de devolución para este movimiento.'];
                } else {
                    $reporte['precio_formato'] = SMONEY . formatMoney($reporte['precio_unitario']);
                    $reporte['total_formato']  = SMONEY . formatMoney($reporte['monto_devuelto']);
                    $arrResponse = ['status' => true, 'reporte' => $reporte];
                }
                echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
            }
        }
        die();
    }

 } 
 ?>    