<?php

namespace Cerad\Module\AppModule;

class AppKernel extends \Cerad\Module\KernelModule\KernelApp
{
  protected function registerServices()
  {
    parent::registerServices();

    $container = $this->container;
    
    new AppParameters($container);
    new AppServices  ($container);
    
    new \Cerad\Module\RefereeModule\RefereeServices($container);
    new \Cerad\Module\RefereeModule\RefereeRoutes  ($container);
  }
}