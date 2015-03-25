<?php
namespace Cerad\Module\RefereeModule;

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

class RefereeRoutes
{
  public function register($routesx)
  {
    $routes = new RouteCollection();
    
    $routes->add('referee',  new Route(
      '/referees/{id}',
      ['_controller_id' => 'referee_controller']
    ));
    
    // One route, switch in controller
    $routes->add('referees',  new Route(
      '/referees',
      ['_controller_id' => 'referee_controller']
    ));
    
    $routesx->addCollection($routes);
  }
}