<?php
$adminPath = trim((string) parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');
$adminNavigation = [
  ['label' => 'Resumen', 'href' => 'admin', 'icon' => 'fa-table-columns'],
  ['label' => 'Usuarios', 'href' => 'admin/usuarios', 'icon' => 'fa-users'],
];
?>
<aside class="offcanvas-lg offcanvas-start bee-admin-sidebar" tabindex="-1" id="adminSidebar" aria-labelledby="admin-sidebar-title">
  <div class="offcanvas-header border-bottom border-secondary">
    <h2 class="offcanvas-title h5 text-white mb-0" id="admin-sidebar-title">Administración</h2>
    <button class="btn-close btn-close-white" type="button" data-bs-dismiss="offcanvas" data-bs-target="#adminSidebar" aria-label="Cerrar navegación"></button>
  </div>
  <div class="offcanvas-body d-flex flex-column p-3">
    <a class="d-flex align-items-center gap-3 px-2 py-3 mb-4 text-white text-decoration-none" href="bee">
      <span class="bee-brand-symbol" aria-hidden="true"></span>
      <span><strong class="d-block">Bee Framework</strong><small class="bee-admin-sidebar-caption">Panel de administración</small></span>
    </a>
    <span class="bee-admin-sidebar-label px-3 mb-2">Gestión</span>
    <nav class="nav nav-pills flex-column gap-2" aria-label="Administración">
      <?php foreach ($adminNavigation as $item): ?>
        <?php $active = $item['href'] === 'admin' ? str_ends_with($adminPath, '/admin') : str_contains($adminPath, '/' . $item['href']); ?>
        <a class="nav-link d-flex align-items-center gap-3 <?= $active ? 'active' : '' ?>" href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8') ?>" <?= $active ? 'aria-current="page"' : '' ?>>
          <i class="fas <?= htmlspecialchars($item['icon'], ENT_QUOTES, 'UTF-8') ?> fa-fw" aria-hidden="true"></i><span><?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?></span>
        </a>
      <?php endforeach; ?>
    </nav>
    <div class="mt-auto pt-4">
      <a class="btn btn-outline-light w-100" href="creator"><i class="fas fa-wand-magic-sparkles me-2" aria-hidden="true"></i>Bee Creator</a>
    </div>
  </div>
</aside>
