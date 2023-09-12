<?php

// Base de datos
require '../../app/database.php';
require '../../app/funciones.php';

$auth = is_auth();
if (!$auth) {
  header('Location: /');
}

$db = conectar_db();
incluir_template('header');

// Arreglo con errores
$errores = [];

$descripcion = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // echo '<pre>';
  // var_dump($_POST);
  // echo '</pre>';

  // echo '<pre>';
  // var_dump($_FILES);
  // echo '</pre>';

  $descripcion = mysqli_real_escape_string($db, $_POST['descripcion']);

  // Asignar files hacia una variable

  $imagen = $_FILES['nombre'];

  if (!$descripcion) {
    $errores[] = 'La descripción es obligatoria';
  }

  if (strlen($descripcion) < 10) {
    $errores[] = 'La descripción debe tener al menos 15 caracteres';
  }

  if (!$imagen['name'] || $imagen['error']) {
    $errores[] = 'La imagen es obligatoria';
  }

  // Validar tamaño de la imagen
  $medida = 5000 * 1000;

  if ($imagen['size'] > $medida) {
    $errores[] = 'La imagen es muy pesada';
  }


  // Revisar que el arreglo de errores este vacio
  if (empty($errores)) {

    // * Subida de archivos

    // Crear carpeta
    $carpetaImagenes = '../../imagenes/';

    if (!is_dir($carpetaImagenes)) {
      mkdir($carpetaImagenes);
    }

    // Generar nombre único
    $nombreImagen = md5(uniqid(rand(), true)) .  '.jpg';
    move_uploaded_file($imagen['tmp_name'], $carpetaImagenes . $nombreImagen);


    // Insertar en la base de datos
    $query = " INSERT INTO imagenes ( descripcion, nombre ) VALUES ( '$descripcion', '$nombreImagen' ) ;";
    $resultado = mysqli_query($db, $query);

    if ($resultado) {
      header('Location: /pages/admin?resultado=1');
    }
  }
}

?>

<div class="contenedor-sm">

  <h1 class="titulo">Subir imagenes</h1>

  <div class="alertas">
    <?php foreach ($errores as $error) : ?>
      <div class="alerta error">
        <?php echo $error; ?>
      </div>
    <?php endforeach; ?>
  </div>

  <form id="crear-imagen" action="/pages/admin/crear.php" class="formulario" method="POST" enctype="multipart/form-data">
    <div class="campo">
      <label for="descripcion">Descripción</label>
      <input type="text" id="descripcion" name="descripcion" placeholder="Descripción de la imagen" value="<?php echo $descripcion; ?>">
    </div>

    <div class="campo">
      <label for="nombre">Seleccione una imagen</label>
      <input type="file" id="nombre" name="nombre" accept="image/jpeg, image/png">
    </div>

    <input type="submit" value="Subir Imagen" class="boton boton-verde">
    <a href="/pages/admin/" class="boton boton-verde">Volver</a>
  </form>

</div>

<?php incluir_template('footer') ?>