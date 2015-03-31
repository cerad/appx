<?php
namespace Cerad\Module\RefereeModule;

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

class RefereeRoutes
{
  public function __construct($container,$prefix = '/api')
  { 
    $service = function($c) use($prefix)
    {
      $routes = new RouteCollection();
      
      $refereeAction = function($request) use ($c)
      {
        $controller = $c->get('referee_controller');
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
        '/referees/{id}',['_action' => $refereeAction]
      ));
      // One route, switch in controller
      $routes->add('referee_resource',  new Route(
        '/referees',['_action' => $refereeAction]
      ));
      // One route, switch in controller
      $routes->add('referee_resource_sra',  new Route(
        '/refereesx',
        [
          '_action' => $refereeAction,
          '_roles'  => 'ROLE_SRA'
        ]
      ));
      $routes->addPrefix($prefix);
      return $routes;
    };
    $container->set('referee_routes',$service,'routes');
  }
}