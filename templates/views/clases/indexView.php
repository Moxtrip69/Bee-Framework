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

      <ul class="list-group">
        <a class="list-group-item list-group-item-action" href="clases/pdf">Generación de PDF</a>
        <a class="list-group-item list-group-item-action" href="clases/memes">Carga remota de imágenes</a>
        <a class="list-group-item list-group-item-action" href="clases/qr">Generación de QRs</a>
        <a class="list-group-item list-group-item-action" href="clases/autoguardado">Autoguardado</a>
        <a class="list-group-item list-group-item-action" href="clases/notificaciones">Notificaciones con SSE</a>
        <a class="list-group-item list-group-item-action" href="clases/reportes">CRUD de reportes</a>
        <a class="list-group-item list-group-item-action" href="clases/fullcalendar">Fullcalendar.js</a>
        <a class="list-group-item list-group-item-action" href="clases/componentes">Componentes</a>
        <a class="list-group-item list-group-item-action" href="clases/correos-notificaciones">Correos electrónicos</a>
      </ul>
    </div>
  </div>
</div>

<?php require_once INCLUDES . 'footer.php'; ?>