<?php
/** @var array $data */
headerAdmin($data);
getModal('modalClientes', $data);
?>
<main class="app-content">
  <div class="app-title">
    <div>
      <h1><i class="fas fa-user-tag"> </i> <?= $data['page_title'] ?>
      <?php if(!EMPTY($_SESSION['permisosMod']['w'])){ ?>
        <button class="btn btn-primary" type="button" onclick="openModal();">
          <i class="fa-solid fa-circle-plus"></i> Nuevo</button>
      <?php } ?>  
      </h1>
    </div>
    <ul class="app-breadcrumb breadcrumb">
      <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
      <li class="breadcrumb-item"><a href="<?= base_url(); ?>clientes"><?= $data['page_title'] ?></a></li>
    </ul>
  </div>
  
  <div class="row">
    <div class="col-md-12">
      <div class="tile">
        <div class="tile-body">
          <div class="table-responsive">
            <table class="table table-striped table-bordered" id="tableClientes">
              <thead>
                <tr>
                  <th>Id_cliente</th>
                  <th>Razón_Social</th>
                  <th>Identificacion</th>
                  <th>Teléfono</th>
                  <th>Correo</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr>
        
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>
<?php footerAdmin($data); ?>