<?php
$uri = $_SERVER["REQUEST_URI"];

if (is_file('.' . $uri)) return false;

$stdout = fopen('php://stdout', 'w');

fwrite($stdout, 'Request: ' . $uri . "\n");

fclose($stdout);

require 'app.php';
