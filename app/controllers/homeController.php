<?php 

class homeController extends Controller {
  function __construct()
  {
  }

  function index()
  {
    $data =
    [
      'title' => 'Home',
      'bg'    => 'dark'
    ];

    register_to_bee_obj('bee_var', 'Súper Bee Var');

    View::render('bee', $data);
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

  function flash()
  {
    parent::auth();

    View::render('flash', ['title' => 'Flash', 'user' => User::profile()]);
  }

  function gastos()
  {
    View::render('gastos');
  }

  function yumi()
  {
    View::render('yumi');
  }
}