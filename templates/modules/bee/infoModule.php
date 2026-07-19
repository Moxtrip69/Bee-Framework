<?php require_once INCLUDES . 'header.php'; ?>
<?php require_once INCLUDES . 'bee_navbar.php'; ?>

<?php
$escape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$info = is_array($d) ? $d : get_object_vars($d);
$value = static fn (string $key, string $fallback = 'No disponible'): string => isset($info[$key])
  ? (string) $info[$key]
  : $fallback;
$groups = [
  'environment' => [
    'title' => 'Entorno',
    'description' => 'Modo de ejecución y localización de la solicitud.',
    'icon' => 'fa-sliders',
    'keys' => ['Entorno local', 'Demostración', 'Sandbox', 'Charset', 'Lenguaje', 'Puerto personalizado'],
  ],
  'application' => [
    'title' => 'Aplicación',
    'description' => 'Identidad y recursos públicos del proyecto.',
    'icon' => 'fa-window-maximize',
    'keys' => ['Nombre del sitio', 'Versión del sitio', 'Plantilla de correos', 'Favicon del sitio', 'Logotipo del sitio'],
  ],
  'urls' => [
    'title' => 'URLs y recursos',
    'description' => 'Direcciones resueltas por el framework para esta instancia.',
    'icon' => 'fa-link',
    'keys' => ['URL del sitio', 'URL actual', 'Path base', 'URL de recursos', 'URL de subidas', 'URL de imágenes'],
  ],
  'database' => [
    'title' => 'Base de datos',
    'description' => 'Motores y conexiones configuradas para cada entorno.',
    'icon' => 'fa-database',
    'keys' => ['Versión MySQL', 'DB Engine', 'DB Host', 'DB Nombre', 'DB Usuario', 'DB Charset', 'DB Engine (local)', 'DB Host (local)', 'DB Nombre (local)', 'DB Usuario (local)', 'DB Charset (local)'],
  ],
  'paths' => [
    'title' => 'Directorios',
    'description' => 'Rutas absolutas utilizadas por servicios y cargadores.',
    'icon' => 'fa-folder-tree',
    'keys' => ['Raíz', 'App', 'Templates', 'Configuración', 'Controladores', 'Modelos', 'Clases', 'Funciones', 'Logs', 'Includes', 'Módulos', 'Vistas', 'Imágenes', 'Subidas'],
  ],
  'security' => [
    'title' => 'Seguridad',
    'description' => 'Confirmación de secretos configurados sin exponer su contenido.',
    'icon' => 'fa-shield-halved',
    'keys' => ['Sal de seguridad'],
  ],
];
?>

<main class="container bee-section main-wrapper bee-info-page">
  <header class="row g-4 align-items-end mb-5">
    <div class="col-12 col-lg-8">
      <span class="d-inline-flex align-items-center gap-2 text-uppercase small fw-bold text-secondary mb-3"><span class="bee-status-dot" aria-hidden="true"></span> Diagnóstico local</span>
      <h1 class="display-4 mb-3">Tu instancia, de un vistazo</h1>
      <p class="lead mb-0">Versiones, entorno y rutas resueltas por Bee. Esta pantalla está disponible únicamente durante el desarrollo local.</p>
    </div>
    <div class="col-12 col-lg-4 d-flex justify-content-lg-end">
      <a class="btn btn-outline-secondary" href="bee"><i class="fas fa-arrow-left me-2" aria-hidden="true"></i>Volver al inicio</a>
    </div>
  </header>

  <?= Flasher::flash(); ?>

  <section class="row row-cols-1 row-cols-sm-2 row-cols-xl-4 g-3 mb-5" aria-label="Resumen del entorno">
    <?php foreach ([
      ['Bee', $value('Versión Bee'), 'fa-cubes'],
      ['PHP', $value('Versión PHP'), 'fa-code'],
      ['MySQL', $value('Versión MySQL'), 'fa-database'],
      ['Entorno', $value('Entorno local') === 'Si' ? 'Local' : 'Remoto', 'fa-laptop-code'],
    ] as [$label, $summaryValue, $icon]): ?>
      <div class="col">
        <article class="card h-100 border-0 bee-info-summary">
          <div class="card-body d-flex align-items-center gap-3 p-4">
            <span class="bee-info-icon flex-shrink-0" aria-hidden="true"><i class="fas <?= $escape($icon) ?>"></i></span>
            <div class="overflow-hidden"><span class="d-block text-secondary small fw-bold text-uppercase"><?= $escape($label) ?></span><strong class="d-block fs-5 text-break"><?= $escape($summaryValue) ?></strong></div>
          </div>
        </article>
      </div>
    <?php endforeach; ?>
  </section>

  <nav class="d-flex flex-wrap gap-2 mb-4" aria-label="Secciones de información">
    <?php foreach ($groups as $id => $group): ?>
      <a class="btn btn-sm btn-outline-secondary" href="#info-<?= $escape($id) ?>"><?= $escape($group['title']) ?></a>
    <?php endforeach; ?>
  </nav>

  <div class="row g-4">
    <?php foreach ($groups as $id => $group): ?>
      <div class="col-12 <?= $id === 'paths' ? '' : 'col-xl-6' ?>">
        <section class="card h-100 bee-info-section" id="info-<?= $escape($id) ?>" aria-labelledby="info-<?= $escape($id) ?>-title">
          <div class="card-body p-0">
            <header class="d-flex align-items-start gap-3 p-4 border-bottom">
              <span class="bee-info-icon flex-shrink-0" aria-hidden="true"><i class="fas <?= $escape($group['icon']) ?>"></i></span>
              <div><h2 class="h4 mb-1" id="info-<?= $escape($id) ?>-title"><?= $escape($group['title']) ?></h2><p class="small mb-0"><?= $escape($group['description']) ?></p></div>
            </header>
            <dl class="list-group list-group-flush mb-0">
              <?php foreach ($group['keys'] as $key): ?>
                <?php if (!array_key_exists($key, $info)) {
                  continue;
                } ?>
                <?php $displayValue = is_scalar($info[$key]) || $info[$key] === null ? (string) $info[$key] : (string) json_encode($info[$key], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>
                <div class="list-group-item d-grid gap-2 px-4 py-3 bee-info-row">
                  <dt class="text-secondary small fw-bold text-uppercase mb-0"><?= $escape($key) ?></dt>
                  <dd class="mb-0"><code class="d-block text-break"><?= $escape($displayValue) ?></code></dd>
                </div>
              <?php endforeach; ?>
            </dl>
          </div>
        </section>
      </div>
    <?php endforeach; ?>
  </div>
</main>

<?php require_once INCLUDES . 'footer.php'; ?>
