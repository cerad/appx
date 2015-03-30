<?php

namespace Cerad\Module\KernelModule;

use Symfony\Component\HttpFoundation\Request;
//  Symfony\Component\HttpFoundation\Response;

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
  private function boot()
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
    $container = $this->container;
    
    $routes = $container->get    ('routes');
    $tags   = $container->getTags('routes');
    foreach($tags as $tag)
    {
      $routes->addCollection($container->get($tag['service_id']));
    }
  }
  protected function registerEventListeners()
  {
    $container = $this->container;
    
    $dispatcher = $container->get    ('event_dispatcher');
    $tags       = $container->getTags('kernel_event_listener');
    foreach($tags as $tag)
    {
      $listener = $container->get($tag['service_id']);
      $dispatcher->addSubscriber($listener);
    }
  }
  // TODO add exception wrapper
  public function handle(Request $request, $requestType = self::REQUEST_TYPE_MASTER, $catch = true)
  {
    // Boot on first request
    if (!$this->booted) $this->boot();
    
    // Add request
    $requestStack = $this->container->get('request_stack');
    $requestStack->push($request);
    
    // Match the route
    $matcher = $this->container->get('route_matcher');
    $match   = $matcher->matchRequest($request);
    $request->attributes->add($match);
    
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
