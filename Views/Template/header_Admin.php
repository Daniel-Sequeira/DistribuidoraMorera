  <?php
/** @var array $data */
require_once("nav_Admin.php");
?>
  <!DOCTYPE html>
<html lang="en">
  <head>
    <meta name="description" content="Servicios logísticos y distribución de la zona norte Costa Rica">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Daniel Sequeira"
    <meta name="theme-color" content="#007bff">
    <title><?= $data['tag_page'] ?></title>
    <!-- Bootstrap 5 CSS CDN-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Main y Style personalizado CSS-->
    <link rel="stylesheet" type="text/css" href="<?= media();?>/css/main.css">
    <link rel="stylesheet" type="text/css" href="<?= media();?>/css/select2-bootstrap-5-theme.min.css">
      <link rel="stylesheet" type="text/css" href="<?= media();?>/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="<?= media();?>/css/style.css">
  </head>

  <body class="app sidebar-mini">
    <!-- Navbar-->
    <header class="app-header"><a class="app-header__logo" href="<?= base_url();?>/dashboard">Dist. Morera</a>
      <!-- Sidebar-->
      <a class="app-sidebar__toggle" href="#" data-toggle="sidebar" aria-label="Hide Sidebar"><i class="fa-solid fa-bars"></i></a>
      <!-- Navbar Right Menu-->
      <ul class="app-nav">
        <!-- User Menu-->
        <li class="dropdown"><a class="app-nav__item" href="#" data-bs-toggle="dropdown" aria-label="Open Profile Menu" aria-expanded="false"><i class="fa fa-user fa-lg"></i></a>
          <!-- BS5: dropdown-menu-right cambia a dropdown-menu-end -->
          <ul class="dropdown-menu settings-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="<?= base_url();?>/opciones"><i class="fa fa-cog fa-lg"></i> Opcioness</a></li>
            <li><a class="dropdown-item" href="<?= base_url();?>/perfil"><i class="fa fa-user fa-lg"></i> Perfil</a></li>
            <li><a class="dropdown-item" href="<?= base_url();?>/logout"><i class="fa fa-sign-out fa-lg"></i> Logout</a></li>
          </ul>
        </li>
      </ul>
    </header>
