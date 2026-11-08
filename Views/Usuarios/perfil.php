<?php 
/** @var array $data */
 headerAdmin($data);
 getModal('modalPerfil',$data);
?>
<link rel="stylesheet" type="text/css" href="<?= media(); ?>css/style.css">
 <main class="app-content">
  </div>
  <div class="row user">
    <div class="col-md-12">
      <!-- Clase custom-profile-bg y el background-imagen vecteezy -->
      <div class="profile custom-profile-bg" style="background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('<?= media();?>images/vecteezy_vehicle.jpeg');">
        
        <div class="info">
          <img class="user-img" src="<?= media();?>images/avatar.png" alt="Avatar">
          <div class="user-details">
            <h4><?= $_SESSION['userData']['nombre_completo'];?></h4>
            <p><?=$_SESSION['userData']['nombre_rol'];?></p>
          </div>
        </div>
      </div>
    </div>
    
    <div class="col-md-3">
      <div class="tile p-0">
        <ul class="nav flex-column nav-tabs user-tabs">
          <li class="nav-item"><a class="nav-link active" href="#user-timeline" data-toggle="tab">Datos personales</a></li>
        </ul>
      </div>
    </div>
    <div class="row mb-3">
    <div class="col-md-12 text-right">
      <a href="<?= base_url(); ?>dashboard" class="btn btn-secondary">
        <i class="fas fa-arrow-left mr-2"></i> Regresar
      </a>
    </div>
    
    <div class="col-md-9">
      <div class="tab-content">
        <div class="tab-pane active" id="user-timeline">
          <div class="timeline-post">
            <div class="post-media">
              <div class="content">
                <h5>Datos Personales 
                  <button class="btn btn-sm btn-info" type="button" onclick="openModalPerfil();">
                    <i class="fas fa-pencil-alt" aria-hidden="true"></i>
                  </button>
                </h5>
              </div>
            </div>
            <table class="table table-bordered">
              <tbody>
                <tr>
                  <td style="width:150px;">Identificación:</td>
                  <td><?= $_SESSION['userData']['identificacion']; ?></td> 
                </tr>
                <tr>
                  <td>Nombre completo</td>
                  <td><?= $_SESSION['userData']['nombre_completo']; ?></td>
                </tr>
                <tr>
                  <td>Email</td>
                  <td><?= $_SESSION['userData']['correo']; ?></td>
                </tr>
                <tr>
                  <td>Tipo Usuario</td>
                  <td><?= $_SESSION['userData']['nombre_rol']; ?></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>
<?php footerAdmin($data);?>
    