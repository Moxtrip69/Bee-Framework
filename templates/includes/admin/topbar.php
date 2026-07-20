<nav class="navbar navbar-expand bg-body border-bottom sticky-top bee-admin-topbar">
  <div class="container-fluid px-3 px-md-4">
    <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminSidebar" aria-controls="adminSidebar" aria-label="Abrir navegación"><span class="navbar-toggler-icon"></span></button>
    <div class="ms-auto dropdown">
      <button class="btn btn-light d-flex align-items-center gap-3" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <span class="bee-admin-avatar" aria-hidden="true"><?= htmlspecialchars(strtoupper(substr((string) get_user('username'), 0, 1)), ENT_QUOTES, 'UTF-8') ?></span>
        <span class="d-none d-sm-block text-start"><strong class="d-block small"><?= htmlspecialchars((string) get_user('username'), ENT_QUOTES, 'UTF-8') ?></strong><small class="d-block text-secondary">Administrador</small></span>
        <i class="fas fa-chevron-down small text-secondary" aria-hidden="true"></i>
      </button>
      <ul class="dropdown-menu dropdown-menu-end mt-2">
        <li><a class="dropdown-item" href="admin/perfil"><i class="fas fa-user me-2" aria-hidden="true"></i>Mi perfil</a></li>
        <li><a class="dropdown-item" href="bee"><i class="fas fa-arrow-up-right-from-square me-2" aria-hidden="true"></i>Ver aplicación</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item text-danger" href="logout"><i class="fas fa-right-from-bracket me-2" aria-hidden="true"></i>Cerrar sesión</a></li>
      </ul>
    </div>
  </div>
</nav>
