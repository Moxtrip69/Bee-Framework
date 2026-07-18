<?php

declare(strict_types=1);

/** @var array{title: string, applicationName: string, apiUrl: string} $data */
$escape = static fn (string $value): string => htmlspecialchars(
    $value,
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
</head>
<body class="bg-light">
  <main
    id="articles-crud"
    class="container py-5"
    data-api-url="<?= $escape($data['apiUrl']) ?>"
  >
    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
      <div>
        <h1 class="h2 mb-1">CRUD moderno de artículos</h1>
        <p class="text-secondary mb-0">Rutas Bee, BeeModel y JavaScript con async/await.</p>
      </div>
      <div class="d-flex align-items-end gap-2">
        <label class="form-label mb-0" for="articles-status-filter">Estado</label>
        <select id="articles-status-filter" class="form-select">
          <option value="">Todos</option>
          <option value="draft">Borradores</option>
          <option value="published">Publicados</option>
        </select>
      </div>
    </div>

    <div id="articles-alert" class="alert d-none" role="alert" aria-live="polite"></div>

    <div class="row g-4">
      <section class="col-12 col-lg-4" aria-labelledby="article-form-title">
        <div class="card shadow-sm">
          <div class="card-body">
            <h2 id="article-form-title" class="h5">Nuevo artículo</h2>
            <form id="article-form" novalidate>
              <input id="article-id" type="hidden">
              <div class="mb-3">
                <label class="form-label" for="article-title">Título</label>
                <input id="article-title" name="title" class="form-control" maxlength="160" required>
              </div>
              <div class="mb-3">
                <label class="form-label" for="article-slug">Slug opcional</label>
                <input id="article-slug" name="slug" class="form-control" maxlength="180">
              </div>
              <div class="mb-3">
                <label class="form-label" for="article-excerpt">Extracto</label>
                <textarea id="article-excerpt" name="excerpt" class="form-control" maxlength="255" rows="2"></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label" for="article-content">Contenido</label>
                <textarea id="article-content" name="content" class="form-control" maxlength="10000" rows="6" required></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label" for="article-status">Estado</label>
                <select id="article-status" name="status" class="form-select" required>
                  <option value="draft">Borrador</option>
                  <option value="published">Publicado</option>
                </select>
              </div>
              <div class="d-flex gap-2">
                <button id="article-submit" class="btn btn-primary" type="submit">Guardar</button>
                <button id="article-cancel" class="btn btn-outline-secondary d-none" type="button">Cancelar</button>
              </div>
            </form>
          </div>
        </div>
      </section>

      <section class="col-12 col-lg-8" aria-labelledby="articles-list-title">
        <div class="card shadow-sm">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h2 id="articles-list-title" class="h5 mb-0">Artículos</h2>
              <div id="articles-loading" class="spinner-border spinner-border-sm d-none" role="status">
                <span class="visually-hidden">Cargando...</span>
              </div>
            </div>
            <div class="table-responsive">
              <table class="table table-hover align-middle">
                <thead><tr><th>Título</th><th>Estado</th><th>Vistas</th><th class="text-end">Acciones</th></tr></thead>
                <tbody id="articles-table-body"></tbody>
              </table>
            </div>
            <p id="articles-empty" class="text-secondary text-center py-4 d-none">No hay artículos para mostrar.</p>
            <nav class="d-flex justify-content-between align-items-center" aria-label="Paginación de artículos">
              <button id="articles-previous" class="btn btn-sm btn-outline-secondary" type="button">Anterior</button>
              <span id="articles-page" class="small text-secondary"></span>
              <button id="articles-next" class="btn btn-sm btn-outline-secondary" type="button">Siguiente</button>
            </nav>
          </div>
        </div>
      </section>
    </div>
  </main>
  <script src="<?= $escape(JS . 'examples/articlesCrud.js') ?>" defer></script>
</body>
</html>
