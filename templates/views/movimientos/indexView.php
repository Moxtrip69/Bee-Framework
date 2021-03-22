<?php require_once INCLUDES.'inc_header.php'; ?>

<div class="container">
  <div class="py-5 text-center">
    <img class="d-block mx-auto mb-4" src="<?php echo IMAGES.'bee_logo.png' ?>" alt="<?php echo get_sitename() ?>" width="150">
    <h2>Administra tus finanzas</h2>
    <p class="lead">Lorem ipsum dolor sit amet consectetur, adipisicing elit. Nam, ullam.</p>
  </div>

  <!-- options -->
  <div class="row">
    <div class="col-12">
      <div class="card mb-3">
        <div class="card-header"><h5>Opciones del sistema</h5></div>
        <div class="card-body">
          <form class="bee_save_options">
            <div class="mb-3 row">
              <div class="col-4">
                <label for="use_taxes">Calcular Impuestos</label>
                <select name="use_taxes" id="use_taxes" class="form-control">
                  <?php foreach (['Si','No'] as $opt): ?>
                  <option value="<?php echo $opt ?>" <?php echo get_option('use_taxes') === $opt ? 'selected' : ''; ?>><?php echo $opt; ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-4">
                <label for="taxes">Impuestos</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">%</span>
                  </div>
                  <input type="text" class="form-control" name="taxes" value="<?php echo get_option('taxes'); ?>" id="taxes">
                </div>
              </div>
              <div class="col-4">
                <label for="coin">Moneda</label>
                <select name="coin" id="coin" class="form-control">
                  <?php foreach (get_coins() as $coin): ?>
                    <option value="<?php echo $coin; ?>" <?php echo get_option('coin') === $coin ? 'selected' : ''; ?>><?php echo $coin; ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <button class="btn btn-success" type="submit">Guardar</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <!-- formulario -->
    <div class="col-xl-8">
      <div class="card">
        <div class="card-header">
          <h4>Agrega un nuevo movimiento</h4>
        </div>
        <div class="card-body">
          <form class="bee_add_movement" novalidate>
            <div class="mb-3 row">
              <div class="col-xl-6">
                <label for="country">Tipo de movimiento</label>
                <select class="custom-select d-block w-100" id="type" name="type" required>
                  <option value="none">Selecciona...</option>
                  <option value="expense">Gasto</option>
                  <option value="income">Ingreso</option>
                </select>
                <div class="invalid-feedback">
                  Selecciona un tipo de movimiento válido
                </div>
              </div>
              <div class="col-xl-6">
                <label for="descripcion">Descripción</label>
                <input type="text" class="form-control" id="description" name="description" placeholder="Descripción" value="" required>
                <div class="invalid-feedback">
                  Ingresa una descripción
                </div>
              </div>
            </div>
            <div class="mb-3">
              <label for="amount">Monto</label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">$</span>
                </div>
                <input type="text" class="form-control" id="amount" name="amount" placeholder="0.00" required>
                <div class="invalid-feedback" style="width: 100%;">
                  Ingresa un monto válido
                </div>
              </div>
            </div>

            <button class="btn btn-primary btn-lg btn-block" type="submit">Agregar</button>
          </form>
          <div class="bee_wrapper_update_form"><!-- ajax fill --></div>
        </div>
      </div>
    </div>

    <!-- lista de movimientos -->
    <div class="col-xl-4">
      <div class="bee_wrapper_movements">
        <!-- ajax fill -->
      </div>
    </div>
  </div>
</div>

<?php require_once INCLUDES.'inc_footer_v2.php'; ?>

