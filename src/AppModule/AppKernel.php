<?php

namespace Cerad\Module\AppModule;

use Cerad\Module\AuthModule\AuthRoutes;
use Cerad\Module\AuthModule\AuthServices;

use Cerad\Module\RefereeModule\RefereeRoutes;
use Cerad\Module\RefereeModule\RefereeServices;

class AppKernel extends \Cerad\Module\KernelModule\KernelApp
{
  protected function registerServices($container)
  {
    new AppParameters($container);
    
    parent::registerServices($container);
    
    new AuthServices($container);
    new AuthRoutes  ($container);
    
    new RefereeServices($container);
    new RefereeRoutes  ($container);
    
    new AppServices($container);
  }
}