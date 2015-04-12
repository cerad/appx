<?php

require '../vendor/autoload.php';

class ClosureTest extends \PHPUnit_Framework_TestCase
{
  /**
    * @expectedException PHPUnit_Framework_Error
   * 
   * This all works as expected, 
   * phpunit converts the undefined index into a PHPUnit_Framework_Error exception
   * Remove the @ an we see the undefined index
   * 
   * So something else is going on with the appx closures
   */
  public function test1()
  {
    $f1 = function()
    {
      $data = [];
      
      $param = $data['xxx'];
      
      return 42;
    };
    $this->assertEquals(42,$f1());
  }
}
