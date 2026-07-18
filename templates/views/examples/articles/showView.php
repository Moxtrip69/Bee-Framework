<?php

declare(strict_types=1);

/** @var array{title: string, applicationName: string, article: array<string, mixed>, indexUrl: string} $data */
$escape = static fn (mixed $value): string => htmlspecialchars(
    (string) $value,
    ENT_QUOTES | ENT_SUBSTITUTE,
    'UTF-8'
);
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $escape($data['title']) ?> - <?= $escape($data['applicationName']) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= $escape(CSS . 'main.min.css?v=' . get_asset_version()) ?>" rel="stylesheet">
</head>
<body>
  <main class="container py-5">
    <article class="bee-card bee-article">
      <a class="bee-eyebrow text-decoration-none" href="<?= $escape($data['indexUrl']) ?>">← Volver a artículos</a>
      <p class="text-uppercase small fw-bold text-bee mb-2"><?= $escape($data['article']['status'] ?? '') ?></p>
      <h1 class="display-5 mb-3"><?= $escape($data['article']['title'] ?? '') ?></h1>
      <?php if (!empty($data['article']['excerpt'])): ?>
        <p class="lead mb-4"><?= $escape($data['article']['excerpt']) ?></p>
      <?php endif; ?>
      <div class="nl2br"><?= $escape($data['article']['content'] ?? '') ?></div>
    </article>
  </main>
</body>
</html>
