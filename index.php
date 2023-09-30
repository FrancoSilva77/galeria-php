<?php
require './app/funciones.php';
require './app/database.php';

$auth = is_auth();

$db = conectar_db();

$query = "SELECT * FROM imagenes";
$resultado = mysqli_query($db, $query);

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
</div>

<?php incluir_template('footer'); ?>