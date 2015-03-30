<?php

namespace Cerad\Module\KernelModule;

use Cerad\Module\KernelModule\KernelContainer as Container;

class KernelContainerTest extends \PHPUnit_Framework_TestCase
{
  public static function setUpBeforeClass()
  {
    require __DIR__  . '/../../vendor/autoload.php';
  }

  public function test1()
  {
    $container = new Container();
    
    $container->set('scaler',42);
    $this->assertEquals(42, $container->get('scaler'));
    
  }
  public function test2()
  {
    $container = new Container();
    
    $func1 = function($c)
    {
      return 42;
    };
    $container->set('func1',$func1);
    $this->assertEquals(42, $container->get('func1'));
  }
  public function test3()
  {
    $container = new Container();
    $container->set('i42',42);
    
    $func1 = function($c)
    {
      return $c->get('i42');
    };
    $container->set('func1',$func1);
    $this->assertEquals(42, $container->get('func1'));
  }
  public function testClass()
  {
    $container = new Container();
    
    $container->set('i42',42);
    
    $func = function($c)
    {
      $item = new \Cerad\Module\KernelModule\CeradKernelTestClass($c->get('i42'));
      return $item;
    };
    $container->set('func',$func);
    $this->assertEquals(42, $container->get('func')->get());
  }
  public function testClassUse()
  {
    $container = new Container();
    
    $container->set('i42',42);
    
    $i = 21;
    
    $func = function($c) use($i)
    {
      $item = new \Cerad\Module\KernelModule\CeradKernelTestClass($c->get('i42'));
      $item->set($i);
      return $item;
    };
    $container->set('func',$func);
    $this->assertEquals(21, $container->get('func')->get());
  }
  public function testTags()
  {
    $container = new Container();
    $container->set('container_tags',[]);
    
    $func = function($c)
    {
      $item = new \Cerad\Module\KernelModule\CeradKernelTestClass(42);
      return $item;
    };
    $container->set('func',$func,['name' => 'funcs', 'param' => 'p42']);
    
    $funcs = $container->getTags('funcs');
    
    $this->assertEquals(1,count($funcs));
    
    $tag = $funcs[0];
    $this->assertEquals('p42',$tag['param']);
  }
}
class CeradKernelTestClass
{
  private $i;
  
  public function __construct($i)
  {
    $this->i = $i;
  }
  public function get() { return $this->i; }
  public function set($i) { $this->i = $i; }
}