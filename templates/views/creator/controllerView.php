<?php require_once INCLUDES . 'header.php'; ?>
<?php require_once INCLUDES . 'bee_navbar.php'; ?>

<div class="container py-5 main-wrapper">
  <div class="text-center mb-5">
    <h2><?php echo $d->title; ?></h2>
    <p class="lead">Lorem ipsum dolor sit amet consectetur, adipisicing elit. Nam, ullam.</p>
  </div>

  <div class="row">
    <!-- formulario -->
    <div class="col-12 col-md-4 offset-md-4">
      <?php echo Flasher::flash(); ?>
      <div class="card">
        <div class="card-header">
          <h4><?php echo $d->title; ?></h4>
        </div>
        <div class="card-body">
          <form action="creator/post_controller" method="post" novalidate>
            <?php echo insert_inputs(); ?>

            <div class="mb-3">
              <label for="filename" class="form-label">Nombre del controlador (sin "Controller.php") *</label>
              <input type="text" class="form-control" id="filename" name="filename" placeholder="reportes" required>
            </div>

            <div class="mb-3">
              <p class="mb-1">Decide que archivos generaremos</p>
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="generar-vista" name="generar-vista">
                <label class="form-check-label" for="generar-vista">Crear carpeta y vista inicial</label>
              </div>
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="usar-twig" name="usar-twig">
                <label class="form-check-label" for="usar-twig">Usar Twig</label>
              </div>
            </div>

            <button class="btn btn-primary btn-lg btn-block" type="submit">Crear ahora</button>
          </form>
        </div>
        <div class="card-footer">
          <a class="btn btn-success" href="<?php echo 'creator/model' ?>">Nuevo modelo</a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once INCLUDES . 'footer.php'; ?>