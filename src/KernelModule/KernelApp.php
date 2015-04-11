<?php

namespace Cerad\Module\KernelModule;

use Cerad\Component\HttpMessage\Request;
use Cerad\Component\HttpMessage\ResponseJson;

use Cerad\Module\KernelModule\KernelContainer;
use Cerad\Module\KernelModule\Event\KernelRequestEvent;
use Cerad\Module\KernelModule\Event\KernelResponseEvent;

class KernelApp
{
  const REQUEST_TYPE_MASTER = 1;
  const REQUEST_TYPE_SUB    = 2;
    
  protected $container;
  protected $environment;
  protected $debug;
  protected $booted = false;
  
  public function __construct($environment = 'prod', $debug = false)
  {
    $this->environment = $environment;
    $this->debug = (bool)$debug;
  }
  public function getContainer() { return $this->container; }
  
  // Make this public for testing
  public function boot()
  {
    if ($this->booted) return;
    
    $this->container = new KernelContainer();
    
    $this->registerServices      ();
    $this->registerRoutes        ();
    $this->registerEventListeners();
    
    $this->booted = true;
  }
  protected function registerServices()
  {
    new KernelServices($this->container);
  }
  protected function registerRoutes()
  {
    return;
    
    $container = $this->container;
    
    $routes = $container->get    ('routes');
    $tags   = $container->getTags('routes');
    foreach($tags as $tag)
    {
    //echo sprintf("\nRoutes: %s\n",$tag['service_id']);
      $routes->addCollection($container->get($tag['service_id']));
    }
  }
  protected function registerEventListeners()
  {
    return;
    
    $container = $this->container;
    
    $dispatcher = $container->get    ('event_dispatcher');
    $tags       = $container->getTags('kernel_event_listener');
    foreach($tags as $tag)
    {
      echo sprintf("Tag %s\n",$tag['service_id']);
      $listener = $container->get($tag['service_id']);
      $dispatcher->addSubscriber($listener);
    }
  }
  // TODO add exception wrapper
  public function handle(Request $request, $requestType = self::REQUEST_TYPE_MASTER)
  {
    try
    {
      return $this->handleRaw($request,$requestType);
    } 
    catch (\Exception $ex) // TODO: Implement Exception listener
    {
      $class = get_class($ex);
      $message = $ex->getMessage();
      switch($class)
      {
        case 'Symfony\Component\Security\Core\Exception\AccessDeniedException':
          $code = 401;
          break;
        default:
          $code = 401;
      }
      $response = new ResponseJson(['error' => $message],$code);
      
      // Need this so auth headers get set
      $dispatcher = $this->container->get('event_dispatcher');
      return $this->dispatchResponse($dispatcher,$request,$response);
    }
  }
  public function handleRaw(Request $request, $requestType)
  {
    // Boot on first request
    if (!$this->booted) $this->boot();
 
    // Add request
    $requestStack = $this->container->get('request_stack');
    $requestStack->push($request);

    // Match the route
    $matcher = $this->container->get('route_matcher');
    $match   = $matcher->match($request->getRoutePath());
    if (!$match) die ('No match for ' . $request->getRoutePath());
    
    $request->attributes->set($match);
    
    // Dispatcher
    $dispatcher = $this->container->get('event_dispatcher');
    
     // Dispatch request event
    $response = $this->dispatchRequest($dispatcher,$request,$requestType);
    if ($response)
    {
    //$responsex = $this->dispatchResponse($dispatcher,$request,$response);
      $requestStack->pop($request);
      return $response;
    }
    // Process factories
    
    // Try action function
    $action = $request->attributes->get('_action');
    if ($action)
    {
      $response = $action($request);
    }
    // Try view function
    $view = $request->attributes->get('_view');
    if ($view)
    {
      $response = $view($request,$response);
    }
    if (!$response)
    {
      die('no response');
    }
    // Dispatch response event
    $responsex = $this->dispatchResponse($dispatcher,$request,$response);
    
    // Clean up
    $requestStack->pop($request);
    
    return $responsex;
  }
  protected function dispatchRequest($dispatcher,$request,$requestType)
  {
    $requestEvent = new KernelRequestEvent($request,$requestType);
    $dispatcher->dispatch(KernelRequestEvent::name,$requestEvent);
    return $requestEvent->getResponse();
  }
  protected function dispatchResponse($dispatcher,$request,$response)
  {
    $responseEvent = new  KernelResponseEvent($request,$response);
    $dispatcher->dispatch(KernelResponseEvent::name,$responseEvent);
    return $responseEvent->getResponse();
  }
}
