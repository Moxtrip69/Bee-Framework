/**
 * Carga la lista de memes de los resultados de busqueda de google
 * @returns void
 */
async function loadMemes() {
  const loading = '<i class="fas fa-spinner fa-spin"></i> Cargando...';
  const memesWrapper = document.getElementById('memesWrapper');
  const memesBtn = document.getElementById('loadMemes');
  const memesBtnText = memesBtn.innerHTML;
  const page = parseInt(memesBtn.dataset.page);
  const payload = {
    csrf: Bee.csrf,
    page,
    query: 'memes gym'
  };

  // Primero vamos a probar como es cargada la información
  memesBtn.innerHTML = loading;
  memesBtn.disabled = true;
  const memes = await fetch('ajax/load-memes', {
    method: 'POST',
    body: JSON.stringify(payload)
  }).then(res => res.json()).catch(error => alert(error));

  if (memes.status !== 200) {
    toastr.error(memes.msg);
    return;
  }

  // Limpia el contenedor de memes y muestra los nuevos memes
  memesWrapper.innerHTML = '';

  const row = document.createElement('div');
  row.classList.add('row', 'g-3');

  memes.data.forEach((meme) => {
    const img = document.createElement('img');
    img.classList.add('w-100');
    img.src = meme;

    const link = document.createElement('a');
    link.classList.add('border', 'rounded', 'overflow-hidden', 'd-flex', 'align-items-center', 'bg-white');
    link.href = meme;
    link.target = '_blank';
    link.style.display = 'block';
    link.style.width = '100%';
    link.style.height = '100%';
    link.appendChild(img);

    const div = document.createElement('div');
    div.classList.add('col-6', 'col-md-3', 'col-xl-2');
    div.appendChild(link);

    row.appendChild(div);
  });

  memesWrapper.appendChild(row);

  memesBtn.setAttribute('data-page', page + 1);
  memesBtn.innerHTML = `Siguiente página ${memesBtn.dataset.page}`;
  memesBtn.disabled = false;
}

// loadMemes();

// document.getElementById('loadMemes').addEventListener('click', loadMemes);

//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////
////////////////// CLASES EN VIVO
//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////

const generarNot  = document.getElementById('generarNot');
const notWrapper  = document.getElementById('notWrapper');
const notTotal    = document.getElementById('notTotal');
const notList     = document.getElementById('notList');
const eventSource = new EventSource('ajax/sse');
const audio       = new Audio(`${Bee.uploaded}alerta.mp3`);

eventSource.onmessage = event => {
  const res            = JSON.parse(event.data);
  const totales        = res.data.totales;
  const pendientes     = res.data.pendientes;
  const cargadas       = res.data.cargadas;
  const vistas         = res.data.vistas;
  const notificaciones = res.data.notificaciones;

  if (res.status !== 200) {
    notTotal.innerHTML = 0;
    notList.innerHTML = `<li class="dropdown-item">${res.msg}</li>`;
    return;
  }

  // Si no hay notificaciones
  if (totales === 0) {
    notTotal.innerHTML = 0;
    notList.innerHTML = `<li class="dropdown-item">No hay notificaciones.</li>`;
    return;
  }

  // Muestra la notificación en un elemento HTML
  notList.innerHTML = '';
  notificaciones.forEach(notificacion => {
    // Crear un elemento HTML para la notificación
    const notificacionElemento = document.createElement("li");
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