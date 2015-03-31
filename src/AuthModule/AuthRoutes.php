<?php
namespace Cerad\Module\AuthModule;

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

class AuthRoutes
{
  public function __construct($container,$prefix = '/auth')
  { 
    $service = function($c) use($prefix)
    {
      $routes = new RouteCollection();
      
      $authTokenAction = function($request) use ($c)
      {
        $controller = $c->get('auth_token_controller');
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
      $routes->add('auth_resource_token',  new Route(
        '/tokens/{id}',['_action' => $authTokenAction]
      ));
      // One route, switch in controller
      $routes->add('auth_resource_tokens',  new Route(
        '/tokens',['_action' => $authTokenAction]
      ));
      $routes->addPrefix($prefix);
      return $routes;
    };
    $container->set('auth_routes',$service,'routes');
  }
}