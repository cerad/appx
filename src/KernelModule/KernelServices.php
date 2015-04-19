<?php

namespace Cerad\Module\KernelModule;

class KernelServices
{
  public function __construct($container)
  {
    // Me
    $container->set('kernel',$this);
    
    // Routes
    $container->set('routes',function($c)
    {
      return new \Symfony\Component\Routing\RouteCollection();
    });
    // Request/Routes
    $container->set('request_stack',function($c)
    {
      return new \Cerad\Component\HttpMessage\RequestStack();
    });
    /* =============================================
     * $this->context->getHost()
     * $this->context->getMethod()
     * $this->context->getScheme()
     */
    $container->set('request_context',function($c)
    {
      $request = $c->get('request_stack')->getMasterRequest();
      $context = [];
      $context['method'] = $request->getMethod();      
      return $context;
    });
    $container->set('route_matcher',function($c)
    {
      $routes = [];
      $tags = $c->getTags('routes');
      foreach($tags as $tag)
      {
        $serviceId = $tag['service_id'];
        $service   = $c->get($serviceId);
        $routes[$serviceId] = $service;
      }
      return new \Cerad\Component\HttpRouting\UrlMatcher
      (
        $routes,
        $c->get('request_context')
      );
    });
    $container->set('route_generator', function($c)
    {
      return new \Symfony\Component\Routing\Generator\UrlGenerator(
        $c->get('routes'), 
        $c->get('request_context')
      );
    });
    $container->set('database_connection',function($c)
    {
      $config = new \Doctrine\DBAL\Configuration();
      
      $connectionParams = 
      [
        'url' => $c->get('db_url'),
        'driverOptions' => [\PDO::ATTR_EMULATE_PREPARES => false], // For limits
      ];
      $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
      
      return $conn;
    });
    $container->set('event_dispatcher',function($c)
    {
      $dispatcher = new \Symfony\Component\EventDispatcher\EventDispatcher();
      $tags       = $c->getTags('event_listener');
      foreach($tags as $tag)
      {
        $listener = $c->get($tag['service_id']);
        $dispatcher->addSubscriber($listener);
      }
      return $dispatcher;
    });
    $container->set('kernel_cors_listener',function()
    {
      return new \Cerad\Module\KernelModule\EventListener\CorsListener();
    },'event_listener');
  }

}