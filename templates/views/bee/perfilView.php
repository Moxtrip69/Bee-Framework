<?php require_once INCLUDES . 'header.php'; ?>
<?php require_once INCLUDES . 'bee_navbar.php'; ?>

<div class="container py-5 main-wrapper">
  <div class="row">
    <div class="col-12 col-md-4 text-center offset-md-4 mb-5">
      <a href="<?php echo get_base_url(); ?>"><img src="<?php echo get_bee_logo() ?>" alt="<?php echo get_sitename(); ?>" class="img-fluid" style="width: 150px;"></a>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <?php echo Flasher::flash(); ?>
    </div>
    <div class="col-12 col-md-8 offset-md-2">
      <div class="card">
        <div class="card-header">Información del usuario</div>
        <div class="card-body">
          <?php echo sprintf('<p>Bienvenido a %s, usuario <b>%s</b>, esta es tu información.</p>', get_bee_name(), $d->user->username); ?>
          <div class="table-responsive rounded">
            <table class="table table-sm table-striped table-hover table-bordered">
              <tbody>
                <?php foreach ($d->user as $k => $v) : ?>
                  <tr>
                    <th><?php echo $k; ?></th>
                    <td><?php echo $v; ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
        <div class="card-footer clearfix">
          <a href="bee" class="btn btn-outline-success float-start"><i class="fas fa-home fa-fw"></i> Inicio</a>
          <a href="logout" class="btn btn-danger float-end confirmar">Cerrar sesión</a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once INCLUDES . 'footer.php'; ?>