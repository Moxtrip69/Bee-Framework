<?php require_once INCLUDES . 'header.php'; ?>
<?php require_once INCLUDES . 'bee_navbar.php'; ?>

<?php $escape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>

<main class="container bee-section main-wrapper bee-creator">
  <header class="bee-page-header bee-creator-header">
    <span class="bee-eyebrow">Herramientas de desarrollo</span>
    <h1>Construye con Bee Creator</h1>
    <p>Genera componentes compatibles con el flujo clásico o el router moderno desde una interfaz y una API de servicios compartida con CLI.</p>
    <div class="bee-cli-hint"><code>php bee create:model Article --table=articles --fields=title:string,views:int</code></div>
  </header>

  <?= Flasher::flash(); ?>

  <nav class="bee-creator-tabs" aria-label="Secciones de Creator">
    <button class="active" type="button" data-creator-tab="components">Componentes</button>
    <button type="button" data-creator-tab="routes">Rutas <span><?= count($d->routes) ?></span></button>
  </nav>

  <section data-creator-panel="components">
    <div class="row g-4">
      <div class="col-12 col-xl-4">
        <form class="bee-card bee-creator-card" action="creator/post_controller" method="post">
          <?= insert_inputs(); ?>
          <div class="bee-creator-card-icon"><i class="fas fa-code" aria-hidden="true"></i></div>
          <span class="bee-eyebrow">Controller</span>
          <h2>Nuevo controlador</h2>
          <p>Elige compatibilidad legacy o acciones registradas en el router moderno.</p>
          <label class="form-label" for="controller-filename">Nombre</label>
          <input class="form-control" id="controller-filename" name="filename" placeholder="Articles" required>
          <fieldset class="bee-choice-grid mt-3">
            <legend>Tipo de controlador</legend>
            <label><input type="radio" name="type" value="modern" checked><span><strong>Moderno</strong><small>DI, rutas y renderToString</small></span></label>
            <label><input type="radio" name="type" value="legacy"><span><strong>Legacy</strong><small>Resolución automática por URL</small></span></label>
          </fieldset>
          <div class="row g-2 mt-2" data-modern-route-fields>
            <div class="col-8"><label class="form-label" for="controller-route-path">Ruta inicial</label><input class="form-control" id="controller-route-path" name="route_path" placeholder="/articles"></div>
            <div class="col-4"><label class="form-label" for="controller-route-method">Verbo</label><select class="form-select" id="controller-route-method" name="route_method"><option>GET</option><option>POST</option></select></div>
            <div class="col-12"><label class="form-label" for="controller-route-name">Nombre de ruta</label><input class="form-control" id="controller-route-name" name="route_name" placeholder="articles.index"></div>
          </div>
          <div class="d-flex flex-wrap gap-3 mt-3">
            <label class="form-check"><input class="form-check-input" type="checkbox" name="generate_view" checked> <span class="form-check-label">Vista inicial</span></label>
            <label class="form-check"><input class="form-check-input" type="checkbox" name="use_twig"> <span class="form-check-label">Twig</span></label>
          </div>
          <button class="btn btn-primary w-100 mt-4" type="submit">Crear controlador <span aria-hidden="true">→</span></button>
        </form>
      </div>

      <div class="col-12 col-xl-4">
        <form class="bee-card bee-creator-card" action="creator/post_model" method="post">
          <?= insert_inputs(); ?>
          <div class="bee-creator-card-icon"><i class="fas fa-database" aria-hidden="true"></i></div>
          <span class="bee-eyebrow">BeeModel ORM</span>
          <h2>Nuevo modelo</h2>
          <p>Configura asignación masiva, casts y timestamps desde una definición concisa.</p>
          <label class="form-label" for="model-filename">Nombre</label>
          <input class="form-control" id="model-filename" name="filename" placeholder="Article" required>
          <label class="form-label mt-3" for="model-table">Tabla</label>
          <input class="form-control" id="model-table" name="table" placeholder="articles (automática si se omite)">
          <label class="form-label mt-3" for="model-fields">Campos y casts</label>
          <textarea class="form-control" id="model-fields" name="fields" rows="3" placeholder="title:string, views:int, metadata:json"></textarea>
          <small class="form-text">Casts: string, int, float, bool, array o json.</small>
          <label class="form-check mt-3"><input class="form-check-input" type="checkbox" name="timestamps" checked> <span class="form-check-label">Administrar created_at y updated_at</span></label>
          <button class="btn btn-primary w-100 mt-4" type="submit">Crear modelo <span aria-hidden="true">→</span></button>
        </form>
      </div>

      <div class="col-12 col-xl-4">
        <form class="bee-card bee-creator-card" action="creator/post_view" method="post">
          <?= insert_inputs(); ?>
          <div class="bee-creator-card-icon"><i class="fas fa-window-maximize" aria-hidden="true"></i></div>
          <span class="bee-eyebrow">View</span>
          <h2>Nueva vista</h2>
          <p>Crea vistas PHP o Twig, incluso dentro de directorios anidados.</p>
          <label class="form-label" for="view-controller">Carpeta del controlador</label>
          <input class="form-control" id="view-controller" name="controller" list="creator-controller-list" placeholder="articles" required>
          <datalist id="creator-controller-list"><?php foreach ($d->controllers as $controller): ?><option value="<?= $escape($controller) ?>"><?php endforeach; ?></datalist>
          <label class="form-label mt-3" for="view-name">Nombre de vista</label>
          <input class="form-control" id="view-name" name="view_name" placeholder="index o admin/index" required>
          <label class="form-check mt-3"><input class="form-check-input" type="checkbox" name="use_twig" <?= USE_TWIG ? 'checked' : '' ?>> <span class="form-check-label">Usar motor Twig</span></label>
          <button class="btn btn-primary w-100 mt-4" type="submit">Crear vista <span aria-hidden="true">→</span></button>
        </form>
      </div>
    </div>
  </section>

  <section class="d-none" data-creator-panel="routes">
    <div class="bee-card bee-route-editor" id="creator-route-editor">
      <div class="bee-card-heading"><div><span class="bee-eyebrow">Manifest administrado</span><h2>Agregar o editar ruta nombrada</h2></div><button class="btn btn-sm btn-outline-secondary" type="button" data-route-reset>Limpiar</button></div>
      <form action="creator/post_route" method="post" data-route-form>
        <?= insert_inputs(); ?><input type="hidden" name="route_id" value="">
        <div class="row g-3">
          <div class="col-12 col-lg-3"><label class="form-label">Métodos</label><div class="bee-method-picker"><?php foreach ($d->httpMethods as $method): ?><label><input type="checkbox" name="methods[]" value="<?= $escape($method) ?>" <?= $method === 'GET' ? 'checked' : '' ?>><span><?= $escape($method) ?></span></label><?php endforeach; ?></div></div>
          <div class="col-12 col-md-6 col-lg-3"><label class="form-label" for="route-path">Ruta</label><input class="form-control" id="route-path" name="path" placeholder="/articles/{id}" required></div>
          <div class="col-12 col-md-6 col-lg-3"><label class="form-label" for="route-name">Nombre</label><input class="form-control" id="route-name" name="name" placeholder="articles.show" required></div>
          <div class="col-12 col-md-6 col-lg-3"><label class="form-label" for="route-controller">Controller</label><input class="form-control" id="route-controller" name="controller" placeholder="articlesController" required></div>
          <div class="col-12 col-md-6 col-lg-3"><label class="form-label" for="route-action">Método</label><input class="form-control" id="route-action" name="action" placeholder="show" required></div>
          <div class="col-12 col-md-6 col-lg-3"><label class="form-label" for="route-middleware">Middleware</label><input class="form-control" id="route-middleware" name="middleware" placeholder="auth, api"></div>
          <div class="col-12 col-md-6 col-lg-3"><label class="form-label" for="route-parameter">Parámetro restringido</label><input class="form-control" id="route-parameter" name="constraint_parameter" placeholder="id"></div>
          <div class="col-12 col-md-6 col-lg-3"><label class="form-label" for="route-expression">Expresión regular</label><input class="form-control" id="route-expression" name="constraint_expression" placeholder="\d+"></div>
          <div class="col-12 col-lg-3 d-flex align-items-end"><button class="btn btn-primary w-100" type="submit">Guardar ruta</button></div>
        </div>
      </form>
    </div>

    <div class="bee-route-list mt-4">
      <?php foreach ($d->routes as $route): ?>
        <article class="bee-route-row">
          <div><span class="bee-method-badge"><?= $escape($route->methods) ?></span></div>
          <div><code><?= $escape($route->path) ?></code><small><?= $escape($route->action) ?></small></div>
          <div><strong><?= $escape($route->name ?: 'Sin nombre') ?></strong><small><?= $escape($route->middleware ?: 'Sin middleware') ?></small></div>
          <div class="bee-route-actions">
            <?php if ($route->managed instanceof stdClass): $managed = $route->managed; ?>
              <button class="btn btn-sm btn-outline-secondary" type="button" data-route-edit='<?= $escape(json_encode($managed, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)) ?>'>Editar</button>
              <form action="creator/delete_route" method="post" data-route-delete><?= insert_inputs(); ?><input type="hidden" name="route_id" value="<?= $escape($managed->id) ?>"><button class="btn btn-sm btn-outline-danger" type="submit">Borrar</button></form>
            <?php else: ?><span class="bee-route-source">Código</span><?php endif; ?>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </section>
</main>

<?php require_once INCLUDES . 'footer.php'; ?>
