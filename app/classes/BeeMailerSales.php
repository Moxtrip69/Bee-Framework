<?php 

/**
 * Esta clase es sólo educativa, no aporta nada al Framework, puede ser borrada
 */
class BeeMailerSales extends BeeMailer
{
  private $companyEmail = 'jslocal@localhost.com';
  private $email        = null;
  private $sale         = null;

  function __construct()
  {
    $this->email = new parent();
    $this->email->enableSmtp();
    $this->email->useTemplate(true);
    $this->email->setAuthentication('bee@joystick.com.mx', 'u%nq(WMy+7)@', 'mail.joystick.com.mx', 587, 'tls');
  }

  function setCompanyEmail(string $email)
  {
    $this->companyEmail = $email;
  }

  function setSale(array $sale)
  {
    $this->sale = $sale;
  }

  private function buildTable()
  {
    // Crear la tabla de la compra -- sólo como ejemplo
    $htmlTable = '<table>';
    $htmlTable .= 
    '<thead>
      <tr>
        <th align="left">Producto</th>
        <th align="left">Cantidad</th>
        <th align="left">Subtotal</th>
      </tr>
    </thead>';
    $htmlTable .= '<tbody>';

    // Insertando cada producto en la tabla
    foreach ($this->sale['productos'] as $producto) {
      $row = 
      '<tr>
        <td>%s</td>
        <td>%s</td>
        <td>%s</td>
      </tr>';
      $htmlTable .= sprintf(
        $row, 
        $producto['nombre'], 
        $producto['cantidad'], 
        money($producto['precio'] * $producto['cantidad'])
      );
    }
    
    $htmlTable .= '</tbody>';
    $htmlTable .= '</table>';

    return $htmlTable;
  }

  function newSaleToCustomer()
  {
    if ($this->sale === null) {
      throw new Exception('No hay información sobre la venta.');
    }

    // Al comprador
    $this->email->sendTo($this->sale['email']);
    $this->email->setSubject(sprintf('¡Gracias por tu compra! #%s', $this->sale['numero']));
    $this->email->setAlt(sprintf('Recibimos tu compra #%s', $this->sale['numero']));

    // Crear el cuerpo
    $body = '<h3>Gracias por tu compra %s</h3>';
    $body .= '<p>Compraste %s producto(s), los recibirás en la dirección <b>%s</b> durante las próximas 24 horas.</p>';
    $body .= '<p>Agradecemos tu confianza.</p>';
    $body = sprintf(
      $body, 
      $this->sale['cliente'], 
      count($this->sale['productos']), 
      $this->sale['direccion']
    );
    $body .= $this->buildTable();

    $this->email->setBody($body);
    $this->email->send();
  }

  function newSaleToCompany()
  {
    if ($this->sale === null) {
      throw new Exception('No hay información sobre la venta.');
    }

    // Al vendedor
    $this->email->sendTo($this->companyEmail);
    $this->email->setSubject(sprintf('¡Vendimos! #%s', $this->sale['numero']));
    $this->email->setAlt(sprintf('Nueva venta realizada #%s, ¡felicidades!', $this->sale['numero']));
    $this->email->setBody(sprintf(
      '<p>Te compró <b>%s</b>, recibiste <b>%s</b> en tu cuenta. Envía lo antes posible.</p><br>%s', 
      $this->sale['cliente'], 
      money($this->sale['total']), 
      $this->buildTable())
    );
    $this->email->send();
  }

  function shippedToCustomer()
  {
    
  }
}
