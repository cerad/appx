<?php

// So we don't need to rely on rewrites
/*
if (isset($_SERVER["REQUEST_URI"]) && $_SERVER["REQUEST_URI"] == '/')
{
  if (file_exists('./index.php' )) { require 'index.php';  return; }
  if (file_exists('./index.html')) { require 'index.html'; return; }
}*/
use Symfony\Component\HttpFoundation\Request;

use Cerad\Module\AppModule\AppKernel;

call_user_func(function()
{
  require '../vendor/autoload.php';

  $app = new AppKernel('prod',false);

  $request = Request::createFromGlobals();
  $response = $app->handle($request);
  $response->send();
});