<?php

namespace Cerad\Module\KernelModule;

class KernelServices
{
  public function __construct($container)
  {
    // Me
    $container['kernel'] = $this;
    
    // Routes
    $container['routes'] = function($c)
    {
      return new \Symfony\Component\Routing\RouteCollection();
    };
    // Request/Routes
    $container['request_stack'] = function($c)
    {
      return new \Symfony\Component\HttpFoundation\RequestStack();
    };
    $container['request_context'] = function($c)
    {
      $context = new \Symfony\Component\Routing\RequestContext();
      $context->fromRequest($c['request_stack']->getMasterRequest());
      return $context;
    };
    $container['route_matcher'] = function($c)
    {
      return new \Symfony\Component\Routing\Matcher\UrlMatcher($c['routes'], $c['request_context']);
    };
    $container['route_generator'] = function($c)
    {
      return new \Symfony\Component\Routing\Generator\UrlGenerator($c['routes'], $c['request_context']);
    };
    $container['database_connection'] = function($c)
    {
      $config = new \Doctrine\DBAL\Configuration();
      
      $connectionParams = array(
        'url' => $c['db_url'],
        // Need for limits
        'driverOptions' => [\PDO::ATTR_EMULATE_PREPARES => false],
      );
      
      $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
      
      return $conn;
    };
    $container['event_dispatcher'] = function($c)
    {
      return new \Symfony\Component\EventDispatcher\EventDispatcher();
    };
    $container['container_tags'] = [];
    
    $container['kernel_cors_listener'] = function()
    {
      return new \Cerad\Module\KernelModule\EventListener\CORSListener();
    };
  }

}