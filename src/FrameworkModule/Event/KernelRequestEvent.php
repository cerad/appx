<?php

namespace Cerad\Module\FrameworkModule\Event;

use Symfony\Component\EventDispatcher\Event;

class KernelRequestEvent extends Event
{
  const name = 'CeradFrameworkKernelRequest';
  
  private $request;
  private $response;
  
  public function __construct($request)
  {
    $this->request = $request;
  }
  public function getRequest () { return $this->request; }
  public function getResponse() { return $this->response; }
  public function hasResponse() { return $this->response ? true : false; }
  
  public function setResponse($response) { $this->response = $response; }
}