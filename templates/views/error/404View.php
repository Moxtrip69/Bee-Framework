<?php require_once INCLUDES.'inc_header.php'; ?>
<?php require_once INCLUDES.'inc_bee_navbar.php'; ?>

<div class="container">
  <div class="row">
    <div class="col-12">
      <?php echo Flasher::flash(); ?>
    </div>
  </div>
  <div class="row">
    <div class="col-xl-6 col-12 text-center offset-xl-3 py-5">
      <a href="<?php echo URL; ?>"><img src="<?php echo get_image('bee_logo.png') ?>" alt="Bee framework" class="img-fluid" style="width: 150px;"></a>
      <h1 class="mt-5 mb-3">
        <span class="text-warning d-block"><b>404</b></span>
        <b>Página no encontrada</b>
      </h1>
      <p class="text-center text-muted">Entraste a otra dimensión.</p>
      <div class="mt-5">
        <a class="btn btn-success btn-lg" href="home"><i class="fas fa-undo fa-fw"></i> Regresar</a>
      </div>
      <div class="mt-5">
        <p class="text-muted">Desarrollado con <i class="fas fa-heart text-danger"></i> por <a href="https://www.academy.joystick.com.mx">Joystick</a>.</p>
      </div>
    </div>
  </div>
</div>

<?php require_once INCLUDES.'inc_bee_footer.php'; ?>