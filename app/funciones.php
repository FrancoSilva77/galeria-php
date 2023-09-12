<?php

require 'app.php';

function incluir_template(string $template)
{
  include TEMPLATES_URL . "/{$template}.php";
}

function is_auth(): bool
{
  session_start();

  $auth = $_SESSION['login'];

  if ($auth) {
    return true;
  }

  return false;
}
