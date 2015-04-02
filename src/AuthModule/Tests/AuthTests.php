<?php

namespace Cerad\Module\AuthModule\Tests;

require __DIR__  . '/../../../vendor/autoload.php';

use Cerad\Module\KernelModule\KernelContainer;

use Cerad\Module\AuthModule\AuthServices;

class AuthTests extends  \PHPUnit_Framework_TestCase
{  
  protected $container;
  
  public static function setUpBeforeClass()
  { 
    shell_exec(sprintf('mysql --login-path=tests < %s',__DIR__ . '/users_schema.sql'));
  }
  public function setUp()
  {
    $this->container = $container = new KernelContainer();
    
    $container->set('secret','someSecret');
    
    $container->set('cerad_user_master_password','testing');
  
    $container->set('db_url_users','mysql://test:test123@localhost/tests');

    new AuthServices($container);
  }
  public function test1()
  {
  }
}