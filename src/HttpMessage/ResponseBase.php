<?php
namespace Cerad\Component\HttpMessage;

use Psr\Http\Message\ResponseInterface;

use Cerad\Component\HttpMessage\Message;

use Symfony\Component\HttpFoundation\Response as SymfonyResponse; // For status code stuff

/* ====================================================================
 * This is the "pure" PSR-7 implementation
 */
class ResponseBase extends Message implements ResponseInterface
{
  protected $statusCode;
  protected $statusCodeReasonPhrase;
  
  public function withStatus($statusCode,$reasonPhrase = null)
  {
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