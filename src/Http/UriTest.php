<?php

namespace Cerad\Component\Http;

require __DIR__  . '/../../vendor/autoload.php';

use Cerad\Component\Http\Uri;
use Cerad\Component\Http\HttpTestBase;

class UriTest extends HttpTestBase
{  
  public function testUriString()
  {
    $uri = new Uri($this->urlString);
    
    $this->assertEquals('https',$uri->getScheme());
    
    $this->assertEquals('api.zayso.org',$uri->getHost());
    $this->assertEquals('8080',$uri->getPort());
    
    $this->assertEquals('/referees',$uri->getPath());
    
    $this->assertEquals('user:pass',$uri->getUserInfo());
    
    $this->assertEquals('user:pass@api.zayso.org:8080',$uri->getAuthority());
    
    $this->assertEquals('project=ng2016&Title=NG 2016',$uri->getQuery());
    
    $queryParams = $uri->getQueryParams();
    
    $this->assertEquals('NG 2016',$queryParams['title']);
    
    $this->assertEquals('42',$uri->getFragment());
  }
  public function testServer()
  {
    $uri = new Uri($this->serverData);
    
    $this->assertEquals('local.ang2.zayso.org',$uri->getHost());
    $this->assertEquals('8002',$uri->getPort());
    $this->assertEquals('local.ang2.zayso.org:8002',$uri->getAuthority());
    
    $this->assertEquals('/xxx/123',$uri->getPath());
    $this->assertEquals('role=xxx',$uri->getQuery());
    
    $this->assertEquals('http',$uri->getScheme());
  }
}