<?php require_once INCLUDES . 'header.php'; ?>

<main class="bee-auth min-vh-100 d-flex align-items-center py-5">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12 col-xl-10">
        <div class="card border-0 overflow-hidden bee-auth-card">
          <div class="row g-0">
            <section class="col-12 col-lg-5 d-none d-lg-flex flex-column justify-content-between p-5 bee-auth-story" aria-label="Bee Framework">
              <a class="d-inline-flex align-items-center gap-3 text-decoration-none text-reset" href="bee">
                <span class="bee-brand-symbol" aria-hidden="true"></span>
                <span class="fw-bold">Bee Framework</span>
              </a>
              <div class="py-5">
                <span class="badge text-bg-warning mb-4">Tu entorno de desarrollo</span>
                <h1 class="display-5 text-white mb-4">Vuelve a construir.</h1>
                <p class="fs-5 mb-0">Accede a las herramientas y recursos de tu aplicación desde una experiencia coherente con Bee.</p>
              </div>
              <div class="d-flex align-items-center gap-2 small"><span class="bee-status-dot" aria-hidden="true"></span> Instancia disponible</div>
            </section>

            <section class="col-12 col-lg-7 bg-body p-4 p-sm-5" aria-labelledby="login-title">
              <div class="mx-auto bee-auth-form">
                <div class="d-flex d-lg-none align-items-center gap-3 mb-5">
                  <span class="bee-brand-symbol" aria-hidden="true"></span>
                  <span class="fw-bold">Bee Framework</span>
                </div>

                <span class="d-inline-block text-uppercase small fw-bold text-secondary mb-2">Acceso seguro</span>
                <h2 class="display-6 mb-3" id="login-title"><?= htmlspecialchars((string) $d->title, ENT_QUOTES, 'UTF-8') ?></h2>
                <p class="mb-4">Introduce tus credenciales para continuar a tu espacio de trabajo.</p>

                <?= Flasher::flash(); ?>

                <?php if (is_demo() || is_local()): ?>
                  <aside class="alert alert-warning d-flex align-items-start gap-3" aria-label="Credenciales de desarrollo">
                    <i class="fas fa-flask mt-1" aria-hidden="true"></i>
                    <div><strong class="d-block">Acceso de desarrollo</strong><span class="small">Usuario <code>bee</code> · Contraseña <code>123456</code></span></div>
                  </aside>
                <?php endif; ?>

                <form action="login/post_login" method="post" novalidate>
                  <?= insert_inputs(); ?>

                  <div class="mb-4">
                    <label class="form-label" for="usuario">Usuario</label>
                    <div class="input-group">
                      <span class="input-group-text" aria-hidden="true"><i class="fas fa-user"></i></span>
                      <input class="form-control" type="text" id="usuario" name="usuario" placeholder="Tu usuario" autocomplete="username" maxlength="120" autofocus required>
                    </div>
                  </div>

                  <div class="mb-4">
                    <label class="form-label" for="password">Contraseña</label>
                    <div class="input-group">
                      <span class="input-group-text" aria-hidden="true"><i class="fas fa-lock"></i></span>
                      <input class="form-control" type="password" id="password" name="password" placeholder="Tu contraseña" autocomplete="current-password" required>
                    </div>
                  </div>

                  <button class="btn btn-primary btn-lg w-100" type="submit"><i class="fas fa-fingerprint me-2" aria-hidden="true"></i>Ingresar</button>
                </form>

                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-4 pt-4 border-top small">
                  <a class="text-decoration-none" href="bee"><i class="fas fa-arrow-left me-1" aria-hidden="true"></i>Volver al inicio</a>
                  <?php if (is_demo() || is_local()): ?><a class="text-decoration-none" href="<?= htmlspecialchars(build_url('bee/generate-user'), ENT_QUOTES, 'UTF-8') ?>">Crear usuario local</a><?php endif; ?>
                </div>
              </div>
            </section>
          </div>
        </div>
        <p class="text-center small text-secondary mt-4 mb-0">Bee <?= htmlspecialchars(get_bee_version(), ENT_QUOTES, 'UTF-8') ?> · Autenticación protegida con CSRF</p>
      </div>
    </div>
  </div>
</main>

<?php require_once INCLUDES . 'scripts.php'; ?>
</body>
</html>
