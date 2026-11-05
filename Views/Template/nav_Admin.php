 <!-- Sidebar menu-->
 <div class="app-sidebar__overlay" data-toggle="sidebar"></div>
 <aside class="app-sidebar">
     <div class="app-sidebar__user"><img class="app-sidebar__user-avatar" src="<?= media(); ?>/images/avatar.png" alt="User Image">
         <div>
             <p class="app-sidebar__user-name"><?= $_SESSION['userData']['nombre_completo']; ?></p>
             <p class="app-sidebar__user-designation"><?= $_SESSION['userData']['nombre_rol']; ?></p>
         </div>
     </div>
     <ul class="app-menu">
        <?php if(!empty($_SESSION['permisos'][1]['r'])){?>
         <li><a class="app-menu__item" href="<?= base_url(); ?>dashboard"><i class="app-menu__icon fa fa-dashboard"></i><span class="app-menu__label">
                     Dashboard</span></a></li>
        <?php } ?>             
         <!--data-toggle="treeview" script personalizado(main.js) -->
         <?php if(!empty($_SESSION['permisos'][2]['r'])||!empty($_SESSION['permisos'][3]['r'])){?>
         <li class="treeview"><a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-users"></i><span class="
              app-menu__label">Usuarios</span><i class="treeview-indicator fa fa-angle-right"></i></a>
             <ul class="treeview-menu">
                <?php if(!empty($_SESSION['permisos'][2]['r'])){?>
                 <li><a class="treeview-item" href="<?= base_url(); ?>usuarios"><i class="icon fa fa-circle-o"></i> Gestión Usuarios</a></li>
                 <?php } ?> 
                <?php if(!empty($_SESSION['permisos'][3]['r'])){?>
                 <li><a class="treeview-item" href="<?= base_url(); ?>roles"><i class="icon fa fa-circle-o"></i> Roles</a></li>
                 <?php } ?> 
                  
             </ul>
         </li>
         <?php } ?> 

         <?php if(!empty($_SESSION['permisos'][4]['r'])){?>
         <li>
             <a class="app-menu__item" href="<?= base_url(); ?>clientes">
                 <i class="app-menu__icon fa fa-user"></i>
                 <span class=" app-menu__label">Clientes</span>
             </a>
         </li>
         <?php } ?> 
             <!--data-toggle="treeview" script personalizado producto/estanteria(main.js) -->
         <?php if(!empty($_SESSION['permisos'][5]['r'])||!empty($_SESSION['permisos'][6]['r'])){?>
         <li class="treeview"><a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-archive"></i><span class="
        app-menu__label">Productos</span><i class="treeview-indicator fa fa-angle-right"></i></a>
             <ul class="treeview-menu">
                <?php if(!empty($_SESSION['permisos'][5]['r'])){?>
                 <li><a class="treeview-item" href="<?= base_url(); ?>productos"><i class="icon fa fa-circle-o"></i>Gestión productos</a></li>
                 <?php } ?> 
                 <?php if(!empty($_SESSION['permisos'][6]['r'])){?>
                 <li><a class="treeview-item" href="<?= base_url(); ?>estanterias"><i class="icon fa fa-circle-o"></i> Estanterías</a></li>
                 <?php } ?>
             </ul> 
         </li>
         <?php } ?> 

         <?php if(!empty($_SESSION['permisos'][5]['r'])){?>
         <li>
             <a class="app-menu__item" href="<?= base_url(); ?>reportes">
                 <i class="app-menu__icon fa fa-file"></i>
                 <span class="app-menu__label">Reportes</span>
             </a>
         </li>
         <?php } ?>

         <li>
            <a class="app-menu__item" href="<?= base_url(); ?>logout">
                 <i class="app-menu__icon fa fa-sign-out"></i>
                 <span class="app-menu__label">Logout</span>
             </a>
         </li>
     </ul>
 </aside>