<?php require_once INCLUDES . 'admin/dashboardTop.php'; ?>

<div class="row g-4 align-items-start">
  <div class="col-12 col-xl-4">
    <section class="card sticky-xl-top bee-admin-create-card" aria-labelledby="create-user-title"><div class="card-body p-4">
      <span class="bee-info-icon mb-4" aria-hidden="true"><i class="fas fa-user-plus"></i></span><h2 class="h4" id="create-user-title">Agregar usuario</h2><p class="small">Crea una cuenta con acceso al sistema.</p>
      <form action="admin/post_usuarios" method="post"><?= insert_inputs(); ?>
        <div class="mb-3"><label class="form-label" for="username">Usuario</label><input class="form-control" type="text" id="username" name="username" placeholder="admin" minlength="5" maxlength="20" autocomplete="off" required></div>
        <div class="mb-3"><label class="form-label" for="email">Correo electrónico</label><input class="form-control" type="email" id="email" name="email" placeholder="admin@ejemplo.com" autocomplete="off" required></div>
        <div class="mb-3"><label class="form-label" for="password">Contraseña temporal</label><input class="form-control" type="password" id="password" name="password" minlength="8" maxlength="20" autocomplete="new-password" aria-describedby="password-help" required><div class="form-text" id="password-help">Mayúscula, minúscula, número y carácter especial.</div></div>
        <button class="btn btn-primary w-100" type="submit"><i class="fas fa-plus me-2" aria-hidden="true"></i>Crear usuario</button>
      </form>
    </div></section>
  </div>
  <div class="col-12 col-xl-8">
    <section class="card" aria-labelledby="users-title">
      <header class="d-flex flex-wrap justify-content-between align-items-center gap-3 p-4 border-bottom"><div><h2 class="h4 mb-1" id="users-title">Usuarios</h2><p class="small mb-0">Cuentas registradas y estado de sus sesiones.</p></div><span class="badge text-bg-warning"><?= count($d->users->rows ?? []) ?> en esta página</span></header>
      <div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead><tr><th>Usuario</th><th>Estado</th><th class="text-end">Acciones</th></tr></thead><tbody>
        <?php if (!empty($d->users->rows)): ?><?php foreach ($d->users->rows as $user): ?><?php $isCurrent = (int) $user->id === (int) get_user('id'); ?>
          <tr><td><div class="d-flex align-items-center gap-3"><span class="bee-admin-avatar"><?= htmlspecialchars(strtoupper(substr((string) $user->username, 0, 1)), ENT_QUOTES, 'UTF-8') ?></span><div><strong class="d-block"><?= htmlspecialchars((string) $user->username, ENT_QUOTES, 'UTF-8') ?><?= $isCurrent ? ' <span class="badge text-bg-light">Tú</span>' : '' ?></strong><small class="text-secondary"><?= htmlspecialchars((string) $user->email, ENT_QUOTES, 'UTF-8') ?></small></div></div></td><td><span class="badge <?= empty($user->auth_token) ? 'text-bg-light' : 'text-bg-success' ?>"><?= empty($user->auth_token) ? 'Sin sesión' : 'Activo' ?></span></td><td class="text-end">
            <?php if (!$isCurrent): ?><div class="d-inline-flex flex-wrap justify-content-end gap-2"><?php if (!empty($user->auth_token)): ?><form action="admin/destruir-sesion/<?= (int) $user->id ?>" method="post" data-confirm-form="¿Cerrar la sesión activa de este usuario?"><?= insert_inputs(); ?><button class="btn btn-sm btn-outline-secondary" type="submit" title="Cerrar sesión"><i class="fas fa-right-from-bracket" aria-hidden="true"></i><span class="visually-hidden">Cerrar sesión de <?= htmlspecialchars((string) $user->username, ENT_QUOTES, 'UTF-8') ?></span></button></form><?php endif; ?><form action="admin/borrar-usuario/<?= (int) $user->id ?>" method="post" data-confirm-form="¿Eliminar permanentemente este usuario?"><?= insert_inputs(); ?><button class="btn btn-sm btn-outline-danger" type="submit" title="Eliminar usuario"><i class="fas fa-trash" aria-hidden="true"></i><span class="visually-hidden">Eliminar <?= htmlspecialchars((string) $user->username, ENT_QUOTES, 'UTF-8') ?></span></button></form></div><?php else: ?><span class="small text-secondary">Cuenta actual</span><?php endif; ?>
          </td></tr>
        <?php endforeach; ?><?php else: ?><tr><td colspan="3" class="text-center text-secondary py-5">No hay usuarios registrados.</td></tr><?php endif; ?>
      </tbody></table></div>
      <?php if (!empty($d->users->pagination)): ?><footer class="p-4 border-top"><?= $d->users->pagination ?></footer><?php endif; ?>
    </section>
  </div>
</div>

<?php require_once INCLUDES . 'admin/dashboardBottom.php'; ?>
