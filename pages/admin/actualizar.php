<?php
require '../../app/funciones.php';


$auth = is_auth();
if (!$auth) {
  header('Location: /');
}

// Validar que sea un id válido
$id = $_GET['id'];
$id = filter_var($id, FILTER_VALIDATE_INT);

if (!$id) {
  header('Location: /pages/admin/');
}

// Base de datos
require '../../app/database.php';

$db = conectar_db();

// Obtener los datos de la imagen
$consulta = "SELECT * FROM imagenes WHERE id = {$id}";
$resultado = mysqli_query($db, $consulta);
$imagenTotal = mysqli_fetch_assoc($resultado);


//* Escribir el query 
$query = "SELECT * FROM imagenes";

// * Realizar la consulta a la db
$resultado_consulta = mysqli_query($db, $query);

incluir_template('header');

// Arreglo con errores
$errores = [];

$descripcion = $imagenTotal['descripcion'];
$imagenVista = $imagenTotal['nombre'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  $descripcion = mysqli_real_escape_string($db, $_POST['descripcion']);

  // Asignar files hacia una variable
  // La variable debe ser diferente a la que se asigna al obtener los datos de la base de datos
  $imagen = $_FILES['nombre'];

  if (!$descripcion) {
    $errores[] = 'La descripción es obligatoria';
  }

  if (strlen($descripcion) < 10) {
    $errores[] = 'La descripción debe tener al menos 15 caracteres';
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

    $nombreImagen = '';

    if ($imagen['name']) {
      // Eliminar imagen previa
      unlink($carpetaImagenes . $imagenTotal['nombre']);
      // Generar nombre único
      $nombreImagen = md5(uniqid(rand(), true)) .  '.jpg';
      move_uploaded_file($imagen['tmp_name'], $carpetaImagenes . $nombreImagen);
    } else {
      $nombreImagen = $imagenTotal['nombre'];
    }

    // Insertar en la base de datos
    $query = " UPDATE imagenes SET descripcion = '{$descripcion}', nombre = '{$nombreImagen}' WHERE id = '{$id}'; ";

    // Es rescomendable probsr el query que queramos hacer
    // echo $query;

    $resultado = mysqli_query($db, $query);

    if ($resultado) {
      header('Location: /pages/admin?resultado=2');
    }
  }
}

?>

<div class="contenedor-sm">

  <h1 class="titulo">Actualizar imagenes</h1>

  <div class="alertas">
    <?php foreach ($errores as $error) : ?>
      <div class="alerta error">
        <?php echo $error; ?>
      </div>
    <?php endforeach; ?>
  </div>

  <form class="formulario" method="POST" enctype="multipart/form-data">
    <div class="campo">
      <label for="descripcion">Descripción</label>
      <input type="text" id="descripcion" name="descripcion" placeholder="Descripción de la imagen" value="<?php echo $descripcion; ?>">
    </div>

    <div class="campo">
      <label for="nombre">Seleccione una imagen</label>
      <input type="file" id="nombre" name="nombre" accept="image/jpeg, image/png">
      <img src='/imagenes/<?php echo $imagenVista; ?>' alt="Imagen de Galeria" class="imagen-sm">
    </div>

    <input type="submit" value="Actualizar Imagen" class="boton boton-verde">
    <a href="/pages/admin/" class="boton boton-verde">Volver</a>
  </form>

</div>