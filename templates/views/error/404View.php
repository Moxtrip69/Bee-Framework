<?php require_once INCLUDES.'inc_header.php'; ?>

<div class="container">
  <div class="row">
    <div class="col-6 text-center offset-xl-3">
      <a href="<?php echo URL; ?>"><img src="<?php echo get_image('bee_logo.png') ?>" alt="Bee framework" class="img-fluid" style="width: 150px;"></a>
      <h1 class="text-white mt-5 mb-3"><span class="text-warning">404</span><br>Not found</h1>
      <h5 class="text-center text-white">Ejeeeemmm... entraste a otra dimensión, la página que buscas no existe aquí.</h5>
      <div class="mt-5">
        <a class="btn btn-success btn-lg" href="home"><i class="fas fa-undo"></i> Regresar</a>
      </div>
      <div class="mt-5">
        <p class="text-muted">Desarrollado con <i class="fas fa-heart text-danger"></i> por <a href="http://bit.ly/udemy_joystick" class="text-white">Joystick</a>.</p>
      </div>
    </div>
  </div>
</div>

<?php require_once INCLUDES.'inc_footer.php'; ?>