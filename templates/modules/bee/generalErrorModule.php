<?php require_once INCLUDES . 'header.php'; ?>

<div class="container py-5 main-wrapper d-flex justify-content-center align-items-center">
  <div class="d-flex flex-column justify-content-center align-items-center">
    <img src="<?php echo get_image('generalError.png') ?>" alt="<?php echo $d->title; ?>" class="img-fluid" style="width: 200px;">
    <h1 class="fw-bold mt-3">¡Wooopsy!</h1>

    <h5 class="mt-5">Hubo un error</h5>
    <p class="text-muted">Por favor comparte el siguiente error con nuestro equipo de soporte:</p>
    <div class="alert alert-danger">
      <?php echo $d->error; ?>
    </div>
    <?php echo Flasher::flash(); ?>
  </div>
</div>

<?php require_once INCLUDES . 'footer.php'; ?>