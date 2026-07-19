<?php

declare(strict_types=1);

/** @var object{title: string, applicationName: string, indexUrl: string} $d */
$escape = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
require_once INCLUDES . 'header.php';
require_once INCLUDES . 'bee_navbar.php';
?>
  <main class="container bee-section main-wrapper text-center">
    <section class="bee-card bee-not-found">
      <p class="bee-eyebrow">Error 404</p>
      <h1>Artículo no encontrado</h1>
      <p>El artículo no existe o todavía no está publicado.</p>
      <a class="btn btn-primary" href="<?= $escape($d->indexUrl) ?>">Volver al CRUD</a>
    </section>
  </main>

<?php require_once INCLUDES . 'footer.php'; ?>
