<?php

require '../vendor/autoload.php';

use Pimple\Container;

//  Symfony\Component\Routing\Generator\UrlGenerator;
//  Symfony\Component\Routing\Matcher\UrlMatcher;
//  Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
//  Symfony\Component\Routing\Route;

use Symfony\Component\HttpFoundation\Request;
//  Symfony\Component\HttpFoundation\RequestStack;

use Cerad\Module\AppModule\AppServices;
use Cerad\Module\AppModule\AppParameters;

use Cerad\Module\RefereeModule\RefereeRoutes;
use Cerad\Module\RefereeModule\RefereeServices;

class App
{
  protected $container;
  
  public function __construct()
  {
    $this->container = $container = new Container();
    
    $routes = new RouteCollection();

    $refereeRoutes = new RefereeRoutes();
    $refereeRoutes->register($routes);
    
    $container['routes'] = $routes;

    // Parameters and services
    $appParameters = new AppParameters();
    $appParameters->register($container);
    
    $appServices = new AppServices();
    $appServices->register($container);
    
    $refereeServices = new RefereeServices();
    $refereeServices->register($container);
    
  }
  public function dispatch(Request $request)
  {
    // Add request
    $this->container['request_stack']->push($request);
    
    // Match route
    $matcher = $this->container['route_matcher'];
    
    $match = $matcher->matchRequest($request); print_r($match);

    $request->attributes->add($match);
    
    // Use callable and maybe a function?
    $controller = $this->container[$match['_controller_id']];
    
    $actionName = isset($match['_action_name']) ? $match['_action_name'] : 'mainAction';
    
    $response = $controller->$actionName($request);
    
    // Quick test
    /*
    $db = $this->container['database_connection'];
    $row = $db->query('SELECT count(*) AS count FROM referees;')->fetch();
    print_r($row);
    */
    
    // Clean up
    $this->container['request_stack']->pop($request);
    
    return $response;
  }
}
$app = new App();

$request1 = Request::create('/referees/42','PUT',array('name' => 'Fabien'));
$app->dispatch($request1);

$request2 = Request::create('/referees','GET',array('name' => 'Fabien'));
$app->dispatch($request2);
