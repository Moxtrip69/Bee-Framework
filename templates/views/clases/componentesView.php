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
      Lorem ipsum dolor sit amet, consectetur adipisicing elit. Explicabo, consequatur temporibus animi deleniti accusantium vitae commodi tempore eius nam neque?

      <?php echo $d->ac; ?>

      <?php echo $d->card; ?>

      <?php echo $d->slider; ?>

      <?php echo $d->productos; ?>
    </div>
  </div>
</div>

<?php require_once INCLUDES . 'footer.php'; ?>