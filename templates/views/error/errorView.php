<?php require_once INCLUDES.'inc_header.php'; ?>

<div class="container">
  <div class="row">
    <div class="col-12">
      <?php echo Flasher::flash(); ?>
    </div>
  </div>
  <div class="row">
    <div class="col-xl-6 col-12 text-center offset-xl-3 py-5">
      <a href="<?php echo URL; ?>">
        <img src="<?php echo get_logo() ?>" alt="<?php echo get_sitename(); ?>" class="img-fluid" style="width: 150px;">
      </a>

      <h1 class="mt-5 mb-3">
        <span class="text-warning d-block"><b><?php echo $d->code; ?></b></span>
        <b>Página no encontrada</b>
      </h1>

      <p class="text-center text-muted">Entraste a otra dimensión.</p>

      <div class="mt-5">
        <a class="btn btn-outline-success btn-lg" href="<?php echo get_default_controller(); ?>"><i class="fas fa-undo fa-fw"></i> Regresar</a>
      </div>
    </div>
  </div>
</div>

<?php require_once INCLUDES.'inc_footer.php'; ?>