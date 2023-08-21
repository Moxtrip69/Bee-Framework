<?php require_once INCLUDES . 'inc_header.php'; ?>

<!-- Plantilla versión 1.0.0 -->
<div class="container">
  <div class="row">
    <div class="col-12 col-md-6 text-center offset-md-3 py-5">
      <a href="<?php echo get_base_url(); ?>">
        <img src="<?php echo get_logo(); ?>" alt="<?php echo get_sitename(); ?>" class="img-fluid" style="width: 200px;">
      </a>
      <p class="mt-3 text-muted"><?php echo $d->topic; ?></p>

      <div class="card mt-5 shadow">
        <div class="card-header bg-white border-bottom-0">
          <h2>Generar cotización - <?php echo sprintf('Clase #%s', $d->number); ?></h2>
        </div>
        <div class="card-body text-start">
          <?php echo Flasher::flash(); ?>

          <form action="clases/post-generar-reporte" method="post">
            <?php echo insert_inputs(); ?>

            <h3>Configuraciones del PDF</h3>
            <div class="mb-3">
              <label for="bgColor" class="form-label">Color picker</label>
              <input type="color" class="form-control form-control-color" id="bgColor" name="bgColor" value="#009d43" title="Selecciona un color de fondo">
            </div>
            <div class="mb-3">
              <label for="orientacion" class="form-label">Orientación del documento</label>
              <select name="orientacion" id="orientacion" class="form-select">
                <option value="portrait">Vertical</option>
                <option value="landscape">Horizontal</option>
              </select>
            </div>

            <div class="mb-3">
              <label for="tamano" class="form-label">Tamaño del documento</label>
              <select name="tamano" id="tamano" class="form-select">
                <option value="A3">A3 (Tabloide)</option>
                <option value="A4" selected>A4 (Similar a Carta)</option>
                <option value="A5">A5 (1/2 A4)</option>
                <option value="letter">Carta</option>
              </select>
            </div>

            <h3>Información del cliente</h3>

            <div class="mb-3">
              <label for="cliente" class="form-label">Nombre del cliente</label>
              <input type="text" class="form-control" id="cliente" name="cliente" placeholder="Walter White" value="Walter White" required>
            </div>

            <div class="mb-3">
              <label for="email" class="form-label">Correo electrónico</label>
              <input type="email" class="form-control" id="email" name="email" placeholder="walter@white.com" value="walter@white.com" required>
            </div>

            <div class="mb-3">
              <label for="direccion" class="form-label">Dirección</label>
              <input type="text" class="form-control" id="direccion" name="direccion" placeholder="Calle Breaking Bad #123, CDMX, México" value="Calle Breaking Bad #123, CDMX, México" required>
            </div>

            <div class="mb-3 d-flex flex-column">
              <?php foreach ($d->concepts as $c): ?>
                <div class="d-flex flex-row justify-content-between align-items-center">
                  <p class="fw-bold col-6"><?php echo $c->nombre; ?></p>
                  <p><?php echo sprintf('%s x %s', $c->cantidad, money($c->precio)); ?></p>
                  <p class="text-success col-2 text-end"><?php echo money($c->precio * $c->cantidad); ?></p>
                </div>
              <?php endforeach; ?>
            </div>

            <button class="btn btn-success" type="submit">Generar PDF</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once INCLUDES . 'inc_footer.php'; ?>