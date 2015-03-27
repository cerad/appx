<?php

namespace Cerad\Module\AppModule;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

// Private parameters, do not check in
class AppParameters implements ServiceProviderInterface
{
  public function register(Container $container)
  {
    $container['secret'] = 'someSecret';
    
    $container['db_url'] = 'mysql://USER:PASSWORD@localhost/persons';
  }
}