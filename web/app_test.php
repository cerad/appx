<?php

use Symfony\Component\HttpFoundation\Request;

use Cerad\Module\AppModule\AppKernel;

call_user_func(function()
{
  require '../vendor/autoload.php';
  
  $kernel = new AppKernel();

  $request1  = Request::create('/referees/42','OPTIONS',array('name' => 'Fabien'));
  $response1 = $kernel->handle($request1);
  $response1->send(); echo "\nSENT\n";

  $request2 = Request::create('/referees/42','GET',array('name' => 'Fabien'));
  $response2 = $kernel->handle($request2);
  $response2->send(); echo "\nSENT\n";

  $request3 = Request::create('/referees','GET',array('name' => 'Fabien'));
  $response3 = $kernel->handle($request3);
  $response3->send(); echo "\nSENT\n";
});
