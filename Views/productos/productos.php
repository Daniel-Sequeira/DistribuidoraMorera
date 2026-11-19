<?php
/** @var array $data */
headerAdmin($data);
getModal('modalProductos', $data);
?>
<main class="app-content">
  <div class="app-title">
    <div>
      <h1>
        <i class="fas fa-box"></i> <?= $data['page_title'] ?>
        <?php if (!empty($_SESSION['permisosMod']['w'])) { ?>
          <button class="btn btn-primary" type="button" onclick="openModal();">
            <i class="fa-solid fa-circle-plus"></i> Nuevo
          </button>
        <?php } ?>
      </h1>
    </div>
    <ul class="app-breadcrumb breadcrumb">
      <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
      <li class="breadcrumb-item"><a href="<?= base_url(); ?>productos"><?= $data['page_title'] ?></a></li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="tile">
        <div class="tile-body">
          <!-- Filtro por estado (Todos, Activos, Inactivos) -->
          <div class="row mb-3">
            <div class="col-md-3">
              <label for="filtroEstado" class="form-label fw-bold">Filtrar por estado:</label>
              <select id="filtroEstado" class="form-select">
                <option value="">Todos los Productos</option>
                <option value="Activo">Activos</option>
                <option value="Inactivo">Inactivos</option>
              </select>
            </div>
          </div>

          <!-- Tabla con paginación gestionada por DataTables -->
          <div class="table-responsive">
            <table class="table table-hover table-bordered w-100" id="tableProductos">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Código</th>
                  <th>Nombre</th>
                  <th>Proveedor</th>
                  <th>Estantería</th>
                  <th>Stock Total</th>
                  <th>Precio</th>
                  <th>Estado</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <!-- DataTables carga aquí los registros vía AJAX -->
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>
<?php footerAdmin($data); ?>