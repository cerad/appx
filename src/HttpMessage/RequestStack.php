<?php

namespace Cerad\Component\HttpMessage;

use Cerad\Component\HttpMessage\Request;

class RequestStack
{
    private $requests = [];
    
    public function push(Request $request)
    {
      $this->requests[] = $request;
    }
    public function pop()
    {
      return array_pop($this->requests);
    }
    public function getCurrentRequest()
    {
        return end($this->requests) ?: null;
    }

    public function getMasterRequest()
    {
        if (!$this->requests) return; // Need some tests here

        return $this->requests[0];
    }

    public function getParentRequest()
    {
        $pos = count($this->requests) - 2;

        if (!isset($this->requests[$pos])) return;

        return $this->requests[$pos];
    }
}
