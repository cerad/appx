<?php
namespace Cerad\Module\FrameworkModule\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Symfony\Component\HttpFoundation\Response;

use Cerad\Module\FrameworkModule\Event\KernelRequestEvent;
use Cerad\Module\FrameworkModule\Event\KernelResponseEvent;

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
  public function onKernelRequest(KernelRequestEvent $event)
  {
    $request = $event->getRequest();
    
    if ($event->getRequest()->getMethod() !== 'OPTIONS') return;
    
    $response = new Response();
    $response->headers->set('Access-Control-Allow-Headers','Content-Type, Authorization');
    $response->headers->set('Access-Control-Allow-Methods','GET, POST, PUT, DELETE, PATCH, OPTIONS');
    $response->headers->set('Access-Control-Allow-Origin', '*');
    $response->setMaxAge(3600 * 24);
    
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
