<?php require_once INCLUDES . 'header.php'; ?>
<?php require_once INCLUDES . 'bee_navbar.php'; ?>

<div class="container py-5 main-wrapper">
  <div class="text-center mb-5">
    <h2><?php echo $d->title; ?></h2>
    <p class="lead">Lorem ipsum dolor sit amet consectetur, adipisicing elit. Nam, ullam.</p>
  </div>

  <div class="row">
    <div class="col-12">
      <?php echo Flasher::flash(); ?>
    </div>
  </div>

  <div class="row">
    <!-- Formulario para crear controlador -->
    <div class="col-12 col-md-4 col-lg-4">
      <div class="card shadow mb-4">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary">Crear nuevo controlador</h6>
        </div>
        <div class="card-body">
          <form action="creator/post_controller" method="post">
            <?php echo insert_inputs(); ?>

            <div class="mb-3">
              <label for="filename" class="form-label">Nombre del controlador <code>(sin "Controller.php")</code> <span class="text-danger">*</span></label>
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

            <button class="btn btn-success btn-lg btn-block" type="submit">Crear controlador</button>
          </form>
        </div>
      </div>
    </div>

    <!-- Formulario para crear modelo -->
    <div class="col-12 col-md-4 col-lg-4">
      <div class="card shadow mb-4">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary">Crear nuevo modelo</h6>
        </div>
        <div class="card-body">
          <form action="creator/post_model" method="post">
            <?php echo insert_inputs(); ?>

            <div class="mb-3">
              <label for="filename" class="form-label">Nombre del modelo <code>(sin "Model.php")</code> <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="filename" name="filename" placeholder="usuario" required>
            </div>

            <div class="mb-3">
              <label for="tabla" class="form-label">Tabla principal</label>
              <input type="text" class="form-control" id="tabla" name="tabla" placeholder="usuarios">
            </div>

            <div class="mb-3">
              <label for="esquema" class="form-label">Esquema de la tabla <?php echo more_info('Separa con una "," cada columna del esquema.') ?></label>
              <input type="text" class="form-control" id="esquema" name="esquema" placeholder="id, nombre, email, telefono">
            </div>

            <button class="btn btn-success btn-lg btn-block" type="submit">Crear modelo</button>
          </form>
        </div>
      </div>
    </div>

    <!-- Formulario para crear vista -->
    <div class="col-12 col-md-4 col-lg-4">
      <div class="card shadow mb-4">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary">Crear nueva vista</h6>
        </div>
        <div class="card-body">
          <form action="creator/post_view" method="post">
            <?php echo insert_inputs(); ?>

            <div class="mb-3">
              <label for="controller" class="form-label">Controlador</code> <span class="text-danger">*</span></label>
              <select name="controller" id="controller" class="form-select">
                <?php foreach ($d->controllers as $c): ?>
                  <option value="<?php echo $c; ?>"><?php echo $c; ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="mb-3">
              <label for="viewName" class="form-label">Nombre de la vista <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="viewName" name="viewName" placeholder="index">
            </div>

            <div class="mb-3">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="usar-twig2" name="usar-twig" <?php echo USE_TWIG ? 'checked' : ''; ?>>
                <label class="form-check-label" for="usar-twig2">Usar Twig</label>
              </div>
            </div>

            <button class="btn btn-success btn-lg btn-block" type="submit">Crear vista</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once INCLUDES . 'footer.php'; ?>