// const dt = new DataTable('#miTabla', {
//   serverSide: true, // se requiere para hacer una petición por cada cambio de página
//   processing: true, // se añade un loader
//   ajax: {
//     url: 'ajax/datatables',
//     data: function (d) {
//       d.page = Math.ceil((d.start + 1) / d.length);
//       d.per_page = d.length;
//     }, // la página actual,
//     dataSrc: 'rows'
//   },
//   // Listar columnas que quieres mostrar o usar de preferencia que coincidan con la estructura base html
//   columns: [
//     { data: 'id' },
//     { data: 'nombre' },
//     { data: 'titulo' },
//     { data: 'contenido' },
//     { data: 'creado' }
//   ],
//   paging: true,
//   pageLength: 1 // cuántos registros mostrar por página, se envía al backend
// });

const tablaClientes106 = $("#tbllistadoClientes").dataTable({
  bProcessing: true, // activamos procesamiento del datatables
  bServerSide: true, // paginación y filtrado por el servidor
  responsive: true,
  sAjaxSource: 'ajax/listar-clientes',
  rowCallback: function (row, data) {
    // Como mero ejemplo
    if (data[4] == null || data[4].length == 0) {
      $($(row).find("td")).css("background-color", "orange");
      $($(row).find("td")).css("color", "white");
    }
  }
}).DataTable();