<?php

/** @var array $data */
headerAdmin($data);
getModal('modalUsuarios', $data);
?>
<main class="app-content">
  <div class="app-title">
    <div>
      <h1><i class="fas fa-user-tag"> </i> <?= $data['page_title'] ?>
        <button class="btn btn-primary" type="button" onclick="openModal();">
          <i class="fa-solid fa-circle-plus"></i> Nuevo</button>
      </h1>
    </div>
    <ul class="app-breadcrumb breadcrumb">
      <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
      <li class="breadcrumb-item"><a href="<?= base_url(); ?>usuarios"><?= $data['page_title'] ?></a></li>
    </ul>
  </div>
  
  <div class="row">
    <div class="col-md-12">
      <div class="tile">
        <div class="tile-body">
          <div class="table-responsive">
            <table class="table table-hover table-bordered" id="tableUsuarios">
              <thead>
                <tr>
                  <th>Id_usuario</th>
                  <th>Id_rol</th>
                  <th>Identificacion</th>
                  <th>Nombre_completo</th>
                  <th>Correo</th>
                  <th>Estado</th>
                  <th>Editar</th>
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