<?php
namespace Cerad\Module\RefereeModule;

class RefereeRoutes
{
  public function __construct($container)
  { 
    $refereeAction = function($request) use ($container)
    {
      $controller = $container->get('referee_controller');
      $id = $request->getAttribute('id');
      switch($request->getMethod())
      {
        case 'GET':
          return $id !== null
            ? $controller->getOneAction($request,$id)
            : $controller->searchAction($request);
          
        case 'POST':   return $controller->postAction  ($request);
        case 'PUT':    return $controller->putAction   ($request,$id);
        case 'DELETE': return $controller->deleteAction($request,$id);
      }
    };
    $routeReferees = function($path, $context = null) use($refereeAction)
    {  
      $params = [
        'id'      => null,
        '_action' =>  $refereeAction,
        '_roles'  => ['ROLE_ASSIGNOR']
      ];
      if ($path === '/referees') 
      {
      //if (!in_array($context['method'],['GET','POST'])) return false;

        return $params;
      }
      $matches = [];
        
      if (!preg_match('#^/referees/(\d+$)#', $path, $matches)) return false;

      $params['id'] = $matches[1]; // No typecast, ussf id's are 16 digits long
        
      return $params;
    };
    $routeRefereesService = function() use ($routeReferees)
    {
      return $routeReferees;
    };
    $container->set('route_referees',$routeRefereesService,'routes');
  }
}