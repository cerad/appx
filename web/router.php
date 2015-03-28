<?php
$uri = $_SERVER["REQUEST_URI"];

if (file_exists('.' . $uri)) return false;

if ($uri == '/')
{
  if (file_exists('./index.php' )) { require 'index.php';  return; }
  if (file_exists('./index.html')) { require 'index.html'; return; }
}
require 'app.php';