/**
 * Carga la lista de memes de los resultados de busqueda de google
 * @returns void
 */
async function loadMemes() {
  const loading      = '<i class="fas fa-spinner fa-spin"></i> Cargando...';
  const memesWrapper = document.getElementById('memesWrapper');
  const memesBtn     = document.getElementById('loadMemes');
  const memesBtnText = memesBtn.innerHTML;
  const page         = parseInt(memesBtn.dataset.page);
  const payload      = {
    csrf: Bee.csrf,
    page,
    query: 'memes gym'
  };

  // Primero vamos a probar como es cargada la información
  memesBtn.innerHTML = loading;
  memesBtn.disabled  = true;
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
    const img          = document.createElement('img');
    img.classList.add('w-100');
    img.src            = meme;

    const link         = document.createElement('a');
    link.classList.add('border', 'rounded', 'overflow-hidden', 'd-flex', 'align-items-center', 'bg-white');
    link.href          = meme;
    link.target        = '_blank';
    link.style.display = 'block';
    link.style.width   = '100%';
    link.style.height  = '100%';
    link.appendChild(img);

    const div          = document.createElement('div');
    div.classList.add('col-6', 'col-md-3', 'col-xl-2');
    div.appendChild(link);

    row.appendChild(div);
  });

  memesWrapper.appendChild(row);

  memesBtn.setAttribute('data-page', page + 1);
  memesBtn.innerHTML = `Siguiente página ${memesBtn.dataset.page}`;
  memesBtn.disabled  = false;
}

// loadMemes();

// document.getElementById('loadMemes').addEventListener('click', loadMemes);

//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////
////////////////// CLASE EN VIVO 7: AUTOGUARDADO
//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////

const autosaveForm    = document.getElementById('autosaveForm');
const btnSubmit       = document.getElementById('btnSubmit');
const timerWrapper    = document.getElementById('timer');
const statusMessage   = document.getElementById('statusMessage');
const saving          = '<i class="fas fa-save fa-fw"></i> Guardando...';
const autosaveIn      = 5000; // milisegundos

const responseWrapper = document.getElementById('responseWrapper');

const id              = autosaveForm.querySelector('#id');
const titulo          = autosaveForm.querySelector('#titulo');
const contenido       = autosaveForm.querySelector('#contenido');

let autosaveTimer; // Variable para almacenar el temporizador
let timer = 0; // Segundos transcurridos

// Función para guardar un registro en la db
async function save(payload) {
  return await fetch('ajax/save', {
    method: 'POST',
    body  : JSON.stringify(payload)
  })
  .then(res => res.json())
  .catch(error => alert(error));
}

// Mostrar el tiempo transcurrido
function showTimer() {
  setInterval(() => {
    timer++;
    timerWrapper.innerHTML = timer;
  }, 1000);
}
showTimer();

// Reiniciar el temporizador cada vez que el usuario interactúa
function resetAutosaveTimer() {
  clearTimeout(autosaveTimer);
  autosaveTimer = setTimeout(autoSave, autosaveIn); // Guardar después de xyz segundos de inactividad
  timer         = 0; // reiniciar el reloj
}

// Escuchar eventos de entrada en los campos
titulo.addEventListener('input', resetAutosaveTimer);
contenido.addEventListener('input', resetAutosaveTimer);

// Función para guardar automáticamente
async function autoSave() {
  const payload = {
    csrf     : Bee.csrf,
    id       : id.value.trim(),
    titulo   : titulo.value.trim(),
    contenido: contenido.value.trim()
  };

  // Validar que haya contenido
  if (payload.titulo == '' && payload.contenido == '') return;

  statusMessage.innerHTML = saving;
  statusMessage.classList.remove('d-none', 'text-danger', 'text-muted');
  statusMessage.classList.add('text-muted');

  // Desactivar el botón
  btnSubmit.disabled = true;

  // Guardar la noticia en la base de datos
  const res = await save(payload);

  // Activar el botón
  btnSubmit.disabled = false;

  if (res.status !== 200) {
    toastr.error(res.msg);
    statusMessage.innerHTML = '';
    statusMessage.classList.add('d-none');
    return;
  }

  // Mostrar el bloque de código
  responseWrapper.innerHTML = `<code>${JSON.stringify(res, null, 2)}</code>`;
  responseWrapper.classList.remove('d-none');

  // Establecer el ID del registro
  id.value = res.data.id;

  // Mensajes de éxito
  toastr.success(res.msg, 'Autoguardado');
  statusMessage.innerHTML = `<i class="fas fa-check fa-fw"></i> ${res.msg}`;
  statusMessage.classList.add('text-success');
  setTimeout(() => {
    statusMessage.innerHTML = '';
    statusMessage.classList.add('d-none');
  }, 2500);
  
  return true;
}

// Guardar al presionar el botón de guardado
autosaveForm.addEventListener('submit', async (e) => {
  e.preventDefault();
  
  const payload = {
    csrf     : Bee.csrf,
    id       : id.value.trim(),
    titulo   : titulo.value.trim(),
    contenido: contenido.value.trim()
  };

  // Validar que haya contenido
  if (payload.titulo == '') {
    toastr.error('Completa el título de la noticia por favor.');
    return;
  };

  // Desactivar el botón de guardado
  btnSubmit.disabled = true;

  // Guardar el registro en la base de datos
  const res = await save(payload);

  if (res.status !== 200) {
    toastr.error(res.msg);
    return;
  }

  toastr.success(res.msg);
  btnSubmit.disabled = false;

  // Borrar el timeout para que sólo empiece a contar de nuevo hasta que se haga input
  clearInterval(autosaveTimer);

  return true;
});