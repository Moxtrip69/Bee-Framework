<?php require_once INCLUDES.'header.php'; ?>
<?php require_once INCLUDES.'bee_navbar.php'; ?>

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
          <form action="creator/post_model" method="post" novalidate>
            <?php echo insert_inputs(); ?>
            
            <div class="mb-3">
              <label for="filename" class="form-label">Nombre del modelo (sin "Model.php") *</label>
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

            <button class="btn btn-primary btn-lg btn-block" type="submit">Crear ahora</button>
          </form>
        </div>
        <div class="card-footer">
          <a class="btn btn-success" href="<?php echo 'creator/controller' ?>">Nuevo controlador</a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once INCLUDES.'footer.php'; ?>
