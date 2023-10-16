document.addEventListener('DOMContentLoaded', async function() {
  const wrapper                 = document.getElementById('calendario');
  const agregarEventoModal      = new bootstrap.Modal('#agregarEventoModal');
  const verEventoModal          = new bootstrap.Modal('#verEventoModal');
  const verEventoWrapper        = document.getElementById('verEventoWrapper');

  const agregarEventoForm       = document.getElementById('agregarEventoForm');
  const titulo                  = document.getElementById('titulo');
  const fecha                   = document.getElementById('fecha');
  const color                   = document.getElementById('color');
  const agregarEventoFormSubmit = document.getElementById('agregarEventoFormSubmit');

  const config = {
    themeSystem: 'bootstrap5',
    initialView: 'dayGridMonth',
    locale: 'es-MX',
    headerToolbar: {
      start: 'multiMonthYear dayGridMonth,dayGridWeek,timeGridDay agregarEventoBtn',
      center: 'title',
      end: 'today prev,next'
    },
    buttonText: {
      today: 'Hoy',
      year : 'Año',
      month: 'Mes',
      week : 'Semana',
      day  : 'Día',
      list : 'Lista'
    },
    customButtons: {
      agregarEventoBtn: {
        text: 'Agendar cita',
        click: function(e) {
          agregarEventoModal.show();
        }
      }
    },
    editable: true,
    selectable: true,
    dateClick: function(info) {
      fecha.value = info.dateStr;
      titulo.focus();

      agregarEventoModal.show();
    },
    eventClick: async function(info) {
      // Cargar de la base de datos el evento
      const res = await cargarEvento(info.event.id);

      if (res.status !== 200) {
        toastr.error(res.msg);
        return;
      }

      const evento = res.data;

      verEventoWrapper.innerHTML = 
      /*html*/
      `
      <h3>${evento.titulo}</h3>
      <p>${evento.fecha}</p>
      <button type="button" class="btn btn-danger" id="eliminarEventoBtn" data-id="${evento.id}">Eliminar evento</button>
      `;

      verEventoModal.show();

      const eliminarEventoBtn = document.getElementById('eliminarEventoBtn');
      eliminarEventoBtn.addEventListener('click', async (e) => {
        if (!confirm('¿Estás seguro?')) return;

        const idEvento = e.target.dataset.id;
        const res      = await eliminarEvento(idEvento);

        if (res.status !== 200) {
          toastr.error(res.msg);
          return;
        }

        toastr.success(res.msg);

        setTimeout(() => {
          verEventoModal.hide();
          verEventoWrapper.innerHTML = res.msg;
          calendario.refetchEvents();
        }, 1000);
      });
    },
    eventDrop: async function(info) {
      const evento = info.event;

      if (!confirm('¿Estás seguro del cambio?')) {
        info.revert();
        return;
      }

      const res = await actualizarEvento(evento.id, evento.start.toISOString());

      if (res.status !== 200) {
        toastr.error(res.msg);
        info.revert();
        return;
      }

      toastr.success(res.msg);
    },
    events: 'ajax/eventos'
    // eventSources: [
    //   'eventos.php',
    //   'eventos2.php',
    //   'citas.php',
    //   'ajax/eventos'
    // ]
    // events: [
    //   {
    //     title: 'Trabajación',
    //     start: '2023-10-04 09:00:00',
    //     end: '2023-10-04 17:00:00',
    //     allDay: false
    //   }
    // ]
  }

  const calendario = new FullCalendar.Calendar(wrapper, config);
  
  calendario.render();

  agregarEventoFormSubmit.addEventListener('click', async (e) => {
    e.preventDefault();

    if (titulo.value == '') {
      titulo.focus();
      toastr.error('Ingresa un título válido por favor.');
      return;
    }

    if (fecha.value == '') {
      fecha.focus();
      toastr.error('Ingresa una fecha válida por favor.');
      return;
    }

    // Agregar el nuevo evento en la base de datos
    const res = await agregarEvento(titulo.value, `${fecha.value} 00:00:00`, color.value);

    if (res.status !== 201) {
      toastr.error(res.msg);
      return;
    }

    toastr.success(res.msg);
    agregarEventoForm.reset();
    agregarEventoModal.hide();
    calendario.refetchEvents();
  });

  async function agregarEvento(titulo, fecha, color) {
    const payload = {
      csrf: Bee.csrf,
      titulo,
      fecha,
      color
    };

    return await fetch('ajax/agregar-evento', {
      method: 'POST',
      body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .catch(err => alert(err));
  }

  async function actualizarEvento(idEvento, fecha) {
    const payload = {
      csrf: Bee.csrf,
      id: idEvento,
      fecha: fecha,
    };

    return await fetch('ajax/actualizar-evento', {
      method: 'POST',
      body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .catch(err => alert(err));
  }

  async function cargarEvento(idEvento) {
    return await fetch(`ajax/evento/${idEvento}`, {
      method: 'GET'
    })
    .then(res => res.json())
    .catch(err => alert(err));
  }

  async function eliminarEvento(idEvento) {
    const payload = {
      csrf: Bee.csrf,
      id: idEvento
    };

    return await fetch('ajax/eliminar-evento', {
      method: 'POST',
      body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .catch(err => alert(err));
  }
});