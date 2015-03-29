<?php

namespace Cerad\Module\RefereeModule;

class RefereeServices
{
  public function __construct($container)
  {
    $container['referee_repository'] = function($c)
    {
      return new \Cerad\Module\RefereeModule\RefereeRepository($c['database_connection']);
    };
    $container['referee_controller'] = function($c)
    {
      return new \Cerad\Module\RefereeModule\RefereeController($c['referee_repository']);
    };
  }
}