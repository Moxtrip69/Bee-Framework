<?php

declare(strict_types=1);

/** @var object{title: string, applicationName: string, article: object, indexUrl: string} $d */
$escape = static fn (mixed $value): string => htmlspecialchars(
    (string) $value,
    ENT_QUOTES | ENT_SUBSTITUTE,
    'UTF-8'
);
require_once INCLUDES . 'header.php';
require_once INCLUDES . 'bee_navbar.php';
?>
  <main class="container bee-section main-wrapper">
    <article class="bee-card bee-article">
      <a class="bee-eyebrow text-decoration-none" href="<?= $escape($d->indexUrl) ?>">← Volver a artículos</a>
      <p class="text-uppercase small fw-bold text-bee mb-2"><?= $escape($d->article->status ?? '') ?></p>
      <h1 class="display-5 mb-3"><?= $escape($d->article->title ?? '') ?></h1>
      <?php if (!empty($d->article->excerpt)): ?>
        <p class="lead mb-4"><?= $escape($d->article->excerpt) ?></p>
      <?php endif; ?>
      <div class="nl2br"><?= $escape($d->article->content ?? '') ?></div>
    </article>
  </main>

<?php require_once INCLUDES . 'footer.php'; ?>
