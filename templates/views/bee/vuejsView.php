<?php require_once INCLUDES . 'header.php'; ?>
<?php require_once INCLUDES . 'bee_navbar.php'; ?>

<main class="container bee-section main-wrapper">
  <header class="bee-page-header">
    <span class="bee-eyebrow">Vue 3 + Bee API</span>
    <h1><?= htmlspecialchars((string) $d->title, ENT_QUOTES, 'UTF-8') ?></h1>
    <p>Ejemplo reactivo que consume la API del framework y conserva el JavaScript desacoplado de la vista.</p>
  </header>
  <?= Flasher::flash(); ?>
  <section class="bee-demo-stage" aria-label="Aplicación de ejemplo con Vue">
    <div id="mainApp"></div>
  </section>
</main>

<?php require_once INCLUDES . 'footer.php'; ?>
