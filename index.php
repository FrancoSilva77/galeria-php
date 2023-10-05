<?php
require './app/funciones.php';
require './app/database.php';

$auth = is_auth();

$db = conectar_db();

// Mostrar Imagenes
$query = "SELECT * FROM imagenes;";
$resultado = mysqli_query($db, $query);

// Mostrar Galerias
$query_galerias = "SELECT * FROM galerias;";
$resultado_galerias = mysqli_query($db, $query_galerias);

incluir_template('header');
?>

<div class="contenedor">

  <h1 class="titulo">Galería de Imagenes </h1>

  <?php if ($auth) :  ?>
    <a href="cerrar-sesion.php" class="boton boton-verde">Cerrar Sesion</a>
    <a href="/pages/admin/index.php" class="boton boton-verde">Panel de Administración</a>
  <?php endif; ?>

  <div class="galeria">
    <?php while ($imagenes = mysqli_fetch_array($resultado)) : ?>

      <div class="card">
        <img class="imagen-principal" src="imagenes/<?php echo $imagenes['nombre']; ?>" alt="<?php echo $imagenes['descripcion'] ?>">
        <h4 class="descripcion'principal"><?php echo $imagenes['descripcion'] ?></h4>
      </div>

    <?php endwhile; ?>
  </div>

  <div class="contenedor-galerias">
    <?php while ($galerias = mysqli_fetch_assoc($resultado_galerias)) :
      // Obtiene la lista de nombres de imágenes
      $nombres_imagenes = $galerias['imagenes'];
      // Divide la lista de nombres en un arreglo
      $arreglo_nombres = explode(",", $nombres_imagenes);
    ?>
      <div class="galerias">
        <div class="swiper swiper-galeria swiper-galerias">
          <div class="swiper-wrapper">
            <?php   // Itera sobre los nombres y muestra cada imagen
            foreach ($arreglo_nombres as $nombre_imagen) : ?>
              <img src="imagenes/<?php echo $nombre_imagen; ?>" alt="Imagen" class="swiper-slide">
            <?php endforeach; ?>
          </div>
          <!-- If we need navigation buttons -->
          <div class="swiper-button-prev"></div>
          <div class="swiper-button-next"></div>
        </div>
        <h3 class="titulo"><?php echo $galerias['titulo']; ?></h3>
      </div>
    <?php endwhile; ?>
  </div>


</div>
<?php incluir_template('footer'); ?>