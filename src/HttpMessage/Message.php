<?php
namespace Cerad\Component\HttpMessage;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamableInterface;

use Cerad\Component\HttpMessage\TextStream;

class Message implements MessageInterface
{
  protected $protocolVersion = '1.1';
  
  protected $headers    = []; // Original keys, value is always an array
  protected $headerKeys = []; // lowercase => original
  
  protected $body;
  
  public function __construct($content = '',$headers = [])
  {
    $body = ($content instanceof StreamableInterface) ? $content : new TextStream($content);
    $this->setBody($body);
    
    foreach($headers as $key => $value)
    {
      $this->setHeader($key,$value);
    }
  }
  /* ================================================
   * Protocol methods
   */
  public function withProtocolVersion($version)
  {
    $message = clone $this;
    
    $message->setProtocolVersion($version);
    
    return $message;
  }
  public    function getProtocolVersion()  { return $this->protocolVersion; }
  protected function setProtocolVersion($version) { $this->protocolVersion = $version; }
  
  /* ===================================================
   * Internel header methods
   */
  protected function setHeader($name,$value)
  {
    $nameLower = strtolower($name);
    $valueArray = is_array($value) ? $value : [$value];
    
    // Deal with a case change
    if (isset($this->headerKeys[$nameLower]))
    {
      if ($name !== $this->headerKeys[$nameLower])
      {
        // case change
        unset($this->headers[$this->headerKeys[$nameLower]]);
      }
    }
    $this->headers   [$name]      = $valueArray;
    $this->headerKeys[$nameLower] = $name;
  }
  protected function addHeader($name,$value)
  {
    $nameLower = strtolower($name);
    $valueArray = is_array($value) ? $value : [$value];
    
    if (isset($this->headerKeys[$nameLower]))
    {
      $header = $this->headers[$this->headerKeys[$nameLower]];
      $this->headers[$this->headerKeys[$nameLower]] = array_merge($header,$valueArray);
      return;
    }
    $this->setHeader($name,$value);
  }
  protected function removeHeader($name)
  {
    $nameLower = strtolower($name);
    
    if (isset($this->headerKeys[$nameLower]))
    {
      unset($this->headers   [$this->headerKeys[$nameLower]]);
      unset($this->headerKeys[$nameLower]);
    }
  }
  /* =========================================================
   * Public header methods
   */
  public function getHeaders() { return $this->headers; }
  
  public function hasHeader($name)
  {
    $nameLower = strtolower($name);
    
    return isset($this->headerKeys[$nameLower]) ? true : false;
  }
  public function getHeader($name)
  {
    $nameLower = strtolower($name);
    
    return 
      isset($this->headerKeys[$nameLower]) ?
      implode(',',$this->headers[$this->headerKeys[$nameLower]]) :
      null;
  }
  public function getHeaderLines($name)
  {
    $nameLower = strtolower($name);
    
    return 
      isset($this->headerKeys[$nameLower]) ?
      $this->headers[$this->headerKeys[$nameLower]] :
      [];
  }
  public function withHeader($name, $value)
  {
    $message = clone $this;
    
    $message->setHeader($name,$value);
    
    return $message;
  }
  public function withAddedHeader($name, $value)
  { 
    $message = clone $this;
    
    $message->addHeader($name,$value);
    
    return $message;
  }
  public function withoutHeader($name)
  { 
    $message = clone $this;
    
    $message->removeHeader($name);
    
    return $message;
  }
  /* ===========================================================
   * Body methods
   */
  public    function getBody()      { return $this->body; }
  protected function setBody($body) { $this->body = $body; }
  
  public function withBody(StreamableInterface $body)
  {
    $message = clone $this;
    $message->setBody($body);
    return $message;
  }
}