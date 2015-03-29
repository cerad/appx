<?php

namespace Cerad\Module\KernelModule\Event;

use Symfony\Component\EventDispatcher\Event;

class KernelResponseEvent extends Event
{
  const name = 'CeradKernelResponse';
  
  private $request;
  private $response;
  
  public function __construct($request,$response)
  {
    $this->request  = $request;
    $this->response = $response;
  }
  public function getRequest () { return $this->request; }
  public function getResponse() { return $this->response; }
  public function hasResponse() { return $this->response ? true : false; }
  
  public function setResponse($response) { $this->response = $response; }
}