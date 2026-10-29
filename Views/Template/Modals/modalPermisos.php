<?php

/** @var array $data */

?>

<!-- Modal de Permisos en Bootstrap 5 -->
<div class="modal fade" id="modalPermisos" tabindex="-1" aria-labelledby="modalPermisosLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPermisosLabel">Gestión de Permisos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- CONTENIDO FORMULARIO -->
            <form id="formPermisos" name="formPermisos">
                <input type="hidden" id="idrol" name="idrol" value="<?= $data['id_rol']; ?>" required="">
                
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Módulo</th>
                                    <th>Leer</th>
                                    <th>Escribir</th>
                                    <th>Actualizar</th>
                                    <th>Eliminar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                $modulos = $data['modulos'];
                                for ($i = 0; $i < count($modulos); $i++) {
                                    $permisos = $modulos[$i]['permisos'];
                                    $rCheck = $permisos['r'] == 1 ? "checked" : "";
                                    $wCheck = $permisos['w'] == 1 ? "checked" : "";
                                    $uCheck = $permisos['u'] == 1 ? "checked" : "";
                                    $dCheck = $permisos['d'] == 1 ? "checked" : "";
                                    $idmod  = $modulos[$i]['id_modulo'];
                                    $nombreModulo = isset($modulos[$i]['titulo']) ? $modulos[$i]['titulo'] : 'Módulo ' . $idmod;
                                ?>
                                    <tr>
                                        <td>
                                            <?= $no; ?>
                                            <input type="hidden" name="modulos[<?= $i; ?>][id_modulo]" value="<?= $idmod ?>" required="">
                                        </td>
                                        <td><?= $nombreModulo; ?></td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" role="switch" name="modulos[<?= $i; ?>][r]" <?= $rCheck; ?>>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" role="switch" name="modulos[<?= $i; ?>][w]" <?= $wCheck; ?>>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" role="switch" name="modulos[<?= $i; ?>][u]" <?= $uCheck; ?>>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" role="switch" name="modulos[<?= $i; ?>][d]" <?= $dCheck; ?>>
                                            </div>
                                        </td>
                                    </tr>
                                <?php
                                    $no++;
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardarPermisos">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>