<?php

namespace Cerad\Module\AppModule;

class AppServices
{
  public function __construct($container)
  {
    $container->set('database_connection_ng2014',function($c)
    {
      $config = new \Doctrine\DBAL\Configuration();
      
      $connectionParams = 
      [
        'url' => $c->get('db_url_ng2014'),
        'driverOptions' => [\PDO::ATTR_EMULATE_PREPARES => false], // For limits
      ];
      $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
      
      return $conn;
    });
  }
}