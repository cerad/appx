<?php
namespace Cerad\Module\RefereeModule;

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

class RefereeRoutes
{
  public function register($container,$routesx)
  {
    $routes = new RouteCollection();
    
    // TODO: add generic resourceAction function to container
    $refereeAction = function($request) use ($container)
    {
      $controller = $container['referee_controller'];
      $id = $request->attributes->get('id');
      switch($request->getMethod())
      {
        case 'GET':
          return $id !== null
            ? $controller->getOneAction($request,$request->attributes->get('id'))
            : $controller->searchAction($request);
          
        case 'POST':   return $controller->postAction  ($request);
        case 'PUT':    return $controller->putAction   ($request,$id);
        case 'DELETE': return $controller->deleteAction($request,$id);
      }
      // Toss exception
    };
    // TODO: Make RestRoute function
    $routes->add('referee_resource_one',  new Route(
      '/referees/{id}',
      [
        '_action' => $refereeAction
      ]
    ));
    // One route, switch in controller
    $routes->add('referee_resource',  new Route(
      '/referees',
      [
        '_action' => $refereeAction
      ]
    ));
    
    $routesx->addCollection($routes);
  }
}