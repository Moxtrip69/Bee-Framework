document.addEventListener('DOMContentLoaded', () => {
  const generarPdfForm    = document.getElementById('generarPdfForm');
  const generarPdfFormBtn = document.getElementById('generarPdfFormBtn');

  generarPdfForm.addEventListener('submit', agregar_documento);

  async function agregar_documento(e) {
    e.preventDefault();
    
    const nombre       = document.getElementById('nombre').value;
    const pancho       = document.getElementById('correo-electronico').value;
    const mensaje      = document.getElementById('mensaje').value;
    const csrf         = Bee.csrf;
    const textoDefault = generarPdfFormBtn.innerHTML;

    const payload = {
      nombre,
      'email': pancho,
      mensaje,
      csrf
    };

    // Validación

    generarPdfFormBtn.disabled = true;

    // Petición asíncrona async / await
    const res = await fetch('ajax/agregar-documento', {
      'method': 'POST',
      'body': JSON.stringify(payload)
    })
    .then(res => res.json());

    if (res.status !== 201) {
      toastr.error(res.msg);
      generarPdfFormBtn.disabled = false;
      return;
    }

    generarPdfForm.reset();
    generarPdfFormBtn.innerHTML = 'PDF generado con éxito.';
    toastr.success(res.msg);

    setTimeout(() => {
      window.location.href = res.data.url_pdf;
    }, 3000);
  }
});