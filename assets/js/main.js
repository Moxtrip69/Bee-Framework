$(document).ready(function() {

  /**
   * @since 1.1.4
   */
  init_bee_greeting();
  function init_bee_greeting() {
    console.log('////////// Bienvenido a Bee Framework Versión ' + Bee.bee_version + ' //////////');
    console.log('//////////////////// www.joystick.com.mx ////////////////////');
    if (Bee.is_local == true) {
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
    var wrapper = $('.wrapper_db_test'),
    alert       = $('.alert', wrapper);

    if (wrapper.length == 0) return;

    $.ajax({
      url: 'ajax/db_test',
      type: 'get',
      dataType: 'json',
      cache: false,
      headers: {
        'auth_public_key' : Bee.public_key,
        'auth_private_key': 'Esta es otra prueba 2'
      },
      beforeSend() {
        alert.removeClass('alert-success').addClass('alert-danger');
        alert.html('Probando conexión a la base de datos...');
        wrapper.fadeIn();
      }
    }).done(res => {
      if (res.status === 200) {
        alert.removeClass('alert-danger').addClass('alert-success');
        alert.html(res.msg);
      } else {
        alert.html(res.msg);
      }
    }).fail(err => {
      alert.html(err.responseJSON.msg);
    }).always(() => {
    });
  }

  /**
   * Prueba de peticiones ajax al backend en versión 1.1.3
   */
  function test_ajax() {
    var body = $('body'),
    hook     = 'bee_hook',
    action   = 'post',
    csrf     = Bee.csrf;

    if ($('#test_ajax').length == 0) return;

    $.ajax({
      url: 'ajax/test',
      type: 'post',
      dataType: 'json',
      data : { hook , action , csrf },
      beforeSend: function() {
        body.waitMe();
      }
    }).done(function(res) {
      if (res.status === 200) {
        toastr.success(res.msg, '¡Bien!');
      } else {
        toastr.error(res.msg, '¡Error!');
      }
     
    }).fail(function(err) {
      toastr.error('Prueba AJAX fallida.', '¡Upss!');
    }).always(function() {
      body.waitMe('hide');
    })
  }
  
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

  /**
   * Interactua con la API de Bee framework función de prueba y demostración
   * @param {string} method El tipo de petición a ejecutar
   * @param {mixed} data Información del cuerpo de la petición
   * @returns mixed
   */
  function posts(method = 'get', data = null) {
    return $.ajax({
      url        : Bee.url + 'api/posts',
      headers    : { 'auth_private_key': Bee.private_key },
      type       : method,
      dataType   : 'json',
      contentType: false,
      processData: false,
      cache      : false,
      data       : data
    });
  }

  // Es posible borrar la siguiente línea de código
  posts('get').done(res => toastr.success(`Fueron cargados <b>${res.data.length}</b> posts registrados desde la API.`));

  // Inicialización de elementos
  init_summernote();
  init_tooltips();
  init_toastr_setup();
  init_db_test();
  test_ajax();

  ////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////
  ///////// NO REQUERIDOS, SOLO PARA EL PROYECTO DEMO DE GASTOS E INGRESOS
  ////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////

  // Guardar o actualizar opciones
  $('.bee_save_options').on('submit', bee_save_options);
  function bee_save_options(event) {
    event.preventDefault();

    var form = $('.bee_save_options'),
    data     = new FormData(form.get(0)),
    hook     = 'bee_hook',
    action   = 'add';
    data.append('hook', hook);
    data.append('action', action);

    // AJAX
    $.ajax({
      url: 'ajax/bee_save_options',
      type: 'post',
      dataType: 'json',
      contentType: false,
      processData: false,
      cache: false,
      data : data,
      beforeSend: function() {
        form.waitMe();
      }
    }).done(function(res) {
      if(res.status === 200 || res.status === 201) {
        toastr.success(res.msg, '¡Bien!');
        bee_get_movements();
      } else {
        toastr.error(res.msg, '¡Upss!');
      }
    }).fail(function(err) {
      toastr.error('Hubo un error en la petición', '¡Upss!');
    }).always(function() {
      form.waitMe('hide');
    })
  }
});