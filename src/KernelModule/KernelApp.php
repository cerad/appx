<?php

namespace Cerad\Module\KernelModule;

use Cerad\Component\HttpMessage\Request;
use Cerad\Component\HttpMessage\ResponseJson;

use Cerad\Component\DependencyInjection\Container;

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
    
    $this->container = $container = new Container();
    
    $this->registerServices      ($container);
    $this->registerRoutes        ($container);
    $this->registerEventListeners($container);
    
    $this->booted = true;
  }
  protected function registerServices($container)
  {
    new KernelServices($container);
  }
  protected function registerRoutes()
  {
    return;
  }    
  protected function registerEventListeners()
  {
    return;
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
    
    $request->setAttributes($match);
    
    // Dispatcher
    $dispatcher = $this->container->get('event_dispatcher');
    
     // Dispatch request event
    $response = $this->dispatchRequest($dispatcher,$request,$requestType);
    if ($response)
    {
      $responsex = $this->dispatchResponse($dispatcher,$request,$response);
      $requestStack->pop($request);
      return $responsex;
    }
    // Process factories
    
    // Try action function
    $action = $request->getAttribute('_action');
    if ($action)
    {
      $response = $action($request);
    }
    // Try view function
    $view = $request->getAttribute('_view');
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
