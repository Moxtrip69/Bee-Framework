<?php require_once INCLUDES . 'header.php'; ?>
<?php require_once INCLUDES . 'bee_navbar.php'; ?>

<main class="container bee-section main-wrapper">
  <header class="bee-page-header">
    <span class="bee-eyebrow">Diagnóstico local</span>
    <h1>Información de Bee</h1>
    <p>Versiones, rutas y parámetros activos en esta instancia. Esta pantalla solo está disponible en entorno local.</p>
  </header>
  <?= Flasher::flash(); ?>
  <section class="bee-card" aria-labelledby="info-title">
    <div class="bee-card-heading"><div><span class="bee-eyebrow">Configuración efectiva</span><h2 id="info-title">Estado del framework</h2></div></div>
    <dl class="bee-data-list bee-info-list">
      <?php foreach ($d as $key => $value) : ?>
        <?php $displayValue = is_scalar($value) || $value === null ? (string) $value : (string) json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>
        <div><dt><?= htmlspecialchars((string) $key, ENT_QUOTES, 'UTF-8') ?></dt><dd><code><?= htmlspecialchars($displayValue, ENT_QUOTES, 'UTF-8') ?></code></dd></div>
      <?php endforeach; ?>
    </dl>
  </section>
</main>

<?php require_once INCLUDES . 'footer.php'; ?>
