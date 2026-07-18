<?php require_once INCLUDES . 'header.php'; ?>
<?php require_once INCLUDES . 'bee_navbar.php'; ?>

<main class="main-wrapper bee-home">
  <section class="bee-hero">
    <div class="container">
      <div class="row align-items-center g-5">
        <div class="col-12 col-lg-7">
          <span class="bee-eyebrow"><span class="bee-status-dot"></span> Bee Framework <?= htmlspecialchars(get_bee_version()) ?></span>
          <h1>Construye productos claros.<br><span class="text-bee">Hazlos volar.</span></h1>
          <p class="bee-hero-copy">Un framework PHP ligero y modular para aplicaciones web, APIs, CLI y procesos programados, con una arquitectura que puede crecer contigo.</p>
          <?= Flasher::flash(); ?>
          <div class="d-flex flex-wrap gap-2 mt-4">
            <a href="documentacion" class="btn btn-primary">Explorar documentación</a>
            <a href="creator" class="btn btn-outline-secondary">Crear componente</a>
            <a href="https://github.com/Moxtrip69/Bee-Framework/tree/<?= rawurlencode(get_bee_version()) ?>" class="btn btn-link" target="_blank" rel="noopener noreferrer">Ver en GitHub ↗</a>
          </div>
          <dl class="bee-stats mt-5">
            <div><dt>PHP</dt><dd>8.2+</dd></div>
            <div><dt>Core</dt><dd><?= htmlspecialchars(get_core_version()) ?></dd></div>
            <div><dt>Arquitectura</dt><dd>Modular</dd></div>
          </dl>
        </div>
        <div class="col-12 col-lg-5">
          <div class="bee-hero-mark" aria-hidden="true">
            <span class="bee-wing bee-wing-left"></span><span class="bee-body-mark"></span><span class="bee-wing bee-wing-right"></span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="container bee-section">
    <div class="bee-section-heading">
      <span class="bee-eyebrow">Tu espacio de trabajo</span>
      <h2>Todo lo necesario, sin ruido.</h2>
      <p>Accede a las herramientas principales y ejemplos incluidos en esta instalación.</p>
    </div>
    <div class="row g-3">
      <?php
      $items = [
        ['Creator', 'Genera controladores, modelos y vistas.', 'creator', 'fa-wand-magic-sparkles'],
        ['Artículos', 'Prueba el router y ORM modernos.', 'examples/articles', 'fa-route'],
        ['Vue JS', 'Explora la integración reactiva.', 'bee/vuejs', 'fa-bolt'],
        ['Información', 'Consulta versiones y configuración.', 'bee/info', 'fa-sliders'],
        ['Contraseñas', 'Genera credenciales seguras.', 'bee/password', 'fa-key'],
        [is_logged() ? 'Administración' : 'Ingresar', is_logged() ? 'Gestiona la aplicación.' : 'Accede a tu cuenta.', is_logged() ? 'admin' : 'login', 'fa-user'],
      ];
      foreach ($items as [$title, $description, $href, $icon]): ?>
        <div class="col-12 col-md-6 col-xl-4">
          <a class="bee-tool-card" href="<?= htmlspecialchars($href, ENT_QUOTES, 'UTF-8') ?>">
            <span class="bee-tool-icon"><i class="fas <?= htmlspecialchars($icon) ?>"></i></span>
            <span><strong><?= htmlspecialchars($title) ?></strong><small><?= htmlspecialchars($description) ?></small></span>
            <span class="bee-tool-arrow">→</span>
          </a>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
</main>

<?php require_once INCLUDES . 'footer.php'; ?>
