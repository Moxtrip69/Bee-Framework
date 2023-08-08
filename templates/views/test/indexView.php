<?php require_once INCLUDES.'inc_bee_header.php'; ?>

<div class="container">
  <div class="row">
    <div class="col-6 text-center offset-xl-3 py-5">
      <a href="<?php echo get_base_url(); ?>"><img src="<?php echo get_bee_logo(); ?>" alt="<?php echo get_bee_name(); ?>" class="img-fluid" style="width: 200px;"></a>
      <h2 class="mt-5 mb-3"><span class="text-warning">Bee</span> framework</h2>
      <!-- contenido -->
      <?php foreach ($d->tests as $t): ?>
        <a href="<?php echo $t->url; ?>" class="nav-link d-block"><?php echo $t->title; ?></a>
      <?php endforeach; ?>
      <!-- ends -->
    </div>
  </div>
</div>

<?php require_once INCLUDES.'inc_bee_footer.php'; ?>