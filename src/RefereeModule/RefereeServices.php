<?php

namespace Cerad\Module\RefereeModule;

class RefereeServices
{
  public function __construct($container)
  {
    $container->set('referee_repository',function($c)
    {
      return new \Cerad\Module\RefereeModule\RefereeRepository($c->get('database_connection'));
    });
    $container->set('referee_controller',function($c)
    {
      return new \Cerad\Module\RefereeModule\RefereeController($c->get('referee_repository'));
    });
  }
}