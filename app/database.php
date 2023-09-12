<?php

function conectar_db(): mysqli
{

  $host = 'localhost';
  $usuario = 'root';
  $password = 'root';
  $database = 'galeria_php';

  $db = mysqli_connect($host, $usuario, $password, $database);

  if (!$db) {
    echo "Error no se conecto";
    exit;
  }

  return $db;
}
