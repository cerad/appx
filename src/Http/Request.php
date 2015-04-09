<?php

namespace Cerad\Component\Http;

require __DIR__  . '/../../vendor/autoload.php';

use Cerad\Component\Http\Uri;
use Cerad\Component\Http\Headers;
use Cerad\Component\Http\Message;

use Psr\Http\Message\UriInterface;
use Psr\Http\Message\ServerRequestInterface;

class Request extends Message implements ServerRequestInterface
{  
  protected $uri;
  
  protected $method = 'GET';
  
  /* =========================================
   * Request could be a uri string with no headers
   * Request could be a server array which has headers
   */
  public function __construct($request = null,$headers = null)
  {
    if (is_array($request))
    {
      $this->uri = new Uri($request);
      
      if (isset($request['SERVER_NAME'])) 
      {
        $this->headers = new Headers($request);
        $this->headers->set($headers);
      }
      else
      {
        $this->headers = new Headers($headers);
      }
      if (isset($request['SERVER_PROTOCOL']))
      {
        $this->version = strtoupper($request['SERVER_PROTOCOL']);
      }
      if (isset($request['REQUEST_METHOD']))
      {
        $this->method = strtoupper($request['REQUEST_METHOD']);
      }      
      return;
    }
    if (is_string($request))
    {
      $parts = explode(' ',$request); // No real need to trim
      $method     = null;
      $requestUri = null;
      $version    = null;
      switch(count($parts))
      {
        case 0:
          return;
        case 1:
          $requestUri = $parts[0];
          break;
        case 2:
          $method     = $parts[0];
          $requestUri = $parts[1];
          break;
        default:
          $method     = $parts[0];
          $requestUri = $parts[1];
          $version    = $parts[2];
          break;
      }
      if ($method)  $this->method  = strtoupper($method);
      if ($version) $this->version = strtoupper($version);
      
      $this->uri     = new Uri    ($requestUri);
      $this->headers = new Headers($headers);
      return;
    }
    return;
  }
  
  // Request access
  public function getMethod() { return $this->method; }
  
  // For PSR-7, uri access
  public function getUri () { return $this->uri; }
  
  public function getPath() { return $this->uri->getPath(); }
  
  /* ==================================================
   * Currently extract host from $_SERVER data and store in both
   * the uri and the headers.  So far they stay in sync.
   * Might need some special processing to eliminate conflicts.
   */
  public function getAuthority() 
  { 
    return $this->uri->getAuthority(); 
  }
  
  // Attribute stuff
  public function getAttributes()                          { throw \BadMethodCallException(); }
  public function getAttribute    ($name, $default = null) { throw \BadMethodCallException(); }
  public function withAttribute   ($name, $value)          { throw \BadMethodCallException(); }
  public function withoutAttribute($name)                  { throw \BadMethodCallException(); }
   
  /* ========================================================
   * PSR-7 Stuff
   */
  public function getRequestTarget()                { throw \BadMethodCallException(); }
  public function withRequestTarget($requestTarget) { throw \BadMethodCallException(); }
  public function withMethod($method)               { throw \BadMethodCallException(); }
  public function withUri(UriInterface $uri)        { throw \BadMethodCallException(); }
  
  public function getServerParams() { throw \BadMethodCallException(); }
  public function getCookieParams() { throw \BadMethodCallException(); }
  public function getQueryParams()  { return $this->uri->getQueryParams(); }
  public function getFileParams()   { throw \BadMethodCallException(); }
  public function getParsedBody()   { throw \BadMethodCallException(); }
  
  public function withCookieParams(array $cookies) { throw \BadMethodCallException(); }
  public function withQueryParams (array $query)   { throw \BadMethodCallException(); }
  public function withParsedBody  ($data)          { throw \BadMethodCallException(); }
}