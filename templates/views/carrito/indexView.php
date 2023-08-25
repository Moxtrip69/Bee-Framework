<?php require_once INCLUDES . 'header.php'; ?>

<div class="container py-5 main-wrapper">
  <div class="row mb-5">
    <div class="col-12 col-md-6 text-center offset-md-3">
      <a href="<?php echo get_base_url(); ?>">
        <img src="<?php echo get_logo(); ?>" alt="<?php echo get_sitename(); ?>" class="img-fluid" style="width: 200px;">
      </a>
    </div>
  </div>
  <div class="row g-5">
    <!-- Resumen del carrito de compras -->
    <div class="col-md-5 col-lg-4 order-md-last ">
      <div class="sticky-top" style="top: 15px;">
        <h4 class="d-flex justify-content-between align-items-center mb-3">
          <span class="text-primary">Tu carrito</span>
          <span class="badge bg-primary rounded-pill"><?php echo $d->cart->totalItems; ?></span>
        </h4>
        <?php if ($d->cart->totalItems !== 0): ?>
          <ul class="list-group">
            <!-- Lista de items en el carrito -->
            <?php foreach ($d->cart->items as $item): ?>
              <li class="list-group-item d-flex justify-content-between lh-sm">
                <div class="d-flex justify-content-start align-items-center">
                  <a href="<?php echo build_url(sprintf('carrito/remover/%s', $item->id), [], true, false); ?>" class="text-danger text-decoration-none me-2">
                    <i class="fas fa-times"></i>
                  </a>
                  <img src="<?php echo get_uploaded_image($item->image); ?>" alt="<?php echo $item->name; ?>" class="img-fluid border rounded me-2" style="max-width: 50px; height: 50px; object-fit: cover;">                  
                  <div class="me-2">
                    <h6 class="my-0">
                      <?php echo $item->name; ?> 
                    </h6>
                    <small class="text-muted"><?php echo $item->description; ?></small>
                  </div>
                </div>
                <span class="text-body-secondary">
                  <?php echo money($item->price); ?>
                  <span class="text-muted d-block"><?php echo sprintf('x %s', $item->quantity); ?></span>
                </span>
              </li>
            <?php endforeach; ?>
  
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <span>Subtotal</span>
              <span class="text-body-secondary"><?php echo money($d->cart->subtotal); ?></span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <span>Impuestos</span>
              <span class="text-body-secondary"><?php echo sprintf('+ %s', money($d->cart->taxes)); ?></span>
            </li>
  
            <!-- Cupón aplicado -->
            <?php if (!empty($d->cart->coupon)): ?>
              <li class="list-group-item d-flex justify-content-between bg-body-tertiary">
                <div class="text-success">
                  <h6 class="my-0"><?php echo $d->cart->coupon->description; ?></h6>
                  <small><?php echo strtoupper($d->cart->coupon->code); ?></small>
                </div>
                <span class="text-success"><?php echo sprintf('-%s', money($d->cart->discounts)); ?></span>
              </li>
            <?php endif; ?>
  
            <!-- Envío de ser requerido -->
            <?php if (!empty($d->cart->shipping)): ?>
              <li class="list-group-item d-flex justify-content-between bg-body-tertiary">
                <div class="">
                  <h6 class="my-0"><?php echo $d->cart->shipping->courier; ?></h6>
                  <small class="text-body-secondary">Entrega estimada: <?php echo format_date($d->cart->shipping->date); ?></small>
                </div>
                <span class="text-body-secondary"><?php echo money($d->cart->shipping->price); ?></span>
              </li>
            <?php endif; ?>
  
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <span>Total a pagar</span>
              <h3 class="fw-bold m-0"><?php echo $d->cart->currency; ?> <?php echo money($d->cart->total); ?></h3>
            </li>
          </ul>
  
          <a href="carrito/vaciar" class="text-decoration-none text-danger d-inline-block mb-3 ms-2">
            <small>Vaciar carrito</small>
          </a>
  
        <?php else: ?>
          <div class="py-5 text-center">
            <i class="fas fa-cart-shopping fa-fw fs-1"></i>
            <p class="text-muted mt-3">Tu carrito está vacio.</p>
          </div>
        <?php endif; ?>
  
        <form class="card p-2" method="get" action="carrito">
          <div class="input-group">
            <input type="text" class="form-control" name="couponCode" placeholder="Ingresa un cupón de descuento" required>
            <button type="submit" class="btn btn-secondary">Canjear</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Formulario de envío y de pago -->
    <div class="col-md-7 col-lg-8">
      <h4 class="mb-3">Dirección de facturación</h4>

      <?php echo Flasher::flash(); ?>

      <form class="needs-validation" novalidate="">
        <div class="row g-3">
          <div class="col-sm-6">
            <label for="firstName" class="form-label">Nombre(s)</label>
            <input type="text" class="form-control" id="firstName" placeholder="" value="" required="">
            <div class="invalid-feedback">
              Ingresa un nombre válido.
            </div>
          </div>

          <div class="col-sm-6">
            <label for="lastName" class="form-label">Apellido(s)</label>
            <input type="text" class="form-control" id="lastName" placeholder="" value="" required="">
            <div class="invalid-feedback">
              Ingresa un apellido válido.
            </div>
          </div>

          <div class="col-12">
            <label for="username" class="form-label">Usuario</label>
            <div class="input-group has-validation">
              <span class="input-group-text">@</span>
              <input type="text" class="form-control" id="username" placeholder="Username" required="">
            <div class="invalid-feedback">
                Tu usuario es requerido.
              </div>
            </div>
          </div>

          <div class="col-12">
            <label for="email" class="form-label">Correo electrónico <span class="text-body-secondary">(Opcional)</span></label>
            <input type="email" class="form-control" id="email" placeholder="you@example.com" required>
            <div class="invalid-feedback">
              Por favor ingresa un correo válido para recibir actualizaciones de tu envío.
            </div>
          </div>

          <div class="col-12">
            <label for="address" class="form-label">Dirección</label>
            <input type="text" class="form-control" id="address" placeholder="Calle en el mundo #123" required="">
            <div class="invalid-feedback">
              Por favor ingresa una dirección válida.
            </div>
          </div>

          <div class="col-12">
            <label for="address2" class="form-label">Dirección 2 <span class="text-body-secondary">(Opcional)</span></label>
            <input type="text" class="form-control" id="address2" placeholder="Departamento o interior">
          </div>

          <div class="col-md-5">
            <label for="country" class="form-label">País</label>
            <select class="form-select" id="country" required="">
              <option value="">Selecciona...</option>
              <option>México</option>
            </select>
            <div class="invalid-feedback">
              Selecciona un país válido.
            </div>
          </div>

          <div class="col-md-4">
            <label for="state" class="form-label">State</label>
            <select class="form-select" id="state" required="">
              <option value="">Selecciona...</option>
              <option>CDMX</option>
              <option value="">Estado de México</option>
            </select>
            <div class="invalid-feedback">
              Selecciona un estado válido.
            </div>
          </div>

          <div class="col-md-3">
            <label for="zip" class="form-label">Código Postal</label>
            <input type="text" class="form-control" id="zip" placeholder="" required="">
            <div class="invalid-feedback">
              Código postal es requerido.
            </div>
          </div>
        </div>

        <hr class="my-4">

        <div class="form-check">
          <input type="checkbox" class="form-check-input" id="same-address">
          <label class="form-check-label" for="same-address">Dirección de envío igual a la de facturación</label>
        </div>

        <div class="form-check">
          <input type="checkbox" class="form-check-input" id="save-info">
          <label class="form-check-label" for="save-info">Guardar mi información para después</label>
        </div>

        <hr class="my-4">

        <h4 class="mb-3">Método de pago</h4>

        <div class="my-3">
          <div class="form-check">
            <input id="credit" name="paymentMethod" type="radio" class="form-check-input" checked="" required="">
            <label class="form-check-label" for="credit">Tarjeta de crédito</label>
          </div>
          <div class="form-check">
            <input id="debit" name="paymentMethod" type="radio" class="form-check-input" required="">
            <label class="form-check-label" for="debit">Tarjeta de débito</label>
          </div>
          <div class="form-check">
            <input id="paypal" name="paymentMethod" type="radio" class="form-check-input" required="">
            <label class="form-check-label" for="paypal">PayPal</label>
          </div>
        </div>

        <div class="row gy-3">
          <div class="col-md-6">
            <label for="cc-name" class="form-label">Titular de la tarjeta</label>
            <input type="text" class="form-control" id="cc-name" placeholder="" required="">
            <small class="text-body-secondary">Nombre mostrado en la tarjeta</small>
            <div class="invalid-feedback">
              Nombre del titular requerido.
            </div>
          </div>

          <div class="col-md-6">
            <label for="cc-number" class="form-label">Número en la tarjeta</label>
            <input type="text" class="form-control" id="cc-number" placeholder="" required="">
            <div class="invalid-feedback">
              Número de tarjeta requerido.
            </div>
          </div>

          <div class="col-md-3">
            <label for="cc-expiration" class="form-label">Fecha de expiración</label>
            <input type="text" class="form-control" id="cc-expiration" placeholder="" required="">
            <div class="invalid-feedback">
              Fecha de expiración requerida.
            </div>
          </div>

          <div class="col-md-3">
            <label for="cc-cvv" class="form-label">CVV</label>
            <input type="text" class="form-control" id="cc-cvv" placeholder="" required="">
            <div class="invalid-feedback">
              Código de seguridad requerido.
            </div>
          </div>
        </div>

        <hr class="my-4">

        <button class="w-100 btn btn-primary btn-lg" type="submit">Pagar ahora</button>
      </form>
    </div>
  </div>
</div>

<?php require_once INCLUDES . 'footer.php'; ?>