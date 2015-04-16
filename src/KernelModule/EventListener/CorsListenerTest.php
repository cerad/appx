<?php

namespace Cerad\Module\KernelModule\EventListener;

use Cerad\Module\KernelModule\EventListener\CorsListener;

use Cerad\Module\KernelModule\Event\KernelRequestEvent;
use Cerad\Module\KernelModule\Event\KernelResponseEvent;

use Cerad\Component\HttpMessage\Request;
use Cerad\Component\HttpMessage\Response;
use Cerad\Component\HttpMessage\ResponsePreflight;

class CorsListenerTest extends \PHPUnit_Framework_TestCase
{
  public function testPreflight()
  {
    $listener = new CorsListener();
    $headers = 
    [
      'Origin' => 'localhost',
      'Access-Control-Request-Method' => 'GET',
    ];
    
    $request = new Request('OPTIONS /resource',$headers);
    
    $event = new KernelRequestEvent($request);
    
    $listener->onKernelRequest($event);
    
    $response = $event->getResponse();

    $this->assertTrue($response instanceof ResponsePreflight);
    
    $this->assertEquals('localhost',$response->getHeaderLine('Access-Control-Allow-Origin'));
  }
  public function testCors()
  {
    $listener = new CorsListener();
    $headers = 
    [
      'Origin' => 'localhost',
    ];
    $request  = new Request('GET /resource',$headers);
    $response = new Response();
    
    $event = new KernelResponseEvent($request,$response);
    
    $listener->onKernelResponse($event);
    
    $responsex = $event->getResponse();
    
    $this->assertFalse($response->hasHeader('Access-Control-Allow-Origin'));
    
    $this->assertEquals('localhost',$responsex->getHeaderLine('Access-Control-Allow-Origin'));
    
  }
}