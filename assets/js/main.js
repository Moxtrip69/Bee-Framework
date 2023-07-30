$(document).ready(function() {
  /**
   * Alerta para confirmar una acción establecida en un link o ruta específica
   */
  $('body').on('click', '.confirmar', function(e) {
    e.preventDefault();

    let url = $(this).attr('href'),
    ok      = confirm('¿Estás seguro?');

    // Redirección a la URL del enlace
    if (ok) {
      window.location = url;
      return true;
    }
    
    console.log('Acción cancelada.');
    return true;
  });

  /**
   * Inicializa summernote el editor de texto avanzado para textareas
   */
  function init_summernote() {
    if ($('.summernote').length == 0) return;

    $('.summernote').summernote({
      placeholder: 'Escribe en este campo...',
      tabsize: 2,
      height: 300
    });
  }

  /**
   * Inicializa tooltips en todo el sitio
   */
  function init_tooltips() {
    if (['bs','bs5','bs_lumen','bs_lux','bs_litera','bs_vapor','bs_zephyr'].includes(Bee.css_framework) != true) return;
    
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList        = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    });
  }

  /**
   * Dismiss notificaciones para Bulma framework
   */
  $('body').on('click', '.delete-bulma-notification', delete_bulma_notification);
  function delete_bulma_notification(e) {
    var notification = $(this).closest('.notification');
    notification.remove();
  }

  /** 
   * Configuración inicial de Toastr js | si es necesario se puede retirar o quitar 
   * */
  function init_toastr_setup() {
    if (Bee.toastr === false) return;

    toastr.options = {
      "closeButton": true,
      "debug": false,
      "newestOnTop": true,
      "progressBar": true,
      "positionClass": "toast-top-right",
      "preventDuplicates": false,
      "onclick": null,
      "showDuration": "300",
      "hideDuration": "1000",
      "timeOut": "5000",
      "extendedTimeOut": "1000",
      "showEasing": "swing",
      "hideEasing": "linear",
      "showMethod": "fadeIn",
      "hideMethod": "fadeOut"
    }
  }

  // Inicialización de elementos
  init_summernote();
  init_tooltips();
  init_toastr_setup();

  ////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////
  ///////// INGRESA TU FUNCIONALIDAD CON JQUERY AQUÍ ABAJO
  ////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////
});

document.addEventListener('DOMContentLoaded', () => {
  ////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////
  ///////// FUNCIONES SÓLO DE PRUEBA, PUEDEN SER BORRADAS
  ////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////
  init_bee_greeting();
  init_db_test();
  test_ajax();
  test_api();

  // var valorCompra = 300;
  // var moneda      = 'MXN';
  // var metadata    = {
  //   producto     : 'Nombre del producto',
  //   categoria    : 'Categoría del producto',
  //   numero_pedido: 'Número de pedido 123',
  //   // Agrega más metadatos personalizados si es necesario
  // };

  // fbq('track', 'Purchase', {
  //   value      : valorCompra,
  //   currency   : moneda,
  //   custom_data: metadata
  // });

  ////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////
  ///////// INGRESA TU FUNCIONALIDAD CON VANILLA JAVASCRIPT AQUÍ ABAJO
  ////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////
});

// Función para crear y mostrar el loader dinámicamente
function showLoader() {
  var loaderContainer = document.createElement('div');
  loaderContainer.className = 'loader-container';
  loaderContainer.id = 'loaderContainer';
  loaderContainer.style.display = 'block';

  var loader = document.createElement('div');
  loader.className = 'loader';
  loader.textContent = 'Cargando...';

  loaderContainer.appendChild(loader);
  document.body.appendChild(loaderContainer);
}

// Función para eliminar el loader dinámicamente
function hideLoader() {
  var loaderContainer = document.getElementById('loaderContainer');
  if (loaderContainer) {
    loaderContainer.parentNode.removeChild(loaderContainer);
  }
}

/**
 * Mostrar Bee object en entorno de desarrollo
 */
function init_bee_greeting() {
  console.log('////////// Bienvenido a Bee Framework Versión ' + Bee.bee_version + ' //////////');
  console.log('//////////////////// www.joystick.com.mx ////////////////////');
  if (Bee?.is_local == true) {
    console.log(Bee);
  }
}

/**
 * Prueba la conexión a la base de datos
 * @since 1.1.4
 * 
 * @returns 
 */
function init_db_test() {
  const wrapper = document.querySelector('.wrapper_db_test');

  if (!wrapper) return;

  const alert   = wrapper.querySelector('.alert');

  alert.classList.remove('alert-success');
  alert.classList.add('alert-danger');

  alert.innerHTML       = '<i class="fas fa-spinner fa-spin"></i> Probando conexión a la base de datos...';
  wrapper.style.display = 'block';

  setTimeout(() => {
    fetch('ajax/db_test')
    .then(res => res.json())
    .then(res => {
      if (res.status === 200) {
        alert.classList.remove('alert-danger', 'd-none');
        alert.classList.add('alert-success');
        alert.innerHTML = res.msg;
      } else {
        alert.innerHTML = res.msg;
      }

      setTimeout(() => {
        alert.classList.add('d-none');
        alert.innerHTML = '';
      }, 3000);
    })
    .catch(err => {
      alert.innerHTML = 'Hubo un error en la petición, vuelve a intentarlo.';
    });
  }, 1000);
}

/**
 * Prueba de peticiones ajax al backend en versión 1.1.3
 */
async function test_ajax() {
  const wrapper  = document.getElementById('test_ajax');

  if (!wrapper) return;

  showLoader();

  const body = {
    csrf: Bee.csrf
  }

  try {
    const res = await fetch('ajax/test', {
      headers: { "Content-Type": "application/json" },
      method: "POST",
      body: JSON.stringify(body)
    })
    .then(res => res.json())
    .catch(error => {
      throw new Error(error);
    });

    if (res.status === 200) {
      toastr.success(res.msg, 'Prueba AJAX');
    } else {
      toastr.error(res.msg, '¡Error!');
    }
  } catch (error) {
    toastr.error(error, '¡Error!');
  }

  hideLoader();
}

/**
 * Prueba de peticiones a la API en versión 1.5.5
 */
async function test_api() {
  const wrapper = document.getElementById('test_api');

  if (!wrapper) return;

  const res = await fetch(Bee.url + 'api/posts', {
    headers    : { 'Authorization': `Bearer ${Bee.private_key}` },
    method     : 'GET'
  }).then(res => res.json());

  if (res.status === 200) {
    toastr.success(`API funcional, fueron cargados <b>${res.data.length}</b> registros.`, 'Prueba de la API')
  } else {
    toastr.error(res.msg, 'Hubo un error');
  }
}