<?php
// Importar la conexion
require 'app/database.php';

$db = conectar_db();
// Crear el email y password 
$email = 'fran@gmail.com';
$password = 'fran123';

$password_hash = password_hash($password, PASSWORD_BCRYPT);


// Query para agregar el usuario

$query = "INSERT INTO usuarios (email, password) VALUES ( '{$email}', '{$password_hash}')";

// echo $query;

// Agregar usuario a la base de datos
mysqli_query($db, $query);
