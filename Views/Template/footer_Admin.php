<?php   /** @var array $data */?>
<script>
  const base_url = "<?= base_url(); ?>";
</script>

<!-- Unico jQuery ya que main.js requerido -->
<script src="<?= media(); ?>/js/jquery-3.3.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom scripts del tema -->
<script src="<?= media(); ?>/js/main.js"></script>
<script src="<?= media(); ?>/js/fontawesome.js"></script>
<script src="<?= media(); ?>/js/functions_admin.js"></script>
<!-- The javascript plugin to display page loading on top-->
<script src="<?= media(); ?>/js/plugins/pace.min.js"></script>
<script type="text/javascript" src="<?= media(); ?>/js/plugins/sweetalert.min.js"></script>
<!--Data Table plugin-->
<script type="text/javascript" src="<?= media(); ?>/js/plugins/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?= media(); ?>/js/plugins/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="<?= media(); ?>/js/plugins/select2.min.js"></script>
<?php if ($data['page_name'] == "roles"): ?>
  <script src="<?= media(); ?>/js/functions_roles.js"></script>
<?php endif; ?>

<?php if ($data['page_name'] == "usuarios"): ?>
  <script src="<?= media(); ?>/js/functions_usuarios.js"></script>
<?php endif; ?>


</body>

</html>