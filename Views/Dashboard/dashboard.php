<?php

/** @var array $data */
headerAdmin($data);
?>
<style>
  .app-content {
    position: relative;
    min-height: 100vh;
  }

  .app-content::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;

    background-image: url('<?= media(); ?>/images/DMLogo.png');
    background-repeat: no-repeat;
    background-position: center center;
    background-size: 40%;
    opacity: 0.15;
    z-index: 99;
    pointer-events: none;
  }
</style>

<main class="app-content">
  <div class="app-title">
    <div>
      <h1><i class="fa fa-dashboard"></i> Distribuidora Morera</h1>

    </div>
    <ul class="app-breadcrumb breadcrumb">
      <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
      <li class="breadcrumb-item"><a href="<?= base_url(); ?>dashboard">Dashboard</a></li>
    </ul>
  </div>
  <!-- Area de widgets -->
  <div class="row">
    <?php if (!empty($_SESSION['permisos'][1]['r'])) { ?>
      <div class="col-md-6 col-lg-3">
        <a href="<?= base_url() ?>/usuarios" class="linkw">
          <div class="widget-small primary coloured-icon"><i class="icon fa fa-users fa-3x"></i>
            <div class="info">
              <h4>Usuarios</h4>
              <p><b><?= $data['usuarios'] ?></b></p>
            </div>
          </div>
        </a>
      </div>
    <?php } ?>
    <?php if (!empty($_SESSION['permisos'][4]['r'])) { ?>
      <div class="col-md-6 col-lg-3">
        <a href="<?= base_url() ?>/clientes" class="linkw">
          <div class="widget-small info coloured-icon"><i class="icon fa fa-user-o fa-3x"></i>
            <div class="info">
              <h4>Clientes</h4>
              <p><b><?= $data['clientes'] ?></b></p>
            </div>
          </div>
        </a>
      </div>
    <?php } ?>
    <?php if (!empty($_SESSION['permisos'][5]['r'])) { ?>
      <div class="col-md-6 col-lg-3">
        <a href="<?= base_url() ?>/productos" class="linkw">
          <div class="widget-small warning coloured-icon"><i class="icon fa fa-archive fa-3x"></i>
            <div class="info">
              <h4>Productos</h4>
              <p><b><?= $data['productos'] ?></b></p>
            </div>
          </div>
        </a>
      </div>
    <?php } ?>
    <?php if (!empty($_SESSION['permisos'][8]['r'])) { ?>
      <div class="col-md-6 col-lg-3">
        <a href="<?= base_url() ?>/movimientos" class="linkw">
          <div class="widget-small danger coloured-icon"><i class="icon fa fa-shopping-cart fa-3x"></i>
            <div class="info">
              <h4>Movimientos pendientes</h4>
              <p><b><?= $data['movimientos'] ?></b></p>
            </div>
          </div>
        </a>
      </div>
    <?php } ?>
  </div>
  <div class="row">
    <?php if (!empty($_SESSION['permisos'][8]['r'])) { ?>
      <div class="col-md-6">
        <div class="tile">
          <h3 class="tile-title">Movimientos Recientes</h3>
          <table class="table table-striped table-sm">
            <thead>
              <tr>
                <th>#</th>
                <th>Cliente</th>
                <th>Estado</th>
                <th>Usuario</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if (count($data['ultimosMovimientos']) > 0) {
                foreach ($data['ultimosMovimientos'] as $movimiento) {
              ?>
                  <tr>
                    <td><?= $movimiento['id_movimiento'] ?></td>
                    <td><?= $movimiento['nombre_razon_social'] ?></td>
                    <td><?= $movimiento['estado_badge'] ?></td>
                     <td><?= $movimiento['nombre_completo'] ?></td>
                  </tr>
              <?php }
              } ?>
            </tbody>
          </table>
        </div>
      </div>
    <?php } ?>
  </div>

</main>
<?php footerAdmin($data); ?>