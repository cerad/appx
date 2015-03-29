<?php

require '../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;

use Cerad\Module\AppModule\AppKernel;

$app = new AppKernel();

$request1  = Request::create('/referees/42','OPTIONS',array('name' => 'Fabien'));
$response1 = $app->handle($request1);
$response1->send(); echo "\nSENT\n";

$request2 = Request::create('/referees/200','GET',array('name' => 'Fabien'));
$response2 = $app->handle($request2);
$response2->send(); echo "\nSENT\n";

$request3 = Request::create('/referees','GET',array('name' => 'Fabien'));
$response3 = $app->handle($request3);
$response3->send(); echo "\nSENT\n";
