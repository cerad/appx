<?php

namespace Cerad\Module\AppModule;

// Copy to AppParameters
class AppParameters
{
  public function __construct($container)
  {
    $container['secret'] = 'someSecret';
    
    $container['db_url'] = 'mysql://USER:PASSWORD@localhost/persons';
  }
}