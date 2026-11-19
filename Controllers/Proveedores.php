<?php
class Proveedores extends Controllers
{
    public function __construct()
    {
        sessionStart();
        session_regenerate_id(true);
        parent::__construct();
        if (empty($_SESSION['login'])) {
            header('Location: ' . base_url() . 'login');
            die();
        }
        // Ajusta el número de módulo según el ID que tengas registrado en tu tabla de módulos
        getPermisos(7);
    }

    public function Proveedores()
    {
        if (empty($_SESSION['permisosMod']['r'])) {
            header("Location:" . base_url() . 'dashboard');
            die();
        }
        $data['tag_page'] = "Proveedores";
        $data['page_title'] = "Proveedores";
        $data['page_name'] = "proveedores";
        $data['page_functions_js'] = "functions_proveedores.js";
        $this->views->getView($this, "proveedores", $data);
    }

    public function getProveedores()
    {
        if (!empty($_SESSION['permisosMod']['r'])) {
            $arrData = $this->model->selectProveedores();

            for ($i = 0; $i < count($arrData); $i++) {
                $btnView = '';
                $btnEdit = '';

                // Manejo de boolean en PostgreSQL (puede llegar como true, 't' o 1)
                if ($arrData[$i]['estado'] === true || $arrData[$i]['estado'] === 't' || $arrData[$i]['estado'] == 1) {
                    $arrData[$i]['estado'] = '<span class="badge bg-success">Activo</span>';
                } else {
                    $arrData[$i]['estado'] = '<span class="badge bg-danger">Inactivo</span>';
                }

                $arrData[$i]['telefono'] = !empty($arrData[$i]['telefono']) ? $arrData[$i]['telefono'] : '<span class="text-muted">N/A</span>';
                $arrData[$i]['correo']   = !empty($arrData[$i]['correo']) ? $arrData[$i]['correo'] : '<span class="text-muted">N/A</span>';

                if (!empty($_SESSION['permisosMod']['r'])) {
                    $btnView = '<button class="btn btn-info btn-sm btnViewProveedor" pr="' . $arrData[$i]['id_proveedor'] . '" title="Ver proveedor"><i class="far fa-eye"></i></button>';
                }
                if (!empty($_SESSION['permisosMod']['u'])) {
                    $btnEdit = '<button class="btn btn-primary btn-sm btnEditProveedor" pr="' . $arrData[$i]['id_proveedor'] . '" title="Editar proveedor"><i class="fas fa-pencil-alt"></i></button>';
                }

                $arrData[$i]['opciones'] = '<div class="text-center">' . $btnView . ' ' . $btnEdit . '</div>';
            }

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function getProveedor($idproveedor)
    {
        if (!empty($_SESSION['permisosMod']['r'])) {
            $intIdProveedor = intval($idproveedor);
            if ($intIdProveedor > 0) {
                $arrData = $this->model->selectProveedor($intIdProveedor);
                if (empty($arrData)) {
                    $arrResponse = array('status' => false, 'msg' => 'Datos no encontrados.');
                } else {
                    $arrResponse = array('status' => true, 'data' => $arrData);
                }
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
            }
        }
        die();
    }

    public function setProveedor()
    {
        if ($_POST) {
            if (empty($_POST['txtIdentificacion']) || empty($_POST['txtNombre'])) {
                $arrResponse = array("status" => false, "msg" => "La identificación y el nombre o razón social son obligatorios.");
            } else {
                $idProveedor    = intval($_POST['id_proveedor'] ?? 0);
                $strIdent       = strClean($_POST['txtIdentificacion']);
                $strNombre      = strClean($_POST['txtNombre']);
                $strTelefono    = strClean($_POST['txtTelefono'] ?? '');
                $strCorreo      = strtolower(strClean($_POST['txtCorreo'] ?? ''));
                $strDireccion   = strClean($_POST['txtDireccion'] ?? '');
                $intEstado      = intval($_POST['listStatus'] ?? 1);

                if ($idProveedor == 0) {
                    if (empty($_SESSION['permisosMod']['w'])) {
                        die();
                    }
                    $request = $this->model->insertProveedor(
                        $strNombre,
                        $strIdent,
                        $strTelefono,
                        $strCorreo,
                        $strDireccion,
                        $intEstado
                    );

                    if ($request > 0) {
                        $arrResponse = array('status' => true, 'msg' => 'Proveedor registrado correctamente.');
                    } elseif ($request === 'exist') {
                        $arrResponse = array('status' => false, 'msg' => 'Atención: Ya existe un proveedor con esa identificación.');
                    } else {
                        $arrResponse = array("status" => false, "msg" => "No fue posible almacenar los datos.");
                    }
                } else {
                    if (empty($_SESSION['permisosMod']['u'])) {
                        die();
                    }
                    $request = $this->model->updateProveedor(
                        $idProveedor,
                        $strNombre,
                        $strIdent,
                        $strTelefono,
                        $strCorreo,
                        $strDireccion,
                        $intEstado
                    );

                    if ($request > 0) {
                        $arrResponse = array('status' => true, 'msg' => 'Proveedor actualizado correctamente.');
                    } elseif ($request === 'exist') {
                        $arrResponse = array('status' => false, 'msg' => 'Atención: Esa identificación ya pertenece a otro proveedor.');
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
}
?>