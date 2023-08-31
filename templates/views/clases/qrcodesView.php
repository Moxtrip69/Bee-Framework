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
      <div class="card">
        <div class="card-header"><?php echo $d->title; ?></div>
        <div class="card-body">
          <?php echo $d->form; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once INCLUDES . 'footer.php'; ?>