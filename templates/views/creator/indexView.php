<?php require_once INCLUDES.'inc_header.php'; ?>

<div class="container">
  <div class="row">
    <div class="col-12">
      <?php echo Flasher::flash(); ?>
    </div>

    <!-- formulario -->
    <div class="col-xl-12">
      <div class="card">
        <div class="card-header">
          <h4><?php echo $d->title; ?></h4>
        </div>
        <div class="card-body">
          <a class="btn btn-success" href="<?php echo 'creator/controller' ?>">Crear controlador</a>
          <a class="btn btn-success" href="<?php echo 'creator/model' ?>">Crear modelo</a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once INCLUDES.'inc_footer_v2.php'; ?>

