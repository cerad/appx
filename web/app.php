<?php

use Cerad\Component\HttpMessage\Request;

use Cerad\Module\AppModule\AppKernel;

call_user_func(function()
{
  require '../vendor/autoload.php';

  $app = new AppKernel('prod',false);

  $request  = new Request($_SERVER);
  $response = $app->handle($request);
  $response->send();
});