<?php require_once INCLUDES.'inc_bee_header.php'; ?>
<?php require_once INCLUDES.'inc_bee_navbar.php'; ?>

<div class="container py-5">
  <div class="row">
    <div class="col-12">
      <?php echo Flasher::flash(); ?>
    </div>
  </div>
  <div class="row">
    <div class="col-12 text-center mb-5">
      <h2><a href="<?php echo URL; ?>"><img src="<?php echo get_bee_logo(); ?>" alt="Bee framework" class="img-fluid" style="width: 100px;"></a></h2>
    </div>

    <div class="col-12">
      <p>La contraseña ha sido generado con éxito, debes editarla dentro de <code>loginController.php</code> en el método <code>post_login()</code>.</p>
      <table class="table table-striped table-bordered">
        <tr>
          <th class="bg-light">Password</th>
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

<?php require_once INCLUDES.'inc_bee_footer.php'; ?>