<?php

namespace Cerad\Module\AppModule;

// Copy to AppParameters
class AppParameters
{
  public function __construct($container)
  {
    $container->set('secret','someSecret');
    
    $container->set('db_url','mysql://USER:PASSWORD@localhost/persons');
  }
}