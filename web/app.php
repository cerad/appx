<?php

require '../vendor/autoload.php';

use Pimple\Container;

//  Symfony\Component\Routing\Generator\UrlGenerator;
//  Symfony\Component\Routing\Matcher\UrlMatcher;
//  Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
//  Symfony\Component\Routing\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
//  Symfony\Component\HttpFoundation\RequestStack;

use Cerad\Module\FrameworkModule\FrameworkServices;
use Cerad\Module\FrameworkModule\Event\KernelRequestEvent;
use Cerad\Module\FrameworkModule\Event\KernelResponseEvent;

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
    $frameworkServices = new FrameworkServices();
    $frameworkServices->register($container);
    
    $appParameters = new AppParameters();
    $appParameters->register($container);
    
    $appServices = new AppServices();
    $appServices->register($container);
    
    $refereeServices = new RefereeServices();
    $refereeServices->register($container);
    
    // Setup up event subscribers, need tagging system
    $dispatcher   = $this->container['event_dispatcher'];
    $corsListener = $this->container['kernel_cors_listener'];
    $dispatcher->addSubscriber($corsListener);
    
  }
  public function dispatch(Request $request)
  {
    // Add request
    $this->container['request_stack']->push($request);
    
    // Request transformation, change request but not replace it?
    $dispatcher   = $this->container['event_dispatcher'];
    $requestEvent = new KernelRequestEvent($request);
    $dispatcher->dispatch(KernelRequestEvent::name,$requestEvent);
    if ($requestEvent->hasResponse())
    {
      return $requestEvent->getResponse();
    }
    
    // Add request
    $this->container['request_stack']->push($request);
    
    // Match route
    $matcher = $this->container['route_matcher'];
    
    $match = $matcher->matchRequest($request);

    $request->attributes->add($match);
    
    // Use callable and maybe a function?
    $controller = $this->container[$match['_controller_id']];
    
    $actionName = isset($match['_action_name']) ? $match['_action_name'] : 'mainAction';
    
    $response1 = $controller->$actionName($request);
    
    // Transform response
    $responseEvent = new  KernelResponseEvent($request,$response1);
    $dispatcher->dispatch(KernelResponseEvent::name,$responseEvent);
    $response2 = $responseEvent->getResponse();
    
    // Clean up
    $this->container['request_stack']->pop($request);
    
    return $response2;
  }
}
$app = new App();

$request = Request::createFromGlobals();
$response = $app->dispatch($request);
$response->send();

/*
$request1  = Request::create('/referees/42','OPTIONS',array('name' => 'Fabien'));
$response1 = $app->dispatch($request1);
if (is_object($response1)); $response1->send(); */
/*
$request2 = Request::create('/referees','GET',array('name' => 'Fabien'));
$response2 = $app->dispatch($request2);
if (is_object($response2)) $response2->send();
*/