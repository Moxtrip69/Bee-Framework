<?php require_once INCLUDES . 'header.php'; ?>
<?php require_once INCLUDES . 'navbar.php'; ?>

<div class="container py-5">
  <div class="row">
    <div class="col-12 col-md-6 col-lg-12">
      <div class="card">
        <div class="card-header d-flex flex-row justify-content-between align-items-center">
          <h5 class="m-0">Listado de memes</h5>
          <button class="btn btn-success" id="loadMemes" data-page="1">Cargar memes</button>
        </div>
        <div class="card-body bg-light">
          <div id="memesWrapper"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once INCLUDES . 'footer.php'; ?>