<?php

declare(strict_types=1);

/** @var array{title: string, applicationName: string, indexUrl: string} $data */
$escape = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
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
  <main class="container py-5 text-center">
    <section class="bee-card bee-not-found">
      <p class="bee-eyebrow">Error 404</p>
      <h1>Artículo no encontrado</h1>
      <p>El artículo no existe o todavía no está publicado.</p>
      <a class="btn btn-primary" href="<?= $escape($data['indexUrl']) ?>">Volver al CRUD</a>
    </section>
  </main>
</body>
</html>
