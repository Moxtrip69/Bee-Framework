<!DOCTYPE html>
<html lang="<?php echo SITE_LANG; ?>">
<head>
  <!-- Agregar basepath para definir a partir de donde se deben generar los enlaces y la carga de archivos -->
  <base href="<?php echo BASEPATH; ?>">

  <meta charset="UTF-8">
  
  <title><?php echo isset($d->title) ? $d->title.' - '.get_sitename() : 'Bienvenido - '.get_sitename(); ?></title>

  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  
  <!-- Bootstrap core CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css">

  <!-- Custom styles for this template -->
  <link href="<?php echo CSS.'form-validation.css'; ?>" rel="stylesheet">

  <!-- Toastr css -->
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

  <!-- Waitme css -->
  <link rel="stylesheet" href="<?php echo PLUGINS.'waitme/waitMe.min.css'; ?>">

  <style>
    .btn {
      border-radius: 2px;
    }

    .bg-gradient {
      background: rgba(38, 38, 38, 1);
      background: -moz-linear-gradient(left, rgba(38, 38, 38, 1) 0%, rgba(28, 33, 28, 1) 100%);
      background: -webkit-gradient(left top, right top, color-stop(0%, rgba(38, 38, 38, 1)), color-stop(100%, rgba(28, 33, 28, 1)));
      background: -webkit-linear-gradient(left, rgba(38, 38, 38, 1) 0%, rgba(28, 33, 28, 1) 100%);
      background: -o-linear-gradient(left, rgba(38, 38, 38, 1) 0%, rgba(28, 33, 28, 1) 100%);
      background: -ms-linear-gradient(left, rgba(38, 38, 38, 1) 0%, rgba(28, 33, 28, 1) 100%);
      background: linear-gradient(to right, rgba(38, 38, 38, 1) 0%, rgba(28, 33, 28, 1) 100%);
      filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#262626', endColorstr='#1c211c', GradientType=1);
    }

    .bd-placeholder-img {
      font-size: 1.125rem;
      text-anchor: middle;
      -webkit-user-select: none;
      -moz-user-select: none;
      -ms-user-select: none;
      user-select: none;
    }

    @media (min-width: 768px) {
      .bd-placeholder-img-lg {
        font-size: 3.5rem;
      }
    }
  </style>
</head>
<body class="bg-light">

  <!-- Navbar -->
  <div class="d-flex flex-column flex-md-row align-items-center p-3 px-md-4 mb-3 bg-white border-bottom shadow-sm">
    <h5 class="my-0 mr-md-auto font-weight-normal"><a href="<?php echo URL; ?>"><img src="<?php echo IMAGES.'yumi_150.png'; ?>" alt="Yumi" class="img-fluid" style="width: 70px;"></a></h5>
    <nav class="my-2 my-md-0 mr-md-3">
      <a class="p-2 text-dark" href="consultas/agendar">Agendar consulta</a>
      <a class="p-2 text-dark" href="pacientes">Pacientes</a>
      <a class="btn btn-success" href="login">Ingresar</a>
      <a class="btn btn-outline-danger" href="logout">Cerrar sesión</a>
    </nav>
  </div>
  <!-- ends navbar -->

  <!-- Formulario y contenido -->
  <div class="container">
    <div class="row">
      <div class="offset-xl-2 col-xl-8 py-5">
        <h2 class="mb-4">Agenda tu consulta</h2>
        
        <div class="card">
          <div class="card-header">Completa el formulario</div>
          <div class="card-body">
            <form method="post" action="process.php" enctype="multipart/form-data">
              <div class="mb-3 row">
                <div class="col-xl-6 col-12">
                  <label for="nombres">Nombre(s)</label>
                  <input type="text" class="form-control" name="nombres" required>
                </div>
                <div class="col-xl-6 col-12">
                  <label for="apellidos">Apellido(s)</label>
                  <input type="text" class="form-control" name="apellidos" required>
                </div>
              </div>
              <div class="mb-3 row">
                <div class="col-xl-6 col-12">
                  <label for="email">Correo electrónico</label>
                  <input type="email" class="form-control" name="email" required>
                </div>
                <div class="col-xl-6 col-12">
                  <label for="telefono">Teléfono</label>
                  <input type="text" class="form-control" name="telefono" required>
                </div>
              </div>
              <div class="mb-3 row">
                <div class="col-xl-6 col-12">
                  <label for="sexo">Sexo</label>
                  <select name="sexo" id="sexo" class="form-control" required>
                    <option value="">Selecciona una opción...</option>
                    <option value="femenino">Femenino</option>
                    <option value="masculino">Masculino</option>
                    <option value="otro">Otro</option>
                  </select>
                </div>
                <div class="col-xl-6 col-12">
                  <label for="edad">Edad</label>
                  <input type="number" class="form-control" name="edad" min="1" max="120" required>
                </div>
              </div>
              <div class="mb-3">
                <label for="notas">Describe los síntomas</label>
                <textarea name="notas" id="notas" cols="10" rows="5" class="form-control" required></textarea>
              </div>
              <div class="mb-3">
                <label for="fecha">Reservar consulta</label>
                <input type="date" class="form-control" name="fecha" required>
              </div>

              <button class="btn btn-success" type="submit">Agendar ahora</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- ends contenido -->

  <!-- tabla de pacientes -->
  <div class="container">
    <div class="row">
      <div class="col-12">
        <h2 class="mb-4">Todos los pacientes</h2>
        
        <div class="card">
          <div class="card-header">Lista de pacientes</div>
          <div class="card-body table-responsive">
            <table class="table table-hover table-striped table-borderless">
              <thead>
                <th>Número</th>
                <th>Nombre completo</th>
                <th>Sexo</th>
                <th>Fecha</th>
                <th>Status</th>
                <th></th>
              </thead>
              <tbody>
                <tr>
                  <td>012032</td>
                  <td>Pancho Doe</td>
                  <td>Masculino</td>
                  <td>12 de Agosto</td>
                  <td>Revisado</td>
                  <td>
                    <div class="btn-group">
                      <button type="button" class="btn btn-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>
                      <div class="dropdown-menu">
                        <a class="dropdown-item" href="#"><i class="fas fa-eye"></i> Ver</a>
                        <a class="dropdown-item" href="#"><i class="fas fa-laptop-medical"></i> Revisar</a>
                        <a class="dropdown-item" href="#"><i class="fas fa-check"></i> Terminado</a>
                        <a class="dropdown-item" href="#"><i class="fas fa-trash"></i> Borrar</a>
                      </div>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td>856322</td>
                  <td>María Doe</td>
                  <td>Femenino</td>
                  <td>12 de Agosto</td>
                  <td>Pendiente</td>
                  <td>
                    <div class="btn-group">
                      <button type="button" class="btn btn-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>
                      <div class="dropdown-menu">
                        <a class="dropdown-item" href="#"><i class="fas fa-eye"></i> Ver</a>
                        <a class="dropdown-item" href="#"><i class="fas fa-laptop-medical"></i> Revisar</a>
                        <a class="dropdown-item" href="#"><i class="fas fa-check"></i> Terminado</a>
                        <a class="dropdown-item" href="#"><i class="fas fa-trash"></i> Borrar</a>
                      </div>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- ends contenido -->

  <br><br>

  <!-- vista de paciente -->
  <div class="container">
    <div class="row">
      <div class="col-12">
        <h2 class="mb-4">Viendo paciente Pancho Doe</h2>
      </div>

      <div class="col-xl-4">
        <img src="https://picsum.photos/500" alt="Paciente" class="img-fluid img-thumbnail img-circle">
      </div>
      <div class="col-xl-8">
        <div class="card">
          <div class="card-header">Información general</div>
          <div class="card-body">
            <p><strong>Nombre(s):</strong> Pancho</p>
            <p><strong>Apellido(s):</strong> Doe</p>
            <p><strong>Sexo:</strong> Masculino</p>

            <p><strong>Correo electrónico:</strong> micorreo@doe.com</p>
            <p><strong>Teléfono:</strong> 11223344</p>

            <p><strong>Fecha agendada:</strong> 12 de Agosto 2020</p>

            <p><strong>Notas agregadas o síntomas:</strong> Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quas suscipit et quod possimus laudantium rerum fugiat, deleniti facilis dignissimos perspiciatis.</p>

            <div class="button-group">
              <a href="" class="btn btn-warning"><i class="fas fa-laptop-medical"></i> Revisar</a>
              <a href="" class="btn btn-success"><i class="fas fa-check"></i> Terminado</a>
              <a href="" class="btn btn-danger"><i class="fas fa-trash"></i> Borrar</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- ends contenido -->

  <br><br>

  <!-- vista de paciente -->
  <div class="container">
    <div class="row">
      <div class="col-12">
        <h2 class="mb-4">Revisando paciente Pancho Doe</h2>
      </div>

      <div class="col-xl-4">
        <img src="https://picsum.photos/500" alt="Paciente" class="img-fluid img-thumbnail img-circle">
      </div>
      <div class="col-xl-8">
        <div class="card">
          <div class="card-header">Información general</div>
          <div class="card-body">
            <p><strong>Nombre(s):</strong> Pancho</p>
            <p><strong>Apellido(s):</strong> Doe</p>
            <p><strong>Sexo:</strong> Masculino</p>

            <p><strong>Correo electrónico:</strong> micorreo@doe.com</p>
            <p><strong>Teléfono:</strong> 11223344</p>

            <p><strong>Fecha agendada:</strong> 12 de Agosto 2020</p>

            <p><strong>Notas agregadas o síntomas:</strong> Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quas suscipit et quod possimus laudantium rerum fugiat, deleniti facilis dignissimos perspiciatis.</p>

            <form action="" class="">
              <div class="mb-3">
                <label for="recomendaciones">Recomendaciones</label>
                <textarea class="form-control" name="recomendaciones" reuqired></textarea>
              </div>
              
              <div class="mb-3">
                <label for="receta">Receta</label>
                <input type="file" class="form-control-file" name="receta" id="receta" accept="application/pdf" required>
              </div>

              <button class="btn btn-success" type="submit"><i class="fas fa-envelope-open-text"></i> Terminar revisión</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- ends contenido -->

  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>