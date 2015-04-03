<?php
namespace Cerad\Component\HttpMessage;

use Cerad\Component\HttpMessage\ResponseBase;

use Symfony\Component\HttpFoundation\Response as SymfonyResponse; // For status code stuff

class Response extends ResponseBase
{
  protected $charset = 'UTF-8';
  
  public function __construct($content = '', $statusCode = 200, $headers = [])
  {
    parent::__construct($content,$headers);
    
    $reasonPhrase = 
      isset(SymfonyResponse::$statusTexts[$statusCode]) ?
            SymfonyResponse::$statusTexts[$statusCode] :
            null;
    
    $this->setStatusCode  ($statusCode);
    $this->setReasonPhrase($reasonPhrase);
  }
  // $headers->set('Content-Type', 'text/html; charset='.$charset);
  
  /* =====================================================
   * Dump response to the client
   */
  public function send()
  {
    $this->sendHeaders();
    $this->sendContent();
  }
}