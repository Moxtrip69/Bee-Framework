<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container">
    <a class="navbar-brand" href="<?php echo get_base_url(); ?>">
      <img src="<?php echo get_bee_logo(); ?>" alt="<?php echo get_bee_name(); ?>" width="100px">
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link" aria-current="page" href="bee">Inicio</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="tienda">Tienda</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="utilidades" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Herramientas
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="utilidades">
            <li><a class="dropdown-item" href="https://github.com/Moxtrip69/Bee-Framework/tree/1.5.8" target="_blank">Changelog</a></li>
            <li><a class="dropdown-item" href="documentacion">Documentación</a></li>
            <li><a class="dropdown-item" href="bee/info">Bee info</a></li>
            <li><a class="dropdown-item" href="bee/password">Generar contraseña</a></li>
            <li><a class="dropdown-item" href="<?php echo build_url('bee/generate-user'); ?>">Crear nuevo usuario</a></li>
            <li><a class="dropdown-item" href="<?php echo build_url('bee/regenerate'); ?>">Regenerar credenciales</a></li>
            <li><a class="dropdown-item" href="https://bit.ly/cursos-gratuitos-ajs">Cursos Gratuitos</a></li>
          </ul>
        </li>
        <?php if (is_logged()): ?>
          <li class="nav-item">
            <a class="nav-link" href="admin">Administración</a>
          </li>
        <?php else: ?>
          <li class="nav-item ms-2">
            <a class="btn btn-primary" href="login">Ingresar</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>