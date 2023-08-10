<?php require_once INCLUDES . 'admin/dashboardTop.php'; ?>

<div class="row">
  <div class="col-12">
    <?php echo Flasher::flash(); ?>
  </div>
  <div class="col-12 col-md-8">
    <div class="card">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Información del usuario</h6>
        <div class="dropdown no-arrow">
          <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
          </a>
          <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
            <div class="dropdown-header">Acciones</div>
            <a class="dropdown-item" href="#">Ver</a>
            <a class="dropdown-item" href="#">Editar</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="#">Borrar</a>
          </div>
        </div>
      </div>
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
        <a href="logout" class="btn btn-danger float-end" data-toggle="modal" data-target="#logoutModal">Cerrar sesión</a>
      </div>
    </div>
  </div>
</div>

<?php require_once INCLUDES . 'admin/dashboardBottom.php'; ?>