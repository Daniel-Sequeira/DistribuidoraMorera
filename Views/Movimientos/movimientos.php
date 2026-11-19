<?php 
/** @var array $data */
    headerAdmin($data); 
    getModal('modalMovimientos', $data);
?>
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="fa fa-exchange"></i> <?= $data['page_title'] ?>
                <?php if (!empty($_SESSION['permisosMod']['w'])) { ?>
                    <button class="btn btn-primary" type="button" onclick="openModal();"><i class="fa fa-plus-circle"></i> Nuevo Movimiento</button>
                <?php } ?>
            </h1>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?= base_url(); ?>movimientos"><?= $data['page_tag'] ?></a></li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body">
                    <!-- Filtro rápido por Tipo de Movimiento -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="filtroTipoMovimiento" class="form-label fw-bold">Filtrar por Tipo:</label>
                            <select id="filtroTipoMovimiento" class="form-select">
                                <option value="">Todos los movimientos</option>
                                <option value="ENTRADA">ENTRADA</option>
                                <option value="SALIDA">SALIDA</option>
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="tableMovimientos" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Fecha</th>
                                    <th>Tipo</th>
                                    <th>Producto</th>
                                    <th>Lote</th>
                                    <th>Cantidad</th>
                                    <th>Costo / Precio</th>
                                    <th>Cliente</th>
                                    <th>Estado</th>
                                    <th>Usuario</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php footerAdmin($data); ?>