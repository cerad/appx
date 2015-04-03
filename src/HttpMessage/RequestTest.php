<?php
namespace Cerad\Component\HttpMessage;

use Cerad\Component\HttpMessage\Uri;
use Cerad\Component\HttpMessage\RequestBase as Request;

require __DIR__  . '/../../vendor/autoload.php';

class UriTest extends \PHPUnit_Framework_TestCase
{
  protected $uriString = 'https://user:pass@api.zayso.org:8080/referees?project=ng2016&title=NG+2016#42';
  protected $acceptString = 'application/json, text/plain, */*';
  
  public function testHeaders()
  {
    $content = 'Some Content';
    
    $accept = array_map('trim', explode(',', $this->acceptString));
    $hdrs = 
    [
      'Accept'     => $accept,
      'User-Agent' => ['Chrome Mozilla']
    ];
    $request1 = new Request($content,$hdrs);
    $headers1 = $request1->getHeaders();
    $this->assertEquals(2,count($headers1));
    
    $uri2 = new Uri($this->uriString);
    $request2 = new Request($content,$hdrs,$uri2);
    $headers2 = $request2->getHeaders();
    $this->assertEquals('api.zayso.org',$headers2['Host'][0]);
    
    $hostHeader2 = $request2->getHeader('Host');
    $this->assertEquals('api.zayso.org',$hostHeader2);
    
    $hostHeaderLines2 = $request2->getHeaderLines('Host');
    $this->assertEquals('api.zayso.org',$hostHeaderLines2[0]);
   
    $acceptHeader2 = $request2->getHeader('accept');
    $this->assertEquals($this->acceptString,$acceptHeader2);
    
    $acceptHeaderLines2 = $request2->getHeaderLines('accept');
    $this->assertEquals('text/plain',$acceptHeaderLines2[1]);
    
    $request3 = $request2->withRequestTarget('http://localhost:8001/referees');
    $this->assertEquals('http://localhost:8001/referees',$request3->getRequestTarget());
  }
}
