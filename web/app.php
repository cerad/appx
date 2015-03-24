<?php

require '../vendor/autoload.php';

use Pimple\Container;

use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class App extends Container
{
  public function __construct()
  {
    parent::__construct();
    
    $routes = new RouteCollection();

    $routes->add('referee_get',  new Route(
      '/referees/{id}',
      ['_controller' => 'referee_controller', '_action'=> 'getAction'],
      [],[],'',[],
      ['GET']
    ));
    $routes->add('referee_put',  new Route(
      '/referees/{id}',
      ['_controller' => 'referee_controller'],
      [],[],'',[],
      ['PUT']
    ));
    $routes->add('referee_post',  new Route(
      '/referees/{id}',
      ['_controller' => 'referee_controller', '_action'=> 'postAction'],
      [],[],'',[],
      ['POST']
    ));
    $routes->add('referee_delete',  new Route(
      '/referees/{id}',
      ['_controller' => 'referee_controller', '_action'=> 'deleteAction'],
      [],[],'',[],
      ['DELETE']
    ));
    // One route, switch in controller
    $routes->add('referees',  new Route(
      '/referees',
      ['_controller' => 'referee_controller']
    ));
    
    $this['routes'] = $routes;
    
    // Request/Routes
    $this['request_stack'] = function($c)
    {
      return new RequestStack();
    };
    $this['request_context'] = function($c)
    {
      $context = new RequestContext();
      $context->fromRequest($c['request_stack']->getMasterRequest());
      return $context;
    };
    $this['route_matcher'] = function($c)
    {
      return new UrlMatcher($c['routes'], $c['request_context']);
    };
    $this['route_generator'] = function($c)
    {
      return new UrlGenerator($c['routes'], $c['request_context']);
    };
    // Controllers
    $this['referee_controller'] = function($c)
    {
      return new Cerad\Module\RefereeModule\RefereeController();
    };
  }
  public function dispatch(Request $request)
  {
    // Add request
    $this['request_stack']->push($request);
    
    // Match route
    $matcher = $this['route_matcher'];
    
    $match = $matcher->matchRequest($request); print_r($match);

    $request->attributes->add($match);
    
    $controller = $this[$match['_controller']];
    
    $action = isset($match['_action']) ? $match['_action'] : 'mainAction';
    
    $response = $controller->$action($request);
    
    // Clean up
    $this['request_stack']->pop($request);
    
    return $response;
  }
}
$app = new App();

$request1 = Request::create('/referees/42','PUT',array('name' => 'Fabien'));
$app->dispatch($request1);

$request2 = Request::create('/referees','GET',array('name' => 'Fabien'));
$app->dispatch($request2);
