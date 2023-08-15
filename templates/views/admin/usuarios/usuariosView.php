<?php require_once INCLUDES . 'admin/dashboardTop.php'; ?>

<div class="row">
  <!-- Formulario para agregar usuario -->
  <div class="col-12 col-md-6 col-lg-6 col-xl-3">
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Agregar un usuario</h6>
      </div>
      <div class="card-body">
        <form action="admin/post_usuarios" method="post">
          <?php echo insert_inputs(); ?>

          <div class="mb-3">
            <label for="username" class="form-label">Nombre de usuario <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="username" name="username" placeholder="admin" required>
          </div>

          <div class="mb-3">
            <label for="email" class="form-label">Correo electrónico <span class="text-danger">*</span></label>
            <input type="email" class="form-control" id="email" name="email" placeholder="admin@beeframework.com" required>
          </div>

          <div class="mb-3">
            <label for="password" class="form-label">Contraseña <span class="text-danger">*</span></label>
            <input type="password" class="form-control" id="password" name="password" required>
          </div>

          <button class="btn btn-success btn-lg btn-block" type="submit">Agregar ahora</button>
        </form>
      </div>
    </div>
  </div>

  <!-- Tabla de resultados -->
  <div class="col-12 col-md-6 col-lg-6 col-xl-9">
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Todos los usuarios</h6>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive" style="min-height: 300px;">
          <table class="table table-hover table-striped">
            <thead>
              <tr>
                <th>Usuario</th>
                <th class="text-center">Correo electrónico</th>
                <th class="text-end">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($d->users->rows)): ?>
                <?php foreach ($d->users->rows as $user) : ?>
                  <tr>
                    <td><?php echo $user->id == get_user('id') ? $user->username . ' (Tú)' : $user->username; ?></td>
                    <td class="text-center"><?php echo $user->email; ?></td>
                    <td class="text-end">
                      <div class="dropdown">
                        <a class="btn btn-sm btn-secondary" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                          <i class="fas fa-chevron-down"></i>
                        </a>
                        <ul class="dropdown-menu">
                          <?php if (!empty($user->auth_token)): ?>
                            <li><a class="dropdown-item" href="<?php echo build_url(sprintf('admin/destruir-sesion/%s', $user->id)) ?>">Destruir sesión</a></li>
                          <?php endif; ?>
                          <li><a class="dropdown-item confirmar" href="<?php echo build_url(sprintf('admin/borrar-usuario/%s', $user->id)) ?>">Borrar</a></li>
                        </ul>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="3" class="text-center">No hay usuarios registrados en la base de datos.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
      <div class="card-body">
        <?php echo $d->users->pagination; ?>
      </div>
    </div>
  </div>
</div>

<?php require_once INCLUDES . 'admin/dashboardBottom.php'; ?>