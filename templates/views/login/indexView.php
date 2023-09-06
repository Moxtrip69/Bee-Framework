<!-- vista deprecada -->
<?php require_once INCLUDES . 'header.php'; ?>

<div class="container">
  <div class="py-5 text-center">
    <a href="<?php echo get_base_url(); ?>"><img src="<?php echo get_logo() ?>" alt="<?php echo get_sitename(); ?>" class="img-fluid" style="width: 150px;"></a>
    <h2>Ingresa a tu cuenta</h2>
    <p class="lead">Lorem ipsum dolor sit amet consectetur, adipisicing elit. Nam, ullam.</p>
  </div>

  <div class="row">
    <!-- formulario -->
    <div class="col-12 col-md-4 offset-md-4">
      <div class="card">
        <div class="card-header">
          <h4>Ingresa a tu cuenta</h4>
        </div>
        <div class="card-body">
          <form action="login/post_login" method="post" novalidate>
            <?php echo insert_inputs(); ?>

            <div class="mb-3 row">
              <div class="col-12">
                <?php echo Flasher::flash(); ?>
              </div>
              <div class="col-12 mb-3">
                <label class="form-label" for="usuario">Usuario</label>
                <input type="text" class="form-control" id="usuario" name="usuario" placeholder="Walter White" required>
                <?php if (is_demo() || is_local()) : ?>
                  <small class="text-muted">Ingresa bee</small>
                <?php endif; ?>
              </div>
              <div class="col-12">
                <label class="form-label" for="password">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" required>
                <?php if (is_demo() || is_local()) : ?>
                  <small class="text-muted">Ingresa 123456</small>
                <?php endif; ?>
              </div>
            </div>

            <button class="btn btn-primary" type="submit"><i class="fas fa-fingerprint fa-fw"></i> Ingresar</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once INCLUDES . 'footer.php'; ?>