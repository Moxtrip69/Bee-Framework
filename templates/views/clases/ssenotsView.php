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
    <div class="col-12 col-md-6 offset-md-3 py-5">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h3><?php echo $d->title; ?></h3>
          <button class="btn btn-sm btn-success" id="generarNot"><i class="fas fa-plus"></i></button>
        </div>
        <div class="card-body">
          <nav class="navbar navbar-expand-lg bg-body-tertiary">
            <div class="container-fluid">
              <a class="navbar-brand" href="<?php echo get_base_url(); ?>"><img src="<?php echo get_logo(); ?>" alt="<?php echo get_sitename(); ?>" style="width: 100px;"></a>
              <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
              </button>
              <div class="collapse navbar-collapse justify-content-end" id="navbarNavDropdown">
                <ul class="navbar-nav">
                  <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="#">Inicio</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#">Cursos</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#">Precios</a>
                  </li>
                  <li class="nav-item dropdown">
                    <a class="nav-link position-relative" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" id="notWrapper">
                      Notificaciones<i class="fas fa-bell fa-fw ms-2"></i>
                      <span class="position-absolute badge rounded-pill bg-danger" style="top: -5px; right: -5px;" id="notTotal">0</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" id="notList">
                      <li class="dropdown-item">Sin notificaciones.</li>
                    </ul>
                  </li>
                </ul>
              </div>
            </div>
          </nav>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once INCLUDES . 'footer.php'; ?>

<script>
  const generarNot  = document.getElementById('generarNot');
  const notWrapper  = document.getElementById('notWrapper');
  const notTotal    = document.getElementById('notTotal');
  const notList     = document.getElementById('notList');
  const eventSource = new EventSource('ajax/sse');
  const audio       = new Audio(`${Bee.uploaded}alerta.mp3`);

  eventSource.onmessage = function(event) {
    const res            = JSON.parse(event.data);
    const totales        = res.data.totales;
    const pendientes     = res.data.pendientes;
    const cargadas       = res.data.cargadas;
    const vistas         = res.data.vistas;
    const notificaciones = res.data.notificaciones;

    if (res.status !== 200) {
      notTotal.innerHTML = 0;
      notList.innerHTML  = `<li class="dropdown-item">${res.msg}</li>`;
      return;
    }

    // Si no hay notificaciones
    if (totales === 0) {
      notTotal.innerHTML = 0;
      notList.innerHTML  = `<li class="dropdown-item">No hay notificaciones.</li>`;
      return;
    }

    // Muestra la notificación en un elemento HTML
    notList.innerHTML = '';
    notificaciones.forEach(notificacion => {
      // Crear un elemento HTML para la notificación
      const notificacionElemento     = document.createElement("li");
      notificacionElemento.classList.add('dropdown-item');

      // Agregar el contenido de la notificación
      notificacionElemento.innerHTML = notificacion.titulo;

      // Si no ha sido vista aún
      if (notificacion.status !== 'vista') {
        notificacionElemento.classList.add('bg-light', 'text-dark');

        // Agrega un manejador de eventos para el hover
        notificacionElemento.addEventListener("mouseenter", async function (e) {
          // Actualiza el estado de la notificación en la base de datos
          const res = await actualizarNotificacion(notificacion.id);
          if (res.status !== 200) {
            return;
          }

          // Actualizar los estilos del elemento
          notificacionElemento.classList.remove('bg-light', 'text-dark');

          // Restar uno al total de notificaciones
          notTotal.innerHTML = parseInt(notTotal.innerHTML) - 1;
        });
      }

      // Agregar la notificación al contenedor de notificaciones
      notList.appendChild(notificacionElemento);
    });

    // Actualizar la burbuja de nuevas notificaciones
    notTotal.innerHTML = totales - vistas;

    // Reproducir el sonido de notificaciones sólo si hay nuevas
    if (pendientes > 0) {
      audio.play();
    }
  };

  async function actualizarNotificacion(id) {
    const payload = {
      csrf: Bee.csrf,
      id: id
    }
    return await fetch('ajax/actualizar-notificacion', {
      method: 'POST',
      body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .catch(err => alert(err));
  }

  generarNot.addEventListener('click', generarNotificacion);
  async function generarNotificacion(e) {
    const payload = {
      csrf: Bee.csrf
    };

    generarNot.disabled = true;
    const res = await fetch('ajax/generar-notificacion', {
      method: 'POST',
      body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .catch(err => alert(err));

    if (res.status !== 201) {
      toastr.error(res.msg);
      return;
    }

    generarNot.disabled = false;
    toastr.success(res.msg, 'Notificación generada');
    return;
  }
</script>