<?php

namespace Cerad\Component\Http;

require __DIR__  . '/../../vendor/autoload.php';

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamableInterface;

abstract class Message implements MessageInterface
{  
  protected $version  = 'HTTP/1.1';
  protected $content;
  
  public $headers;
  
  public function getProtocolVersion() { return $this->version; }
  public function withProtocolVersion($version) { throw new \BadMethodCallException(); }
  
  /* =============================================
   * PSR-7 Header stuff
   */
  public function getHeader($name) { return $this->headers->get($name); }
  public function hasHeader($name) { return $this->headers->has($name); }
  
  public function getHeaderLines($name) { return $this->headers->getLines($name); }
   
  public function getHeaders() 
  { 
    // TDOD: convert to arrays
    throw \BadMethodCallException();
    //return $this->headers->get(); 
  }
  public function withHeader     ($name, $value) { throw new \BadMethodCallException(); }
  public function withAddedHeader($name, $value) { throw new \BadMethodCallException(); }
  public function withoutHeader  ($name)         { throw new \BadMethodCallException(); }
  
  public function getBody() { return $this->content; }
  
  public function withBody(StreamableInterface $body) { throw new \BadMethodCallException(); }
}
