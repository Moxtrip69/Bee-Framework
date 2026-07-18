<?php require_once INCLUDES . 'header.php'; ?>
<?php require_once INCLUDES . 'bee_navbar.php'; ?>

<main class="container bee-section main-wrapper">
  <header class="bee-page-header">
    <span class="bee-eyebrow">Herramienta local</span>
    <h1>Generador de contraseñas</h1>
    <p>Crea una contraseña y su hash compatible con la autenticación actual de Bee.</p>
  </header>
  <?= Flasher::flash(); ?>
  <div class="row g-4">
    <div class="col-12 col-lg-4">
      <form class="bee-card" action="bee/password" method="post">
        <label for="password" class="form-label">Contraseña personalizada</label>
        <input class="form-control" type="text" id="password" name="password" minlength="8" autocomplete="new-password" placeholder="Mínimo 8 caracteres">
        <p class="small text-secondary mt-2">Déjala vacía para generar una automáticamente.</p>
        <button class="btn btn-primary w-100 mt-3">Generar credencial</button>
      </form>
    </div>
    <div class="col-12 col-lg-8">
      <section class="bee-card bee-credential" aria-labelledby="credential-title">
        <h2 id="credential-title" class="h4">Resultado</h2>
        <div class="bee-data-row"><span>Contraseña</span><code><?= htmlspecialchars($d->pw->password, ENT_QUOTES, 'UTF-8') ?></code></div>
        <div class="bee-data-row"><span>Hash</span><code class="text-break"><?= htmlspecialchars($d->pw->hash, ENT_QUOTES, 'UTF-8') ?></code></div>
      </section>
    </div>
  </div>
</main>

<?php require_once INCLUDES . 'footer.php'; ?>
