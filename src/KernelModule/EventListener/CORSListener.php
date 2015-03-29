<?php
namespace Cerad\Module\KernelModule\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Symfony\Component\HttpFoundation\Response;

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
  public function onKernelRequest(KernelRequestEvent $event)
  {
    if (!$event->isMasterRequest()) return;
    
    if ($event->getRequest()->getMethod() !== 'OPTIONS') return;
    
    $response = new Response();
    $response->headers->set('Access-Control-Allow-Headers','Content-Type, Authorization');
    $response->headers->set('Access-Control-Allow-Methods','GET, POST, PUT, DELETE, PATCH, OPTIONS');
    $response->headers->set('Access-Control-Allow-Origin', '*'); // Maybe pull from request
    
    if ($this->maxAge) $response->setMaxAge($this->maxAge);
    
    $event->setResponse($response);
    $event->stopPropagation();
  }
  public function onKernelResponse(KernelResponseEvent $event)
  {
    $response = $event->getResponse();
    
    if (!$response) return;
    
    // No explicit cache control
    $response->headers->set('Access-Control-Allow-Headers','Content-Type, Authorization');
    $response->headers->set('Access-Control-Allow-Methods','GET, POST, PUT, DELETE, PATCH, OPTIONS');
    $response->headers->set('Access-Control-Allow-Origin', '*');
  }
}
