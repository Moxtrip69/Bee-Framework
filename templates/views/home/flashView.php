<?php require_once INCLUDES.'inc_header.php'; ?>
<?php require_once INCLUDES.'inc_bee_navbar.php'; ?>

<div class="container py-5">
  <div class="row">
    <div class="col-6 text-center offset-xl-3">
      <a href="<?php echo URL; ?>"><img src="<?php echo get_bee_logo() ?>" alt="Bee framework" class="img-fluid" style="width: 150px;"></a>
      <h2 class="mt-5 mb-3"><span class="text-warning">Bee</span> framework</h2>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <?php echo Flasher::flash(); ?>
    </div>
    <div class="col-12">
      <div class="card">
        <div class="card-header">Información en $_SESSION</div>
        <div class="card-body">
          <code>
            <?php debug($_SESSION); ?>
          </code>
        </div>
        <div class="card-footer clearfix">
          <a href="home" class="btn btn-success float-start">Inicio</a>
          <a href="logout" class="btn btn-danger float-end confirmar">Cerrar sesión</a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once INCLUDES.'inc_bee_footer.php'; ?>