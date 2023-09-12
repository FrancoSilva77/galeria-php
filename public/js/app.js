document.addEventListener('DOMContentLoaded', function () {
  // Seleccionar los elementos de la interfaz
  const formulario = document.querySelector('#crear-imagen');
  const inputNombre = document.querySelector('#nombre');
  const inputDescripcion = document.querySelector('#descripcion');
  const referenciaAlerta = document.querySelector('.alertas');

  // Asignar eventos
  inputDescripcion.addEventListener('input', validar);
  inputNombre.addEventListener('input', validar);

  formulario.addEventListener('submit', subirFoto);

  function subirFoto(e) {
    e.preventDefault();

    setTimeout(() => {
      this.submit();
    }, 3000);
  }

  function validar(e) {
    if (e.target.value.trim() === '') {
      mostrarAlerta('El campo es Obligatorio', referenciaAlerta);
      return;
    }

    if (e.target.value.length < 10) {
      mostrarAlerta(
        'La descripción debe tener al meno 10 caracteres',
        referenciaAlerta
      );
      return;
    }

    if (inputNombre.files.length === 0) {
      mostrarAlerta('La imagen es obligatoria', referenciaAlerta);
      return;
    }

    limpiarAlerta(referenciaAlerta);
  }

  function mostrarAlerta(mensaje, referencia) {
    limpiarAlerta(referencia);

    const error = document.createElement('P');
    error.textContent = mensaje;
    error.classList.add('alerta', 'error');

    referencia.appendChild(error);
  }

  function limpiarAlerta(referencia) {
    const alerta = referencia.querySelector('.error');
    if (alerta) {
      alerta.remove();
    }
  }
});
