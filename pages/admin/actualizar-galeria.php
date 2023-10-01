<?php
require '../../app/database.php';
require '../../app/funciones.php';


$id = $_GET['id'];
$id = filter_var($id, FILTER_VALIDATE_INT);

if (!$id) {
  header('Location: /pages/admin/');
}

$db = conectar_db();

// Obtener los datos de la galeria
$query = "SELECT * FROM galerias WHERE id = {$id}";
$resultado = mysqli_query($db, $query);
$galeriaTotal = mysqli_fetch_assoc($resultado);

$errores = [];

$titulo = $galeriaTotal['titulo'];
$galeria = $galeriaTotal['imagenes'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $titulo = mysqli_real_escape_string($db, $_POST['titulo']);

  if (!$titulo) {
    $errores[] = 'El titulo es obligatoria';
  }

  if (strlen($titulo) < 10) {
    $errores[] = 'El titulo debe tener al menos 10 caracteres';
  }

  $numero_imagenes = count($_FILES['galeria']['name']);
  $imagenes_con_medida_adecuada = true;
  $medida = 3000 * 1000;

  if ($numero_imagenes > 0) {
    $imagenes = [];

    for ($i = 0; $i < $numero_imagenes; $i++) {
      $amount_imagen = $_FILES['galeria']['size'][$i];

      if ($amount_imagen >= $medida) {
        $errores[] = 'Cada Imagen debe pesar menos de 5MB';
        $imagenes_con_medida_adecuada = false;
      }
    }

    if ($imagenes_con_medida_adecuada) {
      // Delete existing images only if new images are uploaded
      $archivos_a_eliminar = explode(",", $galeria);

      if (!empty($_FILES['galeria']['name'][0])) {
        foreach ($archivos_a_eliminar as $archivo) {
          $ruta_archivo = '../../imagenes/' . $archivo;

          if (file_exists($ruta_archivo)) {
            unlink($ruta_archivo);
          }
        }
      }

      for ($i = 0; $i < $numero_imagenes; $i++) {
        // Resto del código para procesar y guardar las nuevas imágenes
        // ...
        $archivo_temporal = $_FILES['galeria']['tmp_name'][$i];
        $nombre_imagen = $_FILES['galeria']['name'][$i];
        $tipo_imagen = $_FILES['galeria']['type'][$i];
        $amount_imagen = $_FILES['galeria']['size'][$i];

        $nombre_imagen_ext = explode(".", $nombre_imagen);
        $extension_imagen = strtolower(end($nombre_imagen_ext));
        $nuevo_nombre_imagen = md5(uniqid(rand(), true)) .  '.' . $extension_imagen;
        $galeria = $nuevo_nombre_imagen;

        $extensiones_permitidas = ['png', 'jpg', 'jpeg'];

        if (in_array($extension_imagen, $extensiones_permitidas)) {
          // Directorio de las imágenes

          // Crear carpeta
          $carpeta_imagenes = '../../imagenes/';

          if (!is_dir($carpeta_imagenes)) {
            mkdir($carpeta_imagenes);
          }

          $destino_imagenes = $carpeta_imagenes . $nuevo_nombre_imagen;

          // Comprimir la imagen
          $calidad = 70;
          $imagen_original = '';

          if (
            $extension_imagen === 'png'
          ) {
            $imagen_original = imagecreatefrompng($archivo_temporal);
          } else {
            $imagen_original = imagecreatefromjpeg($archivo_temporal);
          }

          if (imagejpeg($imagen_original, $destino_imagenes, $calidad)) {
            array_push($imagenes, $galeria);
          }
        }
      }

      // Actualizar la lista de imágenes solo si se proporcionaron nuevas imágenes
      if (!empty($imagenes)) {
        $lista_imagenes = implode(",", $imagenes);
        $query = " UPDATE galerias SET titulo = '{$titulo}', imagenes = '{$lista_imagenes}' WHERE id = '{$id}';";
      } else {
        $query = " UPDATE galerias SET titulo = '{$titulo}' WHERE id = '{$id}';";
      }
    }
  } else {
    // Si no se proporcionaron nuevas imágenes, actualizar solo el título
    $query = " UPDATE galerias SET titulo = '{$titulo}' WHERE id = '{$id}';";
  }

  if (empty($errores)) {
    $resultado = mysqli_query($db, $query);

    if ($resultado) {
      header('Location: /pages/admin?resultado=1');
    }
  }
}

incluir_template('header');
?>

<div class="contenedor-sm">

  <h1 class="titulo">Crear Galería</h1>

  <div class="alertas">
    <?php foreach ($errores as $error) : ?>
      <div class="alerta error">
        <?php echo $error; ?>
      </div>
    <?php endforeach; ?>
  </div>

  <form id="crear-galeria" class="formulario" method="POST" enctype="multipart/form-data">
    <div class="campo">
      <label for="titulo">Titulo de la Galería</label>
      <input type="text" id="titulo" name="titulo" placeholder="Descripción de la imagen" value="<?php echo $titulo; ?>">
    </div>

    <div class="campo">
      <label for="galeria">Seleccione mas de 1 imagen</label>
      <input type="file" id="galeria" name="galeria[]" multiple accept="image/jpeg, image/png">
    </div>

    <input type="submit" value="Crear Galeria" class="boton boton-verde">
    <a href="/pages/admin/" class="boton boton-verde">Volver</a>
  </form>

</div>

<?php
incluir_template('footer');
?>