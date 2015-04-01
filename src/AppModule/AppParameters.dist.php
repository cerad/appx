<?php

namespace Cerad\Module\AppModule;

// Copy to AppParameters
class AppParameters
{
  public function __construct($container)
  {
    $container->set('secret','someSecret');
    $container->set('cerad_user_master_password','somePassword');
    
    $container->set('db_url',       'mysql://USER:PASSWORD@localhost/persons');
    $container->set('db_url_ng2014','mysql://USER:PASSWORD@localhost/ng2014');
  }
}