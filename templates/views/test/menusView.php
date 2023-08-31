<?php require_once INCLUDES . 'header.php'; ?>
<?php require_once INCLUDES . 'navbar.php'; ?>

<div class="container">
  <div class="row">
    <div class="col-6 offset-xl-3 py-5">
      <!-- contenido -->
      <div class="border rounded shadow p-3">
        <nav class="navbar">
          <div class="container-fluid">
            <?php echo $d->menu; ?>
          </div>
        </nav>

        <nav class="navbar navbar-expand-lg bg-body-tertiary">
          <div class="container-fluid">
            <a class="navbar-brand" href="#">Navbar</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
              <?php echo $d->menu; ?>
            </div>
          </div>
        </nav>
      </div>
    </div>
  </div>
</div>

<?php require_once INCLUDES . 'footer.php'; ?>