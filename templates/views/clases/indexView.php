<?php require_once INCLUDES . 'header.php'; ?>
<?php require_once INCLUDES . 'navbar.php'; ?>

<!-- Plantilla versión 1.0.5 -->
<div class="container py-5 main-wrapper">
  <div class="row">
    <div class="col-12">
      <?php echo Flasher::flash(); ?>
    </div>
  </div>
  <div class="row">
    <div class="col-12 col-md-6 text-center offset-md-3 py-5">
      <h2 class="mt-5 mb-3"><span class="text-warning"><?php echo $d->title; ?></h2>

      <!-- Add a placeholder for the Twitch embed -->
      <div id="twitch-embed"></div>

      <!-- Load the Twitch embed JavaScript file -->
      <script src="https://embed.twitch.tv/embed/v1.js"></script>

      <!-- Create a Twitch.Embed object that will render within the "twitch-embed" element -->
      <script type="text/javascript">
        new Twitch.Embed("twitch-embed", {
          width: '100%',
          height: 480,
          channel: "joystickmx",
          theme: 'dark',
          autoplay: true,
          muted: true,
          // Only needed if this page is going to be embedded on other websites
          parent: ["embed.example.com", "othersite.example.com"]
        });
      </script>

      <ul class="list-group">
        <a class="list-group-item list-group-item-action" href="clases/pdf">Generación de PDF</a>
        <a class="list-group-item list-group-item-action" href="clases/memes">Carga remota de imágenes</a>
        <a class="list-group-item list-group-item-action" href="clases/qr">Generación de QRs</a>
        <a class="list-group-item list-group-item-action" href="clases/autoguardado">Autoguardado</a>
        <a class="list-group-item list-group-item-action" href="clases/notificaciones">Notificaciones con SSE</a>
        <a class="list-group-item list-group-item-action" href="clases/reportes">CRUD de reportes</a>
      </ul>
    </div>
  </div>
</div>

<?php require_once INCLUDES . 'footer.php'; ?>