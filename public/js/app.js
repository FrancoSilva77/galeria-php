document.addEventListener('DOMContentLoaded', function () {
  // Seleccionar los elementos de la interfaz
  // Formulario Subir Imagen
  const formularioSubirImagen = document.querySelector('#crear-imagen');
  const inputNombre = document.querySelector('#nombre');
  const inputDescripcion = document.querySelector('#descripcion');

  // Formulario Subir Galeria
  const formularioSubirGaleria = document.querySelector('#crear-galeria');
  const inputTitulo = document.querySelector('#titulo');
  const inputGaleria = document.querySelector('#galeria');

  // Interfaz General
  const referenciaAlerta = document.querySelector('.alertas');

  if (formularioSubirImagen) {
    // Asignar eventos
    inputDescripcion.addEventListener('input', validarFoto);
    inputNombre.addEventListener('input', validarFoto);

    formularioSubirImagen.addEventListener('submit', subirArchivos);
  }

  if (formularioSubirGaleria) {
    inputTitulo.addEventListener('input', validarGaleria);
    inputGaleria.addEventListener('input', validarGaleria);

    formularioSubirGaleria.addEventListener('submit', subirArchivos);
  }

  function validarFoto(e) {
    if (e.target.value.trim() === '') {
      mostrarAlerta('El campo es Obligatorio', referenciaAlerta);
      return;
    }

    if (e.target.value.length < 10) {
      mostrarAlerta(
        'La descripción debe tener al menos 10 caracteres',
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

  function validarGaleria(e) {
    if (e.target.value.trim() === '') {
      mostrarAlerta('El campo es Obligatorio', referenciaAlerta);
      return;
    }

    if (e.target.value.length < 10) {
      mostrarAlerta(
        'El titulo al menos debe tener 10 caracteres',
        referenciaAlerta
      );
      return;
    }

    if (inputGaleria.files.length < 2) {
      mostrarAlerta('Debes seleccionar al menos 2 imagenes', referenciaAlerta);
      return;
    }

    limpiarAlerta(referenciaAlerta);
  }

  // Funciones Generales
  function subirArchivos(e) {
    e.preventDefault();

    setTimeout(() => {
      this.submit();
    }, 2000);
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
