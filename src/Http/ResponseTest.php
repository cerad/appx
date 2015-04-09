<?php

namespace Cerad\Component\Http;

use Cerad\Component\Http\Message;
use Cerad\Component\Http\Response;
use Cerad\Component\Http\TextStream;

require __DIR__  . '/../../vendor/autoload.php';

class ResponseTest extends \PHPUnit_Framework_TestCase
{
  public function testStatusCode()
  {
    $response1 = new Response('',401);
    
    $this->assertEquals(401,            $response1->getStatusCode());
    $this->assertEquals('Unauthorized', $response1->getReasonPhrase());
    
    $this->assertEquals('text/html; charset=UTF-8', $response1->getHeader('Content-Type'));
    
    return;
    
    $response2 = $response1->withStatus(200,'OK');
    
    $this->assertEquals(401,            $response1->getStatusCode());
    $this->assertEquals('Unauthorized', $response1->getReasonPhrase());
    
    $this->assertEquals(200, $response2->getStatusCode());
    $this->assertEquals('OK',$response2->getReasonPhrase());
    
  //$response3 = $response2->withStatus(201);
  //$this->assertEquals('Created',$response3->getReasonPhrase());
  }
}