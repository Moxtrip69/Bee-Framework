<?php require_once INCLUDES.'inc_bee_header.php'; ?>
<?php require_once INCLUDES.'inc_bee_navbar.php'; ?>

<div class="container py-5">
  <div class="row">
    <div class="col-12 col-md-4 text-center offset-md-4">
      <a href="<?php echo URL; ?>"><img src="<?php echo get_logo() ?>" alt="<?php echo get_sitename(); ?>" class="img-fluid" style="width: 150px;"></a>
      <h2 class="mt-5 mb-3"><span class="text-warning">Bee</span> framework</h2>
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
                <?php foreach ($d->user as $k => $v): ?>
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
          <a href="home" class="btn btn-success float-start"><i class="fas fa-home fa-fw"></i> Inicio</a>
          <a href="logout" class="btn btn-danger float-end confirmar">Cerrar sesión</a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once INCLUDES.'inc_bee_footer.php'; ?>