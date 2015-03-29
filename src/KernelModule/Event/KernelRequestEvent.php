<?php

namespace Cerad\Module\KernelModule\Event;

use Symfony\Component\EventDispatcher\Event;

use Cerad\Module\KernelModule\KernelApp;

class KernelRequestEvent extends Event
{
  const name = 'CeradKernelRequest';
  
  private $type;
  private $request;
  private $response;
  
  public function __construct($request,$type)
  {
    $this->type = $type;
    $this->request = $request;
  }
  public function isMasterRequest() { return $this->type == KernelApp::REQUEST_TYPE_MASTER; }
  
  public function getRequest () { return $this->request; }
  public function getResponse() { return $this->response; }
  public function hasResponse() { return $this->response ? true : false; }
  
  public function setResponse($response) { $this->response = $response; }
}