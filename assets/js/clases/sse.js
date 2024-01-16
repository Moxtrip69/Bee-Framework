const wrapper = document.getElementById('wrapperRegistrosSSE');

function initSSE() {
  const eventSource = new EventSource('ajax/cargar_reportes_sse');

  // Manejar eventos recibidos del servidor
  eventSource.onmessage = function(event) {
    const data = JSON.parse(event.data);

    // Actualizar la tabla con los nuevos datos recibidos
    updateTable(data);
  };

  // Manejar errores
  eventSource.onerror = function(error) {
    console.error('Error en la conexión SSE: ', error);

    // Intentar reconectar después de un tiempo
    setTimeout(initSSE, 3000);
  };
}

function updateTable(data) {
  // Referencia a nuestra tabla
  const table   = document.getElementById('tableRegistrosSSE');

  // Iterar sobre los nuevos datos y agregar filas a la tabla
  data.forEach((reporte) => {
    // Crear nueva fila
    const newRow = table.insertRow();

    // Añadir celdas a la fila
    const idCell      = newRow.insertCell(0);
    const reporteCell = newRow.insertCell(1);

    // Asignar valores a las celdas
    idCell.innerHTML      = reporte.id;
    reporteCell.innerHTML = reporte.titulo;
  });
}

// Iniciar conexión SSE cuando se carga la página
window.onload = (e) => {
  console.log(e);
  initSSE();
}