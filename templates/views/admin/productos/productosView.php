<?php require_once INCLUDES . 'admin/dashboardTop.php'; ?>

<div class="row">
  <!-- Formulario para agregar producto -->
  <div class="col-12 col-md-6 col-lg-6 col-xl-3">
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Agregar un producto</h6>
      </div>
      <div class="card-body">
        <?php echo $d->form; ?>
      </div>
    </div>
  </div>

  <!-- Tabla de resultados -->
  <div class="col-12 col-md-6 col-lg-6 col-xl-9">
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Todos los productos</h6>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive" style="min-height: 300px;">
          <table class="table table-hover table-striped">
            <thead>
              <tr>
                <th>Nombre</th>
                <th class="text-center">Precio</th>
                <th class="text-center">Precio de comparación</th>
                <th class="text-center">Unidades disponibles</th>
                <th class="text-end">Creado</th>
                <th class="text-end">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($d->productos->rows)): ?>
                <?php foreach ($d->productos->rows as $p) : ?>
                  <tr>
                    <td width="30%"><?php echo add_ellipsis($p->nombre, 50); ?></td>
                    <td class="text-center"><?php echo _e(money($p->precio)); ?></td>
                    <td class="text-center"><?php echo _e(money($p->precio_comparacion)); ?></td>
                    <td class="text-center"><?php echo _e($p->stock); ?></td>
                    <td class="text-end"><?php echo format_date($p->creado); ?></td>
                    <td class="text-end">
                      <div class="dropdown">
                        <a class="btn btn-sm btn-secondary" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                          <i class="fas fa-chevron-down"></i>
                        </a>
                        <ul class="dropdown-menu">
                          <li><a class="dropdown-item confirmar" href="<?php echo build_url(sprintf('admin/borrar-producto/%s', $p->id)) ?>">Borrar</a></li>
                        </ul>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="5" class="text-center">No hay productos registrados en la base de datos.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
      <div class="card-body">
        <?php echo $d->productos->pagination; ?>
      </div>
    </div>
  </div>
</div>

<?php require_once INCLUDES . 'admin/dashboardBottom.php'; ?>