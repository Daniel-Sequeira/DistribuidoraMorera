 <!-- Sidebar menu-->
 <div class="app-sidebar__overlay" data-toggle="sidebar"></div>
 <aside class="app-sidebar">
     <div class="app-sidebar__user"><img class="app-sidebar__user-avatar" src="<?= media(); ?>/images/avatar.png" alt="User Image">
         <div>
             <p class="app-sidebar__user-name">Daniel Sequeira</p>
             <p class="app-sidebar__user-designation">Administrador</p>
         </div>
     </div>
     <ul class="app-menu">
         <li><a class="app-menu__item" href="<?= base_url(); ?>dashboard"><i class="app-menu__icon fa fa-dashboard"></i><span class="app-menu__label">
                     Dashboard</span></a></li>
         <!--data-toggle="treeview" script personalizado(main.js) -->
         <li class="treeview"><a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-users"></i><span class="
        app-menu__label">Usuarios</span><i class="treeview-indicator fa fa-angle-right"></i></a>
             <ul class="treeview-menu">
                 <li><a class="treeview-item" href="<?= base_url(); ?>usuarios"><i class="icon fa fa-circle-o"></i> Gestión Usuarios</a></li>
                 <li><a class="treeview-item" href="<?= base_url(); ?>roles"><i class="icon fa fa-circle-o"></i> Roles</a></li>
             </ul>
         </li>
         <li>
             <a class="app-menu__item" href="<?= base_url(); ?>clientes">
                 <i class="app-menu__icon fa fa-user"></i>
                 <span class=" app-menu__label">Clientes</span>
             </a>
         </li>
         <li>
             <a class="app-menu__item" href="<?= base_url(); ?>productos">
                 <i class="app-menu__icon fa fa-archive"></i>
                 <span class="app-menu__label">Productos</span>
             </a>
         </li>
         <li>
             <a class="app-menu__item" href="<?= base_url(); ?>reportes">
                 <i class="app-menu__icon fa fa-file"></i>
                 <span class="app-menu__label">Reportes</span>
             </a>
         </li>
         <li><a class="app-menu__item" href="<?= base_url(); ?>logout">
                 <i class="app-menu__icon fa fa-sign-out"></i>
                 <span class="app-menu__label">Logout</span>
             </a>
         </li>
     </ul>
 </aside>