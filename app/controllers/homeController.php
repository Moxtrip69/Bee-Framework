<?php 

class homeController extends Controller {
  function __construct()
  {
  }

  function index()
  {
    $data =
    [
      'title' => 'Home'
    ];

    View::render('bee', $data);
  }

  function pdf()
  {
    try {
      $content = '<!DOCTYPE html>
      <html>
      <head>
      <style>
      code {
        font-family: Consolas,"courier new";
        color: crimson;
        background-color: #f1f1f1;
        padding: 2px;
        font-size: 80%%;
        border-radius: 5px;
      }
      </style>
      </head>
      <body>
  
      <img src="%s" alt="%s" style="width: 100px;"><br>
  
      <h1>Bienvenido de nuevo a %s</h1>
      <p>Versión <b>%s</b></p>
      
      <code>
      // Método 1
      $content = "Contenido del documento PDF, puedes usar cualquier tipo de HTML e incluso la mayoría de estilos CSS3";
      $pdf     = new BeePdf($content); // Se muestra directo en navegador, para descargar pasar en parámetro 2 true y para guardar en parámetro 3 true
  
      // Método 2
      $pdf = new BeePdf();
      $pdf->create("bee_pdfs", $content);
      </code>
  
      </body>
      </html>';
      $content = sprintf($content, get_bee_logo(), get_bee_name(), get_bee_name(), get_bee_version());
  
      // Método 1
      $pdf = new BeePdf($content); // Se muestra directo en navegador, para descargar pasar en parámetro 2 true y para guardar en parámetro 3 true
  
      // Método 2
      //$pdf = new BeePdf();
      //$pdf->create('bee_pdfs', $content);

    } catch (Exception $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::to('home');
    }

  }

  function test()
  {
    echo 'Probando nuestra base de datos<br><br><br>';
    echo '<pre>';
    
    try {

      // SELECT
      $sql = 'SELECT * FROM tests WHERE id=:id AND name=:name';
      $res = Db::query($sql, ['id' => 1, 'name' => 'John Doe']);
      print_r($res);

      // INSERT
      $sql = 'INSERT INTO tests (name, email, created_at) VALUES (:name, :email, :created_at)';
      $registro =
      [
        'name'       => 'Juanito',
        'email'      => 'juanito@gmail.com',
        'created_at' => now()
      ];
      //$id = Db::query($sql, $registro);
      //print_r($id);

      // UPDATE
      $sql = 'UPDATE tests SET name=:name WHERE id=:id';
      $registro_actualizado = 
      [
        'name' => 'Ricardo Algo',
        'id'   => 3
      ];
      //print_r(Db::query($sql, $registro_actualizado));

      // DELETE
      $sql = 'DELETE FROM tests WHERE id=:id LIMIT 1';
      //print_r(Db::query($sql, ['id' => 4]));

      // ALTER TABLE
      $sql = 'ALTER TABLE tests ADD COLUMN username VARCHAR(255) NULL AFTER name';
      //print_r(Db::query($sql));

    } catch (Exception $e) {
      echo 'Hubo un error: '.$e->getMessage();
    }

    echo '</pre>';
    View::render('test');
  }

  function email()
  {
    try {
      $email   = 'jslocal2@localhost.com';
      $subject = 'El asunto del correo';
      $body    = 'El cuerpo del mensaje, puede ser html o texto plano.';
      $alt     = 'El texto corto del correo, preview del contenido.';
      send_email(get_siteemail(), $email, $subject, $body, $alt);
      echo sprintf('Correo electrónico enviado con éxito a %s', $email);
    } catch (Exception $e) {
      echo $e->getMessage();
    }
  }

  function smtp()
  {
    try {
      send_email('tuemail@hotmail.com', 'tuemail@hotmail.com', 'Probando smtp', '¡Hola mundo!', 'Correo de prueba.');
      echo 'Mensaje enviado con éxito.';
    } catch (Exception $e) {
      echo $e->getMessage();
    }
  }

  function perfil()
  {
    parent::auth();

    $data =
    [
      'title' => 'Perfil de usuario',
      'user'  => User::profile()
    ];

    View::render('perfil', $data);
  }

  function vue()
  {
    $data =
    [
      'title'   => 'Administrador de tareas',
      'padding' => '0px'
    ];

    View::render('vue', $data);
  }
}