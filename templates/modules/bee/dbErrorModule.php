<?php require_once INCLUDES . 'header.php'; ?>

<main class="container bee-error-page main-wrapper">
  <section class="bee-error-card" role="alert" aria-labelledby="error-title">
    <span class="bee-error-code">Base de datos</span>
    <span class="bee-brand-symbol bee-error-mark" aria-hidden="true"></span>
    <h1 id="error-title">La conexión perdió el ritmo</h1>
    <p>No fue posible completar la operación de datos. Verifica la conexión y comparte este detalle con el equipo responsable.</p>
    <pre class="bee-error-detail"><?= htmlspecialchars((string) $d->error, ENT_QUOTES, 'UTF-8') ?></pre>
    <?= Flasher::flash(); ?>
    <a class="btn btn-primary" href="<?= htmlspecialchars(get_base_url(), ENT_QUOTES, 'UTF-8') ?>">Volver al inicio</a>
  </section>
</main>

<?php require_once INCLUDES . 'footer.php'; ?>
