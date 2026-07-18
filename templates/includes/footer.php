<footer class="bee-footer">
  <div class="container">
    <div class="bee-footer-main">
      <div class="bee-footer-brand">
        <a href="bee" class="bee-footer-logo" aria-label="Bee Framework, inicio">
          <span class="bee-brand-symbol" aria-hidden="true"></span>
          <span>Bee Framework</span>
        </a>
        <p>Un núcleo PHP ligero para construir aplicaciones web, APIs, tareas CLI y procesos programados.</p>
        <span class="bee-runtime-badge"><span aria-hidden="true"></span> Instancia local activa</span>
      </div>
      <nav class="bee-footer-links" aria-label="Enlaces del framework">
        <div>
          <h2>Framework</h2>
          <a href="bee">Inicio</a>
          <a href="documentacion">Documentación</a>
          <a href="examples/articles">Ejemplos</a>
        </div>
        <div>
          <h2>Herramientas</h2>
          <a href="creator">Creator</a>
          <a href="bee/info">Diagnóstico</a>
          <a href="bee/password">Contraseñas</a>
        </div>
        <div>
          <h2>Comunidad</h2>
          <a href="https://github.com/Moxtrip69/Bee-Framework" target="_blank" rel="noopener noreferrer">GitHub <span aria-hidden="true">↗</span></a>
          <a href="https://www.academy.joystick.com.mx" target="_blank" rel="noopener noreferrer">Joystick Academy <span aria-hidden="true">↗</span></a>
        </div>
      </nav>
    </div>
    <div class="bee-footer-bottom">
      <span>© <?= date('Y') ?> Bee Framework. Hecho para desarrolladores que construyen.</span>
      <span>Bee <?= htmlspecialchars(get_bee_version(), ENT_QUOTES, 'UTF-8') ?> · PHP <?= htmlspecialchars(PHP_VERSION, ENT_QUOTES, 'UTF-8') ?></span>
    </div>
  </div>
</footer>

<?php require_once INCLUDES . 'scripts.php'; ?>
</body>
</html>
