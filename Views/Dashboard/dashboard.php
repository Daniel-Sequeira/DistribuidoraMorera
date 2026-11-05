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
          <h1><i class="fa fa-dashboard"></i> Blank Page</h1>
          <p>Distribuidora Morera</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
          <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
          <li class="breadcrumb-item"><a href="#">Blank Page</a></li>
        </ul>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="tile">
            <div class="tile-body"><?= $data['page_title'] ?></div>
            <?php dep($_SESSION['userData']);
                  dep($_SESSION['permisos']);
                  dep($_SESSION['permisosMod']);
            ?>
          </div>
        </div>
      </div>
    </main>
<?php footerAdmin($data);?>
   