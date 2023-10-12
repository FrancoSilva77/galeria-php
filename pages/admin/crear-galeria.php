<?php
require '../../app/database.php';
require '../../app/funciones.php';

$db = conectar_db();

// Crear la galeria
$galeria = '';

$errores = [];

$titulo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $titulo = mysqli_real_escape_string($db, $_POST['titulo']);

  //* Contamos la cantidad de imagens que queremos publicar
  $numero_imagenes = count($_FILES['galeria']['name']);

  // * Arreglo para guardar los nombres de las imagenes en la bd
  $imagenes = [];


  if (!$titulo) {
    $errores[] = 'El titulo es obligatoria';
  }

  if (strlen($titulo) < 10) {
    $errores[] = 'El titulo debe tener al menos 10 caracteres';
  }

  if ($numero_imagenes < 2) {
    $errores[] = 'Las imagenes son obligatorias';
  }

  $medida = 3000 * 1000;
  $imagenes_con_medida_adecuada = true;

  if ($numero_imagenes > 0) {

    for ($i = 0; $i < $numero_imagenes; $i++) {
      $amount_imagen = $_FILES['galeria']['size'][$i];

      if ($amount_imagen >= $medida) {
        $errores[] = 'Cada Imagen debe pesar menos de 5MB';
        $imagenes_con_medida_adecuada = false;
      }
    }

    if ($imagenes_con_medida_adecuada) {
      for ($i = 0; $i < $numero_imagenes; $i++) {
        $archivo_temporal = $_FILES['galeria']['tmp_name'][$i];
        $nombre_imagen = $_FILES['galeria']['name'][$i];
        $tipo_imagen = $_FILES['galeria']['type'][$i];
        $amount_imagen = $_FILES['galeria']['size'][$i];

        $nombre_imagen_ext = explode(".", $nombre_imagen);
        $extension_imagen = strtolower(end($nombre_imagen_ext));
        $nuevo_nombre_imagen = md5(uniqid(rand(), true)) .  '.' . $extension_imagen;
        $galeria = $nuevo_nombre_imagen;

        $extenisones_permitidas = ['png', 'jpg', 'jpeg'];

        if (in_array($extension_imagen, $extenisones_permitidas)) {
          // Directorio de las imagenes

          // Crear carpeta
          $carpetaImagenes = '../../imagenes/';

          if (!is_dir($carpetaImagenes)) {
            mkdir($carpetaImagenes);
          }

          $destino_imagenes = $carpetaImagenes . $nuevo_nombre_imagen;

          // Comprimir la imagen
          $calidad = 70;
          $imagen_original = '';
          if ($extension_imagen === 'png') {
            $imagen_original = imagecreatefrompng($archivo_temporal);
          } else {
            $imagen_original = imagecreatefromjpeg($archivo_temporal);
          }

          if (imagejpeg($imagen_original, $destino_imagenes, $calidad)) {
            array_push($imagenes, $galeria);
          }
        }
      }

      $lista_imagenes = implode(",", $imagenes);

      if (empty($errores)) {
        $query = " INSERT INTO galerias (titulo, imagenes) VALUES ('$titulo', '$lista_imagenes');";
        $resultado = mysqli_query($db, $query);

        if ($resultado) {
          header('Location: /pages/admin?resultado=1');
        }
      }
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

  <form id="crear-galeria" action="/pages/admin/crear-galeria.php" class="formulario" method="POST" enctype="multipart/form-data">
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