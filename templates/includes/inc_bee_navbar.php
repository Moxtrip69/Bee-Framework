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
          <a class="nav-link" href="https://github.com/Moxtrip69/Bee-Framework/tree/1.5.5" target="_blank">Documentación</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="https://github.com/Moxtrip69/Bee-Framework/tree/1.5.5#v-155" target="_blank">Changelog</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="utilidades" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Utilidades
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="utilidades">
            <li><a class="dropdown-item" href="bee/info">Bee info</a></li>
            <li><a class="dropdown-item" href="bee/password">Generar Password</a></li>
            <li><a class="dropdown-item" href="<?php echo build_url('bee/generate-user'); ?>">Generar Usuario</a></li>
            <li><a class="dropdown-item" href="<?php echo build_url('bee/regenerate'); ?>">Regenerar API keys</a></li>
            <li><a class="dropdown-item" href="https://bit.ly/cursos-gratuitos-ajs">Cursos Gratuitos</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="main_wrapper" style="min-height: 95vh;">