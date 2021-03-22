<h4 class="d-flex justify-content-between align-items-center mb-3">
  <span class="text-muted">Movimientos</span>
  <span class="badge badge-secondary badge-pill"><?php echo $d->cal->total_movements; ?></span>
</h4>
<ul class="list-group mb-3">
  <?php if ($d->movements): ?>
  <?php foreach ($d->movements as $mov): ?>
  <li class="list-group-item d-flex justify-content-between lh-condensed
  <?php echo $mov->type === 'income' ? '' : 'bg-light'; ?>
  bee_movement" data-id="<?php echo $mov->id; ?>">
    <div class="<?php echo $mov->type === 'income' ? 'text-success' : 'text-danger'; ?>">
      <h6 class="my-0"><?php echo $mov->type === 'income' ? 'Ingreso' : 'Gasto'; ?></h6>
      <small class="text-muted"><?php echo $mov->description; ?></small>
    </div>
    <button class="btn btn-sm btn-danger float-end bee_delete_movement" data-id="<?php echo $mov->id; ?>"><i class="fas fa-trash"></i></button>
    <span class="<?php echo $mov->type === 'income' ? 'text-success' : 'text-danger'; ?>">
    <?php echo $mov->type === 'income' ? '' : '-'; ?>
      <?php echo money($mov->amount); ?>
    </span>
  </li>
  <?php endforeach; ?>
  <?php else: ?>
  No hay movimientos en el mes actual.
  <?php endif; ?>
</ul>

<ul class="list-group mb-3">
  <li class="list-group-item d-flex justify-content-between">
    <span>Subtotal (<?php echo get_option('coin') ?>)</span>
    <strong><?php echo money($d->cal->subtotal); ?></strong>
  </li>

  <?php if (get_option('use_taxes') === 'Si'): ?>
  <li class="list-group-item d-flex justify-content-between">
    <span>Impuestos (<?php echo get_option('taxes').'%' ?>)</span>
    <strong><?php echo money($d->cal->taxes); ?></strong>
  </li>
  <?php endif; ?>

  <li class="list-group-item d-flex justify-content-between">
    <span>Total (<?php echo get_option('coin') ?>)</span>
    <strong><?php echo money($d->cal->total); ?></strong>
  </li>
</ul>