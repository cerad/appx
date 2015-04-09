<?php

namespace Cerad\Component\Http;

require __DIR__  . '/../../vendor/autoload.php';

use Cerad\Component\Http\Request;
use Cerad\Component\Http\HttpTestBase;

class UriTest extends HttpTestBase
{  
  public function test1UriString()
  {
    $request = new Request($this->urlString);
    
    $this->assertEquals('/referees',$request->getPath());
  }
  public function testRequestLine()
  {
    $requestLine = 'PUT /referees/1 HTTP/1.0';
    $request = new Request($requestLine);
    
    $this->assertEquals('PUT',     $request->getMethod());
    $this->assertEquals('HTTP/1.0',$request->getProtocolVersion());
    
    $this->assertEquals('/referees/1',$request->getPath());
  }
  public function testRequestServer()
  {
    $request = new Request($this->serverData);
    
    $this->assertEquals('OPTIONS',$request->getMethod());
    
    $this->assertEquals('/xxx/123',$request->getPath());
    
    $this->assertEquals('local.ang2.zayso.org:8002',$request->getAuthority());
    $this->assertEquals('local.ang2.zayso.org:8002',$request->headers->get('Host'));
    
    $accept = $request->getHeader('Accept');
    $this->assertTrue(strpos($accept,self::acceptImage) !== false);
    
    $queryParams = $request->getQueryParams();
    
    $this->assertEquals('xxx',$queryParams['role']);

  }
}