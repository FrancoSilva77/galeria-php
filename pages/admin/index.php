<?php

require '../../app/funciones.php';
$auth = is_auth();
if (!$auth) {
  header('Location: /');
}

//* Hacer conexión a la base de datos
require '../../app/database.php';
$db = conectar_db();

// Musetra mensaje condicional
$resultado = $_GET['resultado'] ?? null;

//* Escribir el query 
$query = "SELECT * FROM imagenes";

// Recoger galeria
$query_galeria = "SELECT * FROM galerias";


// Ejecuta tu consulta SQL
$resultado_galerias = mysqli_query($db, $query_galeria);

// * Realizar la consulta a la db
$resultado_consulta = mysqli_query($db, $query);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = $_POST['id'];
  $id = filter_var($id, FILTER_VALIDATE_INT);

  if ($id) {

    $action = $_POST['action'];

    if ($action === 'eliminar_imagenes') {
      // Eliminar archivo
      $query = "SELECT nombre FROM imagenes WHERE id = {$id};";
      $resultado = mysqli_query($db, $query);
      $imagen = mysqli_fetch_assoc($resultado);
      unlink('../../imagenes/' . $imagen['nombre']);

      // Eliminar imagen
      $query = "DELETE FROM imagenes WHERE id = {$id};";
      $resultado = mysqli_query($db, $query);

      if ($resultado) {
        header('Location: /pages/admin?resultado=3');
      }
    } else if ($action === 'eliminar_galeria') {
      // Obtener la lista de nombres de imágenes
      $query = "SELECT imagenes FROM galerias WHERE id = {$id};";
      $resultado = mysqli_query($db, $query);
      $fila = mysqli_fetch_assoc($resultado);
      $imagenes = explode(',', $fila['imagenes']); // Separa los nombres por comas

      // Eliminar cada imagen
      foreach ($imagenes as $nombre_imagen) {
        $ruta_imagen = '../../imagenes/' . $nombre_imagen;
        if (file_exists($ruta_imagen)) {
          unlink($ruta_imagen);
        }
      }

      // Eliminar imagen
      $query = "DELETE FROM galerias WHERE id = {$id};";
      $resultado = mysqli_query($db, $query);

      if ($resultado) {
        header('Location: /pages/admin?resultado=3');
      }
    }
  }
}

incluir_template('header');
?>

<div class="contenedor">
  <h1 class="titulo">Panel de Administración</h1>

  <?php if ($resultado == 1) : ?>
    <p class="alerta exito">Datos almacenados correctamente</p>
  <?php elseif ($resultado == 2) : ?>
    <p class="alerta exito">Datos actualizados correctamente</p>
  <?php elseif ($resultado == 3) : ?>
    <p class="alerta error">Imagen eliminada correctamente</p>
  <?php endif; ?>

  <div class="enlaces">
    <a href="/pages/admin/crear.php" class="boton boton-verde">Subir Nueva Imagen</a>
    <a href="/pages/admin/crear-galeria.php" class="boton boton-verde">Crear Nueva Galeria</a>
    <a href="/index.php" class="boton boton-verde">Inicio</a>
  </div>

  <!-- Comprobacion si hay registros o no -->
  <?php if ($resultado_consulta->num_rows) {  ?>

    <div class="admin-imagenes">
      <h2>Imagenes</h2>
      <table class="tabla-imagenes">
        <thead>
          <tr>
            <th>ID</th>
            <th>Descripción</th>
            <th>Imagen</th>
            <th>Acciones</th>
          </tr>
        </thead>

        <tbody> <!-- Mostrar los resultados -->
          <?php while ($imagen =  mysqli_fetch_assoc($resultado_consulta)) : ?>
            <tr>
              <td><?php echo $imagen['id']; ?></td>
              <td><?php echo $imagen['descripcion']; ?></td>
              <td><img src="/imagenes/<?php echo $imagen['nombre']; ?>" alt="Imagen" class="imagen-tabla"></td>
              <td>
                <a href="/pages/admin/actualizar.php?id=<?php echo $imagen['id']; ?>" class="boton boton-azul">Actualizar</a>
                <form method="POST" class="w-100">
                  <!-- Inputs ocultos -->
                  <input type="hidden" name="id" value="<?php echo $imagen['id']; ?>">
                  <button type="submit" class="boton boton-rojo" name="action" value='eliminar_imagenes'>Eliminar</button>
                </form>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

  <?php } else { ?>
    <h2>No hay ninguna Imagen Aun </h2>
  <?php } ?>

  <!-- Comprobacion si hay registros o no -->
  <?php if ($resultado_galerias->num_rows) {  ?>

    <div class="admin-galerias">
      <h2>Galerias</h2>
      <table class="tabla-imagenes">
        <thead>
          <tr>
            <th>ID</th>
            <th>Descripción</th>
            <th>Imagen</th>
            <th>Acciones</th>
          </tr>
        </thead>

        <tbody> <!-- Mostrar los resultados -->
          <?php while ($galeria = mysqli_fetch_assoc($resultado_galerias)) :
            // Obtiene la lista de nombres de imágenes
            $nombres_imagenes = $galeria['imagenes'];
            // Divide la lista de nombres en un arreglo
            $arreglo_nombres = explode(",", $nombres_imagenes);
          ?>
            <tr>
              <td><?php echo $galeria['id']; ?></td>
              <td><?php echo $galeria['titulo']; ?></td>
              <td>
                <?php   // Itera sobre los nombres y muestra cada imagen
                foreach ($arreglo_nombres as $nombre_imagen) : ?>
                  <img src="/imagenes/<?php echo $nombre_imagen; ?>" alt="Imagen" class="imagen-tabla">
                <?php endforeach; ?>
              </td>
              <td>
                <a href="/pages/admin/actualizar-galeria.php?id=<?php echo $galeria['id']; ?>" class="boton boton-azul">Actualizar</a>
                <form method="POST" class="w-100">
                  <!-- Inputs ocultos -->
                  <input type="hidden" name="id" value="<?php echo $galeria['id']; ?>">
                  <button type="submit" class="boton boton-rojo" name="action" value="eliminar_galeria">Eliminar</button>
                </form>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  <?php } else { ?>
    <h2>No hay ninguna Galeria aun </h2>
  <?php } ?>

</div>

<?php
// Cerrar la conexión
mysqli_close($db);

incluir_template('footer');
?>