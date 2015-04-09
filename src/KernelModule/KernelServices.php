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
      return new \Symfony\Component\HttpFoundation\RequestStack();
    });
    /* =============================================
     * $this->context->getHost()
     * $this->context->getMethod()
     * $this->context->getScheme()
     */
    $container->set('request_context',function($c)
    {
      $context = new \Symfony\Component\Routing\RequestContext();
      $context->fromRequest($c->get('request_stack')->getMasterRequest());
      return $context;
    });
    $container->set('route_matcher',function($c)
    {
      return new \Symfony\Component\Routing\Matcher\UrlMatcher(
        $c->get('routes'), 
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
      return new \Symfony\Component\EventDispatcher\EventDispatcher();
    });
    $container->set('kernel_cors_listener',function()
    {
      return new \Cerad\Module\KernelModule\EventListener\CORSListener();
    },'kernel_event_listener');
  }

}