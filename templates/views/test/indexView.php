<?php require_once INCLUDES.'inc_header.php'; ?>

<div class="container">
  <div class="row">
    <div class="col-6 text-center offset-xl-3">
      <a href="<?php echo URL; ?>"><img src="<?php echo IMAGES.'bee_logo.png' ?>" alt="Bee framework" class="img-fluid" style="width: 200px;"></a>
      <h2 class="mt-5 mb-3"><span class="text-warning">Bee</span> framework</h2>
      <!-- contenido -->
      <h1><?php echo $d->msg; ?></h1>
      <!-- ends -->
    </div>
  </div>
</div>

<?php require_once INCLUDES.'inc_footer.php'; ?>