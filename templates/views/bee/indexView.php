<?php require_once INCLUDES . 'header.php'; ?>
<?php require_once INCLUDES . 'bee_navbar.php'; ?>

<main class="container bee-section main-wrapper">
  <section class="bee-message-card">
    <span class="bee-brand-symbol bee-message-mark" aria-hidden="true"></span>
    <span class="bee-eyebrow">Bee Framework</span>
    <h1><?= htmlspecialchars((string) $d->msg, ENT_QUOTES, 'UTF-8') ?></h1>
    <p>Una vista mínima lista para convertirse en el inicio de tu aplicación.</p>
    <a href="bee" class="btn btn-primary">Explorar el framework</a>
  </section>
</main>

<?php require_once INCLUDES . 'footer.php'; ?>
