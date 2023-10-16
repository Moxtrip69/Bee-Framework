<?php require_once INCLUDES . 'header.php'; ?>
<?php require_once INCLUDES . 'navbar.php'; ?>

<div class="container">
  <div class="row">
    <div class="col-6 offset-xl-3 py-5">
      <!-- contenido -->
      <div class="border rounded shadow p-3">
        <?php echo $d->slider; ?>

        <br>

        <?php echo $d->card; ?>

        <br>

        <?php echo $d->accordion; ?>
      </div>
    </div>
  </div>
</div>

<?php require_once INCLUDES . 'footer.php'; ?>