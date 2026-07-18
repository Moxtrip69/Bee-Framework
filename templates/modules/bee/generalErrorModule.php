<?php require_once INCLUDES . 'header.php'; ?>

<main class="container bee-error-page main-wrapper">
  <section class="bee-error-card" role="alert" aria-labelledby="error-title">
    <span class="bee-error-code">Error</span>
    <span class="bee-brand-symbol bee-error-mark" aria-hidden="true"></span>
    <h1 id="error-title">Algo interrumpió el vuelo</h1>
    <p>No pudimos completar la operación. Comparte el siguiente detalle con el equipo responsable.</p>
    <pre class="bee-error-detail"><?= htmlspecialchars((string) $d->error, ENT_QUOTES, 'UTF-8') ?></pre>
    <?= Flasher::flash(); ?>
    <a class="btn btn-primary" href="<?= htmlspecialchars(get_base_url(), ENT_QUOTES, 'UTF-8') ?>">Volver al inicio</a>
  </section>
</main>

<?php require_once INCLUDES . 'footer.php'; ?>
