<nav class="navbar navbar-expand-lg bee-navbar sticky-top">
  <div class="container">
    <a class="navbar-brand" href="bee" aria-label="Bee Framework, inicio">
      <span class="bee-brand-symbol" aria-hidden="true"></span>
      <span>Bee<span class="text-bee">.</span></span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#beeNavigation" aria-controls="beeNavigation" aria-expanded="false" aria-label="Abrir navegación">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="beeNavigation">
      <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
        <li class="nav-item"><a class="nav-link" href="bee">Inicio</a></li>
        <li class="nav-item"><a class="nav-link" href="examples/articles">Ejemplos</a></li>
        <li class="nav-item"><a class="nav-link" href="documentacion">Documentación</a></li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Herramientas</a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="creator">Creator</a></li>
            <li><a class="dropdown-item" href="bee/info">Información de Bee</a></li>
            <li><a class="dropdown-item" href="bee/password">Generar contraseña</a></li>
            <li><a class="dropdown-item" href="<?= htmlspecialchars(build_url('bee/generate-user'), ENT_QUOTES, 'UTF-8') ?>">Crear usuario local</a></li>
          </ul>
        </li>
        <li class="nav-item ms-lg-2"><a class="btn btn-sm btn-primary" href="<?= is_logged() ? 'admin' : 'login' ?>"><?= is_logged() ? 'Administrar' : 'Ingresar' ?></a></li>
      </ul>
    </div>
  </div>
</nav>
