<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="<?php echo URL; ?>"><img src="<?php echo get_bee_logo(); ?>" alt="<?php echo get_bee_name(); ?>" width="100px"></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" aria-current="page" href="bee">Inicio</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="https://github.com/Moxtrip69/Bee-Framework/tree/1.5.0" target="_blank">Documentación</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="https://github.com/Moxtrip69/Bee-Framework/tree/1.5.0#v-150" target="_blank">Changelog</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="bee/info">Bee info</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="bee/password">Password</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo build_url('bee/regenerate'); ?>">Regenerar API keys</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="main_wrapper" style="min-height: 95vh;">