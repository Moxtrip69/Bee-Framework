<?php require_once INCLUDES . 'header.php'; ?>
<?php require_once INCLUDES . 'navbar.php'; ?>

<!-- Plantilla versión 1.0.0 -->
<div class="container py-5 main-wrapper">
  <div class="row">
    <div class="col-12 col-md-6 text-center offset-md-3 py-5">
      <a href="<?php echo get_base_url(); ?>">
        <img src="<?php echo get_logo(); ?>" alt="<?php echo get_sitename(); ?>" class="img-fluid" style="width: 200px;">
      </a>

      <h2 class="mt-5 mb-3"><span class="text-warning">Bee</span> framework</h2>

      <!-- contenido -->
      <p class="text-muted">Lorem ipsum dolor sit amet consectetur adipisicing elit. Quam, ducimus!</p>
      <!-- ends -->

    </div>
  </div>
</div>

<?php require_once INCLUDES . 'footer.php'; ?>