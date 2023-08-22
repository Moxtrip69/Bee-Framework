<!doctype html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title><?php echo $d->quote; ?></title>

  <style type="text/css">
    @page {
      margin: 0px;
    }

    body {
      margin: 6.8cm 0px;
    }

    * {
      font-family: <?php echo $d->tipografia; ?>, Arial, sans-serif;
    }

    a {
      text-decoration: none;
    }

    table,
    p {
      font-size: x-small;
    }

    .invoice,
    .information,
    .qr-code,
    .footer {
      padding: 80px;
    }

    .btn-theme {
      padding: 30px 50px;
      display: inline-block;
    }

    .bg-light {
      background-color: #ebebeb;
    }

    .btn-theme,
    .information,
    .footer,
    .bg-theme {
      background-color: <?php echo $d->bgColor; ?>;
    }

    .btn-theme,
    .information,
    .footer,
    .bg-theme,
    a {
      color: <?php echo $d->textColor; ?> !important;
    }

    .information {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
    }

    .information table {
      padding: 10px;
    }

    .qr-code {
      position: fixed;
      left: 0;
      right: 0;
      bottom: 2cm;
    }

    .footer {
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
    }

    .footer .page {
      display: block;
    }

    .footer .page .page-number:after {
      content: counter(page);
    }

    /* Estilos para la tabla de conceptos */
    .invoice table {
      width: 100%;
      border-collapse: collapse;
      border: 1px solid #ddd;
    }

    .invoice table tr {
      vertical-align: middle;
    }

    .invoice table th,
    .invoice table td {
      padding: 30px;
      border-bottom: 1px solid #ddd;
    }

    .invoice td img {
      width: 60px;
      height: 60px;
      border-radius: 10px;
    }

    .invoice table th {
      background-color: #f2f2f2;
    }
  </style>

</head>

<body>
  <!-- Cabecera -->
  <div class="information">
    <table width="100%">
      <tr>
        <td align="left" style="width: 50%;">
          <h3 style="margin: 0px;"><?php echo $d->client; ?></h3>
          <a href="<?php echo sprintf('mailto:%s', $d->email); ?>" style="margin-top: 0px; display: inline-block; margin-bottom: 30px;"><?php echo $d->email; ?></a>
          <p>
            <?php foreach (explode(',', $d->address) as $ae) : ?>
              <?php echo $ae; ?> <br>
            <?php endforeach; ?>
          </p>

          Folio
          <h1 style="margin: 0px;"><?php echo sprintf('<b>%s</b>', $d->quote); ?></h1>
          <p style="margin: 0px;"><?php echo format_date($d->date); ?></p>
        </td>
        <td align="right" style="width: 50%;">
          <img src="<?php echo get_logo(); ?>" alt="<?php echo get_sitename(); ?>" width="300" class="logo" />
          <h3><?php echo $d->companyName; ?></h3>
          <a href="<?php echo get_base_url(); ?>" style="display: inline-block; margin-bottom: 30px;"><?php echo $d->companyUrl; ?></a>

          <p>
            <?php foreach (explode(',', $d->companyAddress) as $ae) : ?>
              <?php echo $ae; ?> <br>
            <?php endforeach; ?>
          </p>
        </td>
      </tr>

    </table>
  </div>

  <!-- Código QR -->
  <div class="qr-code bg-light">
    <table width="100%">
      <tr>
        <td width="20%">
          <img src="https://img.freepik.com/vector-premium/codigo-qr_578229-236.jpg" alt="Código QR" style="width: 100%;">
        </td>
        <td style="padding-left: 50px;">
          <h3>Completa tu pago</h3>
          <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Assumenda maiores impedit vel asperiores iure voluptate temporibus vitae, dolor aperiam magnam?</p>
          <a class="btn-theme" href="<?php echo get_base_url(); ?>">Pagar ahora</a>
        </td>
      </tr>
    </table>
  </div>

  <!-- Footer -->
  <div class="footer">
    <table width="100%">
      <tr>
        <td align="left" style="width: 50%;">
          <?php echo get_sitename(); ?> <?php echo date('Y'); ?> &copy; Todos los derechos reservados.
        </td>
        <td align="right" style="width: 50%;">
          Nunca dejes de aprender.
          <span class="page">Página <span class="page-number"></span></span>
        </td>
      </tr>
    </table>
  </div>

  <!-- Contenido -->
  <div class="main">
    <div class="invoice">
      <h3><?php echo sprintf('Contenido #%s', $d->quote); ?></h3>
      <table width="100%">
        <thead>
          <tr>
            <th align="center" width="5%"></th>
            <th align="left">Concepto</th>
            <th align="center">P. unitario</th>
            <th align="center">Cantidad</th>
            <th align="right">Subtotal</th>
          </tr>
        </thead>

        <tbody>
          <?php $total = 0; ?>
          <?php foreach ($d->concepts as $c) : ?>
            <tr>
              <td align="center"><img src="<?php echo get_image('broken.png'); ?>" alt="<?php echo $c->nombre; ?>"></td>
              <td align="left"><?php echo $c->nombre; ?></td>
              <td align="center"><?php echo money($c->precio); ?></td>
              <td align="center"><?php echo $c->cantidad ?></td>
              <td align="right"><?php echo money($c->precio * $c->cantidad); ?></td>
            </tr>
            <?php $total += $c->precio * $c->cantidad; ?>
          <?php endforeach; ?>
        </tbody>

        <tfoot>
          <tr>
            <td colspan="3"></td>
            <td align="right">Subtotal</td>
            <td align="right"><?php echo money($total / 1.16); ?></td>
          </tr>
          <tr>
            <td colspan="3"></td>
            <td align="right">Impuestos (16% IVA)</td>
            <td align="right"><?php echo money($total - ($total / 1.16)); ?></td>
          </tr>
          <tr>
            <td colspan="3"></td>
            <td align="right">Total</td>
            <td align="right" style="font-weight: bold;"><?php echo money($total); ?></td>
          </tr>
        </tfoot>
      </table>

      <img src="<?php echo $d->chart; ?>" alt="Gráfica generada">
    </div>
  </div>
</body>

</html>