<?php
namespace Cerad\Module\AuthModule;

class AuthRoutes
{
  public function __construct($container,$prefix = '/auth')
  { 
    $authTokensAction = function($request) use($container)
    {
      $controller = $container->get('auth_token_controller');
      switch($request->getMethod())
      {
        case 'POST': return $controller->postAction($request);
      }
    };
    $authTokensRoute = function($path, $context = null) use($authTokensAction)
    { 
      $params = [
        '_action' => $authTokensAction,
      ];
      if ($path === '/auth/tokens') 
      {
        if (!in_array($context['method'],['OPTIONS','POST'])) return false;
        return $params;
      }
      return false;
    };
    $container->set('route_auth_tokens',function() use($authTokensRoute)
    {
      return $authTokensRoute;
    },'routes');
  }
}