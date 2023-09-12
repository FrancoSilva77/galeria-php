<?php
require '../../app/funciones.php';
require '../../app/database.php';

$auth = is_auth();
if (!$auth) {
  header('Location: /');
}

incluir_template('header');
?>

<div class="contenedor-sm">
  <h1 class="titulo">Galería de Imagenes - Crear galeria</h1>

  <form action="" class="formulario" enctype="multipart/form-data">
    <div class="campo">
      <label for="descripcion">Descripción</label>
      <input type="text" id="descripcion" name="descripcion" placeholder="Titulo para la galeria">
    </div>

    <div class="campo">
      <label for="galeria">Seleccione las imagenes para la galeria</label>
      <input type="file" id="galeria" name="galeria" accept="image/jpeg, image/png">
    </div>

    <input type="submit" value="Crear galeria" class="boton boton-verde">

    <a href="/pages/admin/" class="boton boton-verde">Volver</a>

  </form>
</div>



<?php incluir_template('footer'); ?>