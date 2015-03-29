<?php

namespace Cerad\Module\KernelModule;

use Pimple\Container;

use Symfony\Component\HttpFoundation\Request;
//  Symfony\Component\HttpFoundation\Response;

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
  private function boot()
  {
    if ($this->booted) return;
    
    $this->container = $container = new Container();
    
    $this->registerServices      ($container);
    $this->registerRoutes        ($container, $container['routes']);
    $this->registerEventListeners($container['event_dispatcher']);
    
    $this->booted = true;
  }
  protected function registerServices($container)
  {
    new KernelServices($container);
  }
  protected function registerRoutes($container,$routes)
  {
  }
  protected function registerEventListeners($dispatcher)
  {
  }
  // TODO add exception wrapper
  public function handle(Request $request, $requestType = self::REQUEST_TYPE_MASTER, $catch = true)
  {
    // Boot on first request
    if (!$this->booted) $this->boot();
    
    // Add request
    $requestStack = $this->container['request_stack'];
    $requestStack->push($request);
    
    // Dispatcher
    $dispatcher = $this->container['event_dispatcher'];
    
    // Match the route
    $matcher = $this->container['route_matcher'];
    $match   = $matcher->matchRequest($request);
    $request->attributes->add($match);
    
    // Dispatch request event
    $response = $this->dispatchRequest($dispatcher,$request,$requestType);
    if ($response)
    {
      $responsex = $this->dispatchResponse($dispatcher,$request,$response);
      $requestStack->pop($request);
      return $responsex;
    }
    
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
