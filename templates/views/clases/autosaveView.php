<?php require_once INCLUDES . 'header.php'; ?>
<?php require_once INCLUDES . 'navbar.php'; ?>

<div class="container py-5">
  <div class="row">
    <div class="col-12 col-md-6 col-lg-12">
      <div class="card">
        <div class="card-header d-flex flex-row justify-content-between align-items-center">
          <h5 class="m-0"><?php echo $d->title; ?></h5>
          <span id="statusMessage" class="d-none"></span>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-12 col-lg-6">
              <?php echo $d->form; ?>
            </div>
            <div class="col-12 col-lg-6">
              <pre id="responseWrapper" class="code-block d-none"></pre>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once INCLUDES . 'footer.php'; ?>