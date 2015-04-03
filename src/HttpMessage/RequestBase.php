<?php

namespace Cerad\Component\HttpMessage;

//  Psr\Http\Message\UriInterface;
//  Psr\Http\Message\RequestInterface;
use Cerad\Component\HttpMessage\Message;

class RequestBase extends Message// implements RequestInterface
{
  protected $uri;
  
  protected $requestTarget; // GET request-target HTTP-Version http://localhost:8001/referees?
  
  public function __construct($content = '',$headers = [],$uri = null)
  {
    parent::__construct($content,$headers);
    
    $this->uri = $uri;
  }
  /* ======================================
   * More header fun
   * Host needs to be generated automatically
   */
  public function getHeaders()
  {
    $headers = parent::getHeaders();
    
    if (parent::hasHeader('Host')) return $headers;
    
    $host = $this->getHeader('Host');
    
    if ($host) $headers['Host'] = [$host];
    
    return $headers;
  }
  public function getHeader($name)
  {
    if (parent::hasHeader($name)) return parent::getHeader($name);
    
    $nameLower = strtolower($name);
    
    if ($nameLower !== 'host' || !$this->uri) return '';
    
    return $this->uri->getHost();
  }
  public function getHeaderLines($name)
  {
    if (parent::hasHeader($name)) return parent::getHeaderLines($name);
    
    $host = $this->getHeader('Host');
    
    return [$host];
  }
  /* ==========================================
   * More properties
   */
  protected function setRequestTarget($requestTarget) { $this->requestTarget = $requestTarget; }
  public    function getRequestTarget()
  {
    return $this->requestTarget === null ? '/' : $this->requestTarget;
  }
  public function withRequestTarget($requestTarget)
  {
    $request = clone $this;
    $request->setRequestTarget($requestTarget);
    return $request;
  }
}
