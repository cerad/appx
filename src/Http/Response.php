<?php
namespace Cerad\Component\Http;

use Psr\Http\Message\ResponseInterface;

use Cerad\Component\Http\Message;
use Cerad\Component\Http\Headers;

use Symfony\Component\HttpFoundation\Response as SymfonyResponse; // For status code stuff

class Response extends Message implements ResponseInterface
{
  protected $statusCode;
  protected $statusCodeReasonPhrase;
  
  protected $charset = 'UTF-8';
  
  public function __construct($content = '',$statusCode = 200,$headers = [])
  {
    if (!isset($headers['Content-Type']))
    {
      $headers['Content-Type'] = 'text/html; charset=' . $this->charset;
    }
    if (!isset($headers['Date'])) // S2
    {
      $date = new \DateTime(null, new \DateTimeZone('UTC'));
      $headers['Date'] = $date->format('D, d M Y H:i:s') . ' GMT';
    }
    if (!isset($headers['Cache-Control']))
    {
      $headers['Cache-Control'] = 'no-cache';
    }
    $this->headers = new Headers($headers);
    $this->content = $content;
    
    $reasonPhrase = 
      isset(SymfonyResponse::$statusTexts[$statusCode]) ?
            SymfonyResponse::$statusTexts[$statusCode] :
            null;
    
    $this->setStatusCode  ($statusCode);
    $this->setReasonPhrase($reasonPhrase);
  }
  
  /* =====================================================
   * Dump response to the client
   */
  public function send()
  {
    $this->sendHeaders();
    $this->sendContent();
  }
  protected function sendContent()
  {
    echo $this->content;
  }
  public function sendHeaders()
  {
    // headers have already been sent by the developer
    if (headers_sent()) { return $this; }

    // status is just another header
    header(sprintf('%s %s %s', $this->version, $this->statusCode, $this->statusCodeReasonPhrase), true, $this->statusCode);

    // headers
    foreach ($this->headers->get() as $name => $value) 
    {
      header($name . ': ' . $value, false, $this->statusCode);
    }
  }
  
  public function withStatus($statusCode,$reasonPhrase = null)
  {
    throw new \BadMethodCallException();
    
    if ($reasonPhrase === null)
    {
      $reasonPhrase = 
        isset(SymfonyResponse::$statusTexts[$statusCode]) ?
              SymfonyResponse::$statusTexts[$statusCode] :
              null;
    }
    $response = clone $this;
    
    $response->setStatusCode  ($statusCode);
    $response->setReasonPhrase($reasonPhrase);
    
    return $response;
  }
  public function getStatusCode()   { return $this->statusCode;   }
  public function getReasonPhrase() { return $this->statusCodeReasonPhrase; }
  
  protected function setStatusCode  ($code)         { $this->statusCode             = $code;         }
  protected function setReasonPhrase($reasonPhrase) { $this->statusCodeReasonPhrase = $reasonPhrase; }
}