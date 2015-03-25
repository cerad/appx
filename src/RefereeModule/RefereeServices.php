<?php

namespace Cerad\Module\RefereeModule;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class RefereeServices implements ServiceProviderInterface
{
  public function register(Container $container)
  {
    $container['referee_controller'] = function($c)
    {
      return new \Cerad\Module\RefereeModule\RefereeController();
    };
    
  }
}