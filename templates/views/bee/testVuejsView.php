<?php require_once INCLUDES . 'header.php'; ?>
<?php require_once INCLUDES . 'bee_navbar.php'; ?>

<main class="container bee-section main-wrapper">
  <header class="bee-page-header">
    <span class="bee-eyebrow">Componente independiente</span>
    <h1><?= htmlspecialchars((string) $d->title, ENT_QUOTES, 'UTF-8') ?></h1>
    <p>Un punto de montaje aislado para integrar componentes Vue donde realmente se necesiten.</p>
  </header>
  <?= Flasher::flash(); ?>
  <section class="bee-demo-stage" aria-label="Componente de ejemplo con Vue">
    <div id="testApp"></div>
  </section>
</main>

<?php require_once INCLUDES . 'footer.php'; ?>
