<?php require_once INCLUDES.'header.php'; ?>
<?php require_once INCLUDES.'bee_navbar.php'; ?>

<div class="container py-5">
  <div class="text-center">
    <a href="<?php echo get_base_url(); ?>"><img src="<?php echo get_logo() ?>" alt="<?php echo get_sitename(); ?>" class="img-fluid" style="width: 150px;"></a>
    <h2><?php echo $d->title; ?></h2>
  </div>

  <div class="row">
    <div class="col-12">
      <?php echo Flasher::flash(); ?>
    </div>

    <!-- testApp Independiente -->
    <div id="testApp">
    </div>
  </div>
</div>

<?php require_once INCLUDES.'footer.php'; ?>