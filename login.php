<?php
require './app/funciones.php';
require './app/database.php';

$db = conectar_db();

$errores = [];

// Autenticar al usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $email = mysqli_real_escape_string($db,  filter_var($_POST['email'], FILTER_VALIDATE_EMAIL));

  $password = mysqli_real_escape_string($db,  $_POST['password']);

  if (!$email) {
    $errores[] = 'El correo es obligatorio o no es valido';
  }

  if (!$password) {
    $errores[] = 'El password es obligatorio';
  }

  if (empty($errores)) {
    // Revisasi el usuario existe
    $query = "SELECT * FROM usuarios WHERE email = '{$email}';";
    $resultado = mysqli_query($db, $query);

    if ($resultado->num_rows) {
      // Revisar si el password es correcto
      $usuario = mysqli_fetch_assoc($resultado);

      // Verificar si el password es correcto

      $auth = password_verify($password, $usuario['password']);

      if ($auth) {
        // El usuario es correcto
        session_start();

        //Llenar el arreglo de la sesion
        $_SESSION['usuario'] = $usuario['email'];
        $_SESSION['login'] = true;

        header('Location: /pages/admin');
      } else {
        $errores[] = 'El password es incorrecto';
      }
    } else {
      $errores[] = 'El usuario no existe';
    }
  }
}


incluir_template('header');
?>
<div class="contenedor">
  <h1 class="titulo">Galería de Imagenes - Iniciar Sesion</h1>

  <div class="alertas">
    <?php foreach ($errores as $error) : ?>
      <div class="alerta error"><?php echo $error; ?></div>
    <?php endforeach; ?>
  </div>

  <form action="" class="formulario" method="POST">
    <div class="campo">
      <label for="email">Correo Electronico:</label>
      <input type="email" id="email" name="email" placeholder="Correo Electronico">
    </div>

    <div class="campo">
      <label for="password">Contraseña</label>
      <input type="password" id="password" name="password">
    </div>

    <input type="submit" class="boton boton-verde" value="Iniciar Sesion">


    <?php incluir_template('footer'); ?>
  </form>
</div>