<?php require_once INCLUDES . 'header.php'; ?>
<?php require_once INCLUDES . 'navbar.php'; ?>

<!-- Plantilla versión 1.0.5 -->
<div class="container py-5 main-wrapper">
  <div class="row">
    <div class="col-12">
      <?php echo Flasher::flash(); ?>
    </div>
  </div>
  <div class="row">
    <div class="offset-3 col-6 border rounded p-3">
      <h5><?php echo $d->title; ?></h5>

      <div class="card mt-3">
        <div class="card-body">
          <form action="" id="generarPdfForm">
            <div class="mb-3">
              <label for="nombre" class="form-label">Nombre de la persona</label>
              <input type="text" class="form-control" id="nombre" name="nombre" value="John Doe">
            </div>
            <div class="mb-3">
              <label for="correo-electronico" class="form-label">Correo electrónico</label>
              <input type="email" class="form-control" id="correo-electronico" name="correo-electronico" value="jslocal@localhost.com">
            </div>
            <div class="mb-3">
              <label for="mensaje" class="form-label">Mensaje del PDF</label>
              <textarea class="form-control" name="mensaje" id="mensaje" cols="5" rows="10">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Doloribus, maxime.</textarea>
            </div>
            <button class="btn btn-sm btn-success" id="generarPdfFormBtn">Generar PDF</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once INCLUDES . 'footer.php'; ?>