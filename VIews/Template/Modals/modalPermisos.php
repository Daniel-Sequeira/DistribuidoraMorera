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
                            
                            // El ciclo for debe envolver el <tr>
                            for ($i = 0; $i < count($modulos); $i++) {
                                $permisos = $modulos[$i]['permisos'];
                                $rCheck = $permisos['r'] == 1 ? "checked" : "";
                                $wCheck = $permisos['w'] == 1 ? "checked" : "";
                                $uCheck = $permisos['u'] == 1 ? "checked" : "";
                                $dCheck = $permisos['d'] == 1 ? "checked" : "";
                                $idmod  = $modulos[$i]['id_modulo'];
                                // Supongo que tienes el nombre del módulo en tu array, ej: $modulos[$i]['nombre']
                                $nombreModulo = isset($modulos[$i]['nombre']) ? $modulos[$i]['nombre'] : 'Módulo '.$idmod;
                            ?>
                                <tr>
                                    <td><?= $no; ?></td>
                                    <td><?= $nombreModulo; ?></td>
                                    <!-- Aquí se imprimen los checkboxes -->
                                    <td><input type="checkbox" name="modulos[<?= $i; ?>][r]" <?= $rCheck; ?>></td>
                                    <td><input type="checkbox" name="modulos[<?= $i; ?>][w]" <?= $wCheck; ?>></td>
                                    <td><input type="checkbox" name="modulos[<?= $i; ?>][u]" <?= $uCheck; ?>></td>
                                    <td><input type="checkbox" name="modulos[<?= $i; ?>][d]" <?= $dCheck; ?>></td>
                                </tr>
                            <?php
                                $no++;
                            } // Fin del ciclo for
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarPermisos">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>