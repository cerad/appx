<?php

namespace Cerad\Component\JWT;

require __DIR__  . '/../../vendor/autoload.php';

class JWTTest extends \PHPUnit_Framework_TestCase
{
  public function testEncodeDecode()
  {
    $jwt = new JWT('my_key');
    
    $msg = $jwt->encode('abc');
    
    $this->assertEquals($jwt->decode($msg), 'abc');
  }
  public function testValidToken()
  {
    $jwt = new JWT('my_key');
    $payload = array(
      "message" => "abc",
       "exp" => time() + 20); // time in the future
    $encoded = $jwt->encode($payload);
    $decoded = $jwt->decode($encoded);
    $this->assertEquals($decoded['message'], 'abc');
  }
    public function testInvalidToken()
    {
      $jwt = new JWT('my_key');
      $payload = array(
        "message" => "abc",
        "exp" => time() + 20); // time in the future
      $encoded = $jwt->encode($payload);
      $this->setExpectedException('UnexpectedValueException');
      $decoded = $jwt->decode($encoded, 'my_key2');
    }
}