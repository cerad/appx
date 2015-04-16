<?php
namespace Cerad\Module\KernelModule\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Cerad\Component\HttpMessage\Response;
use Cerad\Component\HttpMessage\ResponsePreflight;

use Cerad\Module\KernelModule\Event\KernelRequestEvent;
use Cerad\Module\KernelModule\Event\KernelResponseEvent;

/* ========================================================
 * http://www.html5rocks.com/en/tutorials/cors/
 */
class CorsListener implements EventSubscriberInterface
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
    
    // Test got Cors Preflight
    $request = $event->getRequest();
    
    if (!$request->hasHeader('Origin')) return;
    
    if ($request->getMethod() !== 'OPTIONS') return;
    
    if (!$request->hasHeader('Access-Control-Request-Method')) return;
    
    // Assume Access-Control-Request-Method is valid, use default for caching
    
    $allowOrigin  = $request->getHeaderLine('Origin');
    $allowHeaders = $request->getHeaderLine('Access-Control-Request-Header');
    
    $response = new ResponsePreflight($allowOrigin,$allowHeaders);
   
    $event->setResponse($response);
    $event->stopPropagation();
  }
  public function onKernelResponse(KernelResponseEvent $event)
  {
    if (!$event->hasResponse()) return;
    
    $response = $event->getResponse();
    
    if ($response->hasHeader('Access-Control-Allow-Origin')) return;
    
    $allowOrigin = $event->getRequest()->getHeaderLine('Origin');
    if (!$allowOrigin) return false;
    
    $responsex = $response->withHeader('Access-Control-Allow-Origin',$allowOrigin);
    
    $event->setResponse($responsex);
  }
}
