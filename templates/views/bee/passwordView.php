<?php require_once INCLUDES . 'header.php'; ?>
<?php require_once INCLUDES . 'bee_navbar.php'; ?>

<div class="container py-5 main-wrapper">
  <div class="row">
    <div class="col-12">
      <?php echo Flasher::flash(); ?>
    </div>
  </div>
  <div class="row">
    <div class="col-12 text-center mb-5">
      <h2><a href="<?php echo get_base_url(); ?>"><img src="<?php echo get_bee_logo(); ?>" alt="Bee framework" class="img-fluid" style="width: 100px;"></a></h2>
    </div>

    <div class="col-12">
      <p>La contraseña ha sido generada con éxito, debes editarla dentro de <code>loginController.php</code> en el método <code>post_login()</code> si no usarás <b>sesiones de usuario persistentes</b> (Con Cookies).</p>
      <div class="row">
        <div class="col-12 col-md-4">
          <div class="card">
            <div class="card-body">
              <form action="bee/password" method="post">
                <div class="mb-3">
                  <label for="password" class="form-label">Contraseña deseada <span class="text-danger">*</span></label>
                  <input class="form-control" type="text" id="password" name="password" required>
                </div>

                <button class="btn btn-success">Generar</button>
              </form>
            </div>
          </div>
        </div>

        <div class="col-12 col-md-8">
          <div class="table-responsive">
            <table class="table table-striped table-bordered">
              <tr>
                <th class="bg-light">Contraseña</th>
                <td><?php echo $d->pw->password; ?></td>
              </tr>
              <tr>
                <th class="bg-light">Hash</th>
                <td><?php echo $d->pw->hash; ?></td>
              </tr>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once INCLUDES . 'footer.php'; ?>