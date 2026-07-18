<?php
$currentBeeRoute = trim((string) ($_GET['uri'] ?? ''), '/');
$isBeeHome = $currentBeeRoute === '' || $currentBeeRoute === 'bee';
$isExamples = str_starts_with($currentBeeRoute, 'examples/');
$isDocs = str_starts_with($currentBeeRoute, 'documentacion');
$isTools = str_starts_with($currentBeeRoute, 'creator')
  || str_starts_with($currentBeeRoute, 'bee/info')
  || str_starts_with($currentBeeRoute, 'bee/password');
?>

<nav class="navbar navbar-expand-lg bee-navbar sticky-top" aria-label="Navegación principal de Bee">
  <div class="container">
    <a class="navbar-brand" href="bee" aria-label="Bee Framework, inicio">
      <span class="bee-brand-symbol" aria-hidden="true"></span>
      <span class="bee-brand-copy"><strong>Bee</strong><small>Framework</small></span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#beeNavigation" aria-controls="beeNavigation" aria-expanded="false" aria-label="Abrir navegación">
      <span class="bee-menu-lines" aria-hidden="true"></span>
    </button>
    <div class="collapse navbar-collapse" id="beeNavigation">
      <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
        <li class="nav-item"><a class="nav-link<?= $isBeeHome ? ' active' : '' ?>" href="bee"<?= $isBeeHome ? ' aria-current="page"' : '' ?>>Inicio</a></li>
        <li class="nav-item"><a class="nav-link<?= $isExamples ? ' active' : '' ?>" href="examples/articles"<?= $isExamples ? ' aria-current="page"' : '' ?>>Ejemplos</a></li>
        <li class="nav-item"><a class="nav-link<?= $isDocs ? ' active' : '' ?>" href="documentacion"<?= $isDocs ? ' aria-current="page"' : '' ?>>Documentación</a></li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle<?= $isTools ? ' active' : '' ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Herramientas</a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><span class="dropdown-header">Desarrollo local</span></li>
            <li><a class="dropdown-item" href="creator"><i class="fas fa-wand-magic-sparkles" aria-hidden="true"></i><span><strong>Creator</strong><small>Genera componentes</small></span></a></li>
            <li><a class="dropdown-item" href="bee/info"><i class="fas fa-sliders" aria-hidden="true"></i><span><strong>Información</strong><small>Estado de la instancia</small></span></a></li>
            <li><a class="dropdown-item" href="bee/password"><i class="fas fa-key" aria-hidden="true"></i><span><strong>Contraseñas</strong><small>Genera credenciales</small></span></a></li>
          </ul>
        </li>
        <li class="nav-item ms-lg-2"><a class="btn btn-sm btn-primary bee-navbar-cta" href="<?= is_logged() ? 'admin' : 'login' ?>"><?= is_logged() ? 'Administrar' : 'Ingresar' ?><span aria-hidden="true">→</span></a></li>
      </ul>
    </div>
  </div>
</nav>
