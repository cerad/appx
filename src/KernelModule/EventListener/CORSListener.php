<?php
namespace Cerad\Module\KernelModule\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Cerad\Component\HttpMessage\Response;

use Cerad\Module\KernelModule\Event\KernelRequestEvent;
use Cerad\Module\KernelModule\Event\KernelResponseEvent;

class CORSListener implements EventSubscriberInterface
{
  public static function getSubscribedEvents()
  {
    return 
    [
      KernelRequestEvent ::name => [['onKernelRequest', 255]],
      KernelResponseEvent::name => [['onKernelResponse',  0]],
    ];
  }
  protected $maxAge = 86400; //3600 * 24;
  
  protected $allowOrigin = null;
  
  protected function addCorsHeaders($response)
  {
    // No origin means no cors
    if (!$this->allowOrigin) return;
    
    $response->headers->set('Access-Control-Allow-Headers','Content-Type, Authorization');
    $response->headers->set('Access-Control-Allow-Methods','GET, POST, PUT, DELETE, PATCH, OPTIONS');
    $response->headers->set('Access-Control-Allow-Origin', $this->allowOrigin); // Maybe pull from request    
  }
  public function onKernelRequest(KernelRequestEvent $event)
  {
    if (!$event->isMasterRequest()) return;
    
    // CORS Must have Origin header
    $request = $event->getRequest();
    $this->allowOrigin = $request->headers->get('Origin');
    if (!$this->allowOrigin) return;
    
    // Only interested in preflights
    if ($request->getMethod() !== 'OPTIONS') return;

    // It's a prefilght
    $response = $event->getResponse() ? $event->getResponse() : new Response();
    $this->addCorsHeaders($response);
    
  //if ($this->maxAge) $response->setMaxAge($this->maxAge);
    
    $event->setResponse($response);
    $event->stopPropagation();
  }
  public function onKernelResponse(KernelResponseEvent $event)
  {
    if (!$event->hasResponse() || !$this->allowOrigin) return null;
    
    $response = $event->getResponse();
    
    // No explicit cache control
    $this->addCorsHeaders($response);
  }
}
