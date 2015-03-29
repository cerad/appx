<?php

namespace Cerad\Module\AppModule;

class AppKernel extends \Cerad\Module\KernelModule\KernelApp
{
  protected function registerServices($container)
  {
    parent::registerServices($container);

    new AppParameters($container);
    new AppServices  ($container);
    
    new \Cerad\Module\RefereeModule\RefereeServices($container);
  }
  protected function registerRoutes($container,$routes)
  {
    parent::registerRoutes($container,$routes);
    
    // Keep open the possibility of using the constructor and or tags
    $refereeRoutes = new \Cerad\Module\RefereeModule\RefereeRoutes;
    $refereeRoutes->register($container,$routes);
  }
  protected function registerEventListeners($dispatcher)
  {
    parent::registerEventListeners($dispatcher);
    
    $corsListener = $this->container['kernel_cors_listener'];
    
    $dispatcher->addSubscriber($corsListener);
  }
  /* ================================
   * Other tags
   * registerFormTypes
   */
}