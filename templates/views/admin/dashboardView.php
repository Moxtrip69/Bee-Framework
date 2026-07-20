<?php require_once INCLUDES . 'admin/dashboardTop.php'; ?>

<section class="row row-cols-1 row-cols-md-2 g-4 mb-4" aria-label="Resumen">
  <div class="col"><article class="card h-100 border-0 bee-admin-stat"><div class="card-body d-flex align-items-center gap-4 p-4"><span class="bee-info-icon"><i class="fas fa-users" aria-hidden="true"></i></span><div><span class="d-block text-secondary small fw-bold text-uppercase">Usuarios registrados</span><strong class="display-6"><?= (int) $d->usersCount ?></strong></div></div></article></div>
  <div class="col"><article class="card h-100 border-0 bee-admin-stat"><div class="card-body d-flex align-items-center gap-4 p-4"><span class="bee-info-icon"><i class="fas fa-signal" aria-hidden="true"></i></span><div><span class="d-block text-secondary small fw-bold text-uppercase">Sesiones activas</span><strong class="display-6"><?= (int) $d->activeSessions ?></strong></div></div></article></div>
</section>

<section class="row g-4">
  <div class="col-12 col-xl-8">
    <article class="card h-100"><div class="card-body p-0">
      <header class="d-flex justify-content-between align-items-center gap-3 p-4 border-bottom"><div><h2 class="h4 mb-1">Usuarios recientes</h2><p class="small mb-0">Últimas cuentas creadas en la aplicación.</p></div><a class="btn btn-sm btn-outline-secondary" href="admin/usuarios">Gestionar</a></header>
      <div class="list-group list-group-flush">
        <?php foreach ($d->recentUsers as $user): ?><?php $item = is_object($user) ? $user : (object) $user; ?>
          <div class="list-group-item d-flex align-items-center gap-3 px-4 py-3"><span class="bee-admin-avatar"><?= htmlspecialchars(strtoupper(substr((string) $item->username, 0, 1)), ENT_QUOTES, 'UTF-8') ?></span><div class="overflow-hidden"><strong class="d-block text-truncate"><?= htmlspecialchars((string) $item->username, ENT_QUOTES, 'UTF-8') ?></strong><small class="d-block text-secondary text-truncate"><?= htmlspecialchars((string) $item->email, ENT_QUOTES, 'UTF-8') ?></small></div><span class="badge <?= empty($item->auth_token) ? 'text-bg-light' : 'text-bg-success' ?> ms-auto"><?= empty($item->auth_token) ? 'Sin sesión' : 'Activo' ?></span></div>
        <?php endforeach; ?>
        <?php if (empty($d->recentUsers)): ?><div class="p-5 text-center text-secondary">Todavía no hay usuarios registrados.</div><?php endif; ?>
      </div>
    </div></article>
  </div>
  <div class="col-12 col-xl-4"><aside class="card h-100 bg-dark text-white border-0"><div class="card-body p-4 d-flex flex-column"><span class="badge text-bg-warning align-self-start mb-4">Backend reutilizable</span><h2 class="h3 text-white">Listo para crecer contigo.</h2><p class="text-white-50">Añade nuevos CRUDs incorporando un elemento al arreglo de navegación y una vista dentro de <code class="text-warning">templates/views/admin</code>.</p><a class="btn btn-light mt-auto" href="creator">Crear componente</a></div></aside></div>
</section>

<?php require_once INCLUDES . 'admin/dashboardBottom.php'; ?>
