<?php

namespace Cerad\Component\HttpMessage;

use Cerad\Component\HttpMessage\Message;
use Cerad\Component\HttpMessage\Response;
use Cerad\Component\HttpMessage\TextStream;

require __DIR__  . '/../../vendor/autoload.php';

class RequestTestTest extends \PHPUnit_Framework_TestCase
{
  public function testStatusCode()
  {
    $response1 = new Response('',401);
    
    $response2 = $response1->withStatus(200,'OK');
    
    $this->assertEquals(401,            $response1->getStatusCode());
    $this->assertEquals('Unauthorized', $response1->getReasonPhrase());
    
    $this->assertEquals(200, $response2->getStatusCode());
    $this->assertEquals('OK',$response2->getReasonPhrase());
    
    $response3 = $response2->withStatus(201);
    $this->assertEquals('Created',$response3->getReasonPhrase());
  }
  public function testMessageProtocolVersion()
  {
    $message1 = new Message();
    
    $message2 = $message1->withProtocolVersion('1.0');
    
    $this->assertEquals('1.1',$message1->getProtocolVersion());
    $this->assertEquals('1.0',$message2->getProtocolVersion());
  }
  public function testMessageHeaders()
  {
    $hdr1 = ['value1','value2'];
    $hdr2 = ['value3','value4'];
    $hdrs = ['hdr1' => $hdr1,'Hdr2' => $hdr2];
    $message1 = new Message(null,$hdrs);
    
    $message1Hdrs = $message1->getHeaders();
    
    $this->assertEquals(2, count($message1Hdrs));
    $this->assertEquals('value2',$message1Hdrs['hdr1'][1]);
    
    $this->assertTrue ($message1->hasHeader('hdr1'));
    $this->assertTrue ($message1->hasHeader('Hdr1'));
    $this->assertFalse($message1->hasHeader('hdrx'));
    
    $this->assertEquals('value1,value2',$message1->getHeader('hdr1'));
    $this->assertEquals('value1,value2',$message1->getHeader('Hdr1'));
    $this->assertNull  ($message1->getHeader('hdrx'));
    
    $hdr2Lines = $message1->getHeaderLines('hdr2');
    $this->assertEquals(2, count($hdr2Lines));
    
    $hdr3Lines = $message1->getHeaderLines('hdr3');
    $this->assertTrue(is_array($hdr3Lines));
    $this->assertEquals(0, count($hdr3Lines));
    
    $hdr2x = ['value8','value9'];
    $message2 = $message1->withHeader('HDR2',$hdr2x);
    $message2Headers = $message2->getHeaders();
    $this->assertEquals('value9',$message2Headers['HDR2'][1]);
    
    $message3 = $message2->withAddedHeader('hdr2','value10');
    $message3Headers = $message3->getHeaders();
    $this->assertEquals('value10',$message3Headers['HDR2'][2]);
    
    $message4 = $message3->withoutHeader('hDr1');
    $this->assertFalse($message4->hasHeader('hdr1'));
    $this->assertTrue ($message4->hasHeader('hdr2'));
    
  }
  public function testMessageBody()
  {
    $message1 = new Message('Some Content 1');
    
    $message2 = $message1->withBody(new TextStream('Some Content 2'));
    
    $content1 = $message1->getBody()->getContents();
    $content2 = $message2->getBody()->getContents();
    
    $this->assertEquals('Some Content 1',$content1);
    $this->assertEquals('Some Content 2',$content2);
  }
}