<?php
namespace Cerad\Component\Http;

use Cerad\Component\Http\Headers;

require __DIR__  . '/../../vendor/autoload.php';

class HeadersTest extends \PHPUnit_Framework_TestCase
{
  const acceptImage = 'image/webp';
  const acceptJson  = 'application/json';
  
  private $headersData =
  [
    'Accept'        => ['text/html','application/xml;q=0.9',self::acceptImage,'*/*;q=0.8'],
    'Cache-Control' => 'max-age=0',
    'Host'          => 'local.ang2.zayso.org:8002',
    'Authorization' => 'TOKEN',
  ];
  private $cors =
  [
    'Access-Control-Allow-Headers' => 'Content-Type, Authorization',
    'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, PATCH, OPTIONS',
    'Access-Control-Allow-Origin'  => 'zayso.org'     
  ];
  private $serverData =
  [
    'SERVER_PROTOCOL' => 'HTTP/1.1',
    'SERVER_NAME'     => 'local.ang2.zayso.org',
    'SERVER_PORT'     => '8002',
    'REQUEST_URI'     => '/xxx/123?role=xxx',
    'REQUEST_METHOD'  => 'GET',
    'PATH_INFO'       => '/xxx/123',
    'QUERY_STRING'    => 'role=xxx',
    'HTTP_HOST'       => 'local.ang2.zayso.org:8002',
    'HTTP_ACCEPT'     => 'text/html,application/xml;q=0.9,image/webp,*/*;q=0.8',
    'HTTP_USER_AGENT' => 'Mozilla/5.0 Chrome/41.0.2272.101 Safari/537.36',
  ];
  public function test1()
  {
    $headers = new Headers($this->headersData);
    
    $this->assertEquals('TOKEN',$headers->get('Authorization'));
    
    $accept = $headers->get('Accept');
    $this->assertTrue(strpos($accept,self::acceptImage . ',') !== false);
    
    $acceptLines = $headers->getLines('Accept');
    $this->assertEquals(self::acceptImage,$acceptLines[2]);
    
    $headers->setLine('Accept',self::acceptJson);
    $accept2 = $headers->get('Accept');
    $this->assertTrue(strpos($accept2,self::acceptJson)  !== false);
    $this->assertTrue(strpos($accept2,self::acceptImage) !== false);
    
    $this->assertTrue($headers->hasValue('Accept',self::acceptJson ));
    $this->assertTrue($headers->hasValue('Accept',self::acceptImage));
    
    $headers->set('Cache-Control','max-age=60');
    $this->assertEquals('max-age=60',$headers->get('Cache-Control'));
    
    $this->assertTrue ($headers->has('Host'));
    $this->assertFalse($headers->has('host')); // Case sensitive
    
    $headers->set($this->cors);
    $this->assertEquals('zayso.org',$headers->get('Access-Control-Allow-Origin'));
  }
  public function testServer()
  {
    $headers = new Headers($this->serverData,true);
    $this->assertTrue($headers->has('User-Agent'));
    
    $accept = $headers->get('Accept');
    $this->assertTrue(strpos($accept,self::acceptImage . ',') !== false);
    
    $acceptLines = $headers->getLines('Accept');
    $this->assertEquals(self::acceptImage,$acceptLines[2]);
    
    $this->assertEquals('local.ang2.zayso.org:8002',$headers->get('Host'));
  }
}