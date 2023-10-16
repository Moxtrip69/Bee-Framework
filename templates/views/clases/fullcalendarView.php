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
    <div class="col-12 text-center py-5">
      <div class="card">
        <div class="card-header">
          <?php echo $d->title; ?>
        </div>
        <div class="card-body">
          <div id="calendario"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Agregar -->
<div class="modal fade" id="agregarEventoModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="agregarEventoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5">Nueva cita</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cancelar"></button>
      </div>
      <div class="modal-body">
        <form id="agregarEventoForm">
          <div class="mb-3">
            <label for="titulo" class="form-label">Nombre del paciente <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="titulo" name="titulo" required>
          </div>

          <div class="mb-3">
            <label for="fecha" class="form-label">Fecha de la cita <span class="text-danger">*</span></label>
            <input type="date" class="form-control" id="fecha" required>
          </div>

          <div class="mb-3">
            <label for="color" class="form-label">Color</label>
            <select name="color" id="color" class="form-select">
              <option value="blue">Azul (por defecto)</option>
              <option value="green">Verde</option>
              <option value="red">Rojo</option>
              <option value="purple">Morado</option>
            </select>
          </div>

        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="agregarEventoFormSubmit">Agendar cita</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Ver -->
<div class="modal fade" id="verEventoModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="verEventoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5">Evento</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cancelar"></button>
      </div>
      <div class="modal-body" id="verEventoWrapper">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<?php require_once INCLUDES . 'footer.php'; ?>