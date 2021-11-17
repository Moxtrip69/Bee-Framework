<?php require_once INCLUDES.'inc_bee_header.php'; ?>
<?php require_once INCLUDES.'inc_bee_navbar.php'; ?>

<div class="container">
  <div class="py-5 text-center">
    <a href="<?php echo URL; ?>"><img src="<?php echo get_bee_logo() ?>" alt="Bee framework" class="img-fluid" style="width: 150px;"></a>
    <h2><?php echo $d->title; ?></h2>
    <p class="lead">Lorem ipsum dolor sit amet consectetur, adipisicing elit. Nam, ullam.</p>
  </div>

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

<?php require_once INCLUDES.'inc_bee_footer.php'; ?>
