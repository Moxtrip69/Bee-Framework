<?php require_once INCLUDES . 'header.php'; ?>
<?php require_once INCLUDES . 'bee_navbar.php'; ?>

<main class="main-wrapper bee-docs">
  <section class="bee-docs-hero border-bottom"><div class="container py-5"><div class="row align-items-end g-4">
    <div class="col-12 col-lg-8"><span class="badge text-bg-primary mb-3">Bee Framework <?= htmlspecialchars(get_bee_version(), ENT_QUOTES, 'UTF-8') ?></span><h1 class="display-4 mb-3">Documentación para construir sin ruido.</h1><p class="lead text-secondary mb-0">Aprende el núcleo moderno y conserva compatibilidad con tus módulos existentes.</p></div>
    <div class="col-12 col-lg-4"><div class="d-flex flex-wrap gap-2 justify-content-lg-end"><a class="btn btn-primary" href="creator"><i class="fas fa-wand-magic-sparkles me-2" aria-hidden="true"></i>Abrir Creator</a><a class="btn btn-outline-secondary" href="examples/articles">Ver CRUD</a></div></div>
  </div></div></section>
  <div class="container py-5"><div class="row g-5">
    <aside class="col-12 col-lg-3"><div class="bee-docs-nav sticky-lg-top"><p class="text-uppercase small fw-bold text-secondary px-3 mb-2">En esta guía</p><nav class="nav nav-pills flex-column" aria-label="Contenido de la documentación">
      <?php foreach (['inicio' => 'Primeros pasos', 'estructura' => 'Estructura', 'configuracion' => 'Configuración', 'rutas' => 'Routing moderno', 'controladores' => 'Controladores y vistas', 'modelos' => 'Modelos y consultas', 'api' => 'APIs y frontend', 'cli' => 'CLI', 'seguridad' => 'Seguridad', 'compatibilidad' => 'Compatibilidad', 'actualizaciones' => 'Novedades'] as $anchor => $label): ?><a class="nav-link" href="<?= htmlspecialchars(new_anchor($anchor), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></a><?php endforeach; ?>
    </nav></div></aside>
    <div class="col-12 col-lg-9"><?php echo get_module('bee/guide'); ?></div>
  </div></div>
</main>
<?php require_once INCLUDES . 'footer.php'; ?>
