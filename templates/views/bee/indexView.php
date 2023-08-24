<?php require_once INCLUDES . 'header.php'; ?>
<?php require_once INCLUDES . 'bee_navbar.php'; ?>

<div class="container py-5 main-wrapper">
  <div class="row">
    <div class="col-6 text-center offset-xl-3">
      <a href="<?php echo get_base_url(); ?>"><img src="<?php echo get_logo(); ?>" alt="<?php echo get_sitename(); ?>" class="img-fluid" style="width: 200px;"></a>
      <h2 class="mt-5 mb-3"><span class="text-warning">Bee</span> framework</h2>
      <!-- contenido -->
      <h1><?php echo $d->msg; ?></h1>
      <!-- ends -->
    </div>
  </div>
</div>

<?php require_once INCLUDES . 'footer.php'; ?>