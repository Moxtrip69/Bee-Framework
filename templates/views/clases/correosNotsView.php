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
    <div class="offset-3 col-6 border rounded p-3">
      <h5><?php echo $d->title; ?></h5>
      Sistema para enviar notificaciones de diferentes tipos por correo electrónico.

      <div class="card mt-3">
        <div class="card-body">
          <p class="card-text">Presiona un botón para enviar una notificación por correo electrónico.</p>
          <a class="btn btn-success" href="<?php echo build_url('clases/enviar-notificacion', ['tipo' => 'new']); ?>">Nueva compra</a>
          <a class="btn btn-info" href="<?php echo build_url('clases/enviar-notificacion', ['tipo' => 'shipped']); ?>">Envío en camino</a>
          <a class="btn btn-primary" href="<?php echo build_url('clases/enviar-notificacion', ['tipo' => 'delivered']); ?>">Compra entregada</a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once INCLUDES . 'footer.php'; ?>