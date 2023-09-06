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
    <div class="col-12 col-md-6 offset-md-3 py-5">
      <h2 class="mt-5 mb-3"><span class="text-warning"><?php echo $d->title; ?></h2>

      <?php echo $d->form; ?>
      <?php echo $d->script; ?>
    </div>
  </div>
</div>

<?php require_once INCLUDES . 'footer.php'; ?>