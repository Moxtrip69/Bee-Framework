<?php require_once INCLUDES.'inc_header.php'; ?>

<div class="container">
  <div class="row">
    <div class="col-6 text-center offset-xl-3">
      <img src="<?php echo IMAGES.'bee_logo.png' ?>" alt="Bee framework" class="img-fluid" style="width: 200px;">
      <h2 class="mt-5 mb-3"><span class="text-warning">Bee</span> framework</h2>
      <!-- contenido -->
      <?php echo Flasher::flash(); ?>
      <!-- ends -->

    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <?php debug($_SESSION); ?>
    </div>
    <div class="col-12 text-center">
      <a href="logout" class="btn btn-danger">Cerrar sesión</a>
    </div>
  </div>
</div>

<?php require_once INCLUDES.'inc_footer.php'; ?>