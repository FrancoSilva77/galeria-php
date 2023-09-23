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

// * Realizar la consulta a la db
$resultado_consulta = mysqli_query($db, $query);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = $_POST['id'];
  $id = filter_var($id, FILTER_VALIDATE_INT);

  if ($id) {
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
  }
}

incluir_template('header');
?>

<div class="contenedor">
  <h1 class="titulo">Administrador de Galeria</h1>

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
              <input type="submit" class="boton boton-rojo" value="Eliminar">
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<?php
// Cerrar la conexión
mysqli_close($db);

incluir_template('footer');
?>