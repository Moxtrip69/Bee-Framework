<?php require_once INCLUDES . 'admin/dashboardTop.php'; ?>
<?php $username = (string) ($d->user->username ?? 'Usuario'); ?>

<div class="row g-4">
  <div class="col-12 col-xl-4"><section class="card h-100"><div class="card-body p-4 text-center"><span class="bee-admin-avatar bee-admin-avatar-lg mx-auto mb-3"><?= htmlspecialchars(strtoupper(substr($username, 0, 1)), ENT_QUOTES, 'UTF-8') ?></span><h2 class="h4 mb-1"><?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?></h2><p class="small">Cuenta autenticada actualmente.</p><a class="btn btn-outline-danger w-100 mt-3" href="logout"><i class="fas fa-right-from-bracket me-2" aria-hidden="true"></i>Cerrar sesión</a></div></section></div>
  <div class="col-12 col-xl-8"><section class="card"><header class="p-4 border-bottom"><h2 class="h4 mb-1">Información de cuenta</h2><p class="small mb-0">Datos disponibles para la sesión actual.</p></header><dl class="list-group list-group-flush mb-0"><?php foreach ($d->user as $key => $item): ?><?php if (in_array((string) $key, ['password', 'auth_token'], true)) { continue; } ?><div class="list-group-item d-grid gap-2 px-4 py-3 bee-info-row"><dt class="small fw-bold text-secondary text-uppercase"><?= htmlspecialchars((string) $key, ENT_QUOTES, 'UTF-8') ?></dt><dd class="mb-0 text-break"><?= htmlspecialchars(is_scalar($item) || $item === null ? (string) $item : (string) json_encode($item, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8') ?></dd></div><?php endforeach; ?></dl></section></div>
</div>

<?php require_once INCLUDES . 'admin/dashboardBottom.php'; ?>
