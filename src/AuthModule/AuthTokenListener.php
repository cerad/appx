<?php

namespace Cerad\Module\AuthModule;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Cerad\Module\KernelModule\Event\KernelRequestEvent;

use Cerad\Module\AuthModule\AuthToken;

//  Cerad\Component\HttpMessage\Request;

class AuthTokenListener implements EventSubscriberInterface
{
  public static function getSubscribedEvents()
  {
    return 
    [
      KernelRequestEvent::name => 
      [
        ['onKernelRequestAuthToken', 250],
        ['onKernelRequestRoleToken', 250],
        ['onKernelRequestAuthorize', 245]
      ]
    ];
  }
  private $jwtCoder;
  private $roleHierarchy;
  
  public function __construct($roleHierarchy,$jwtCoder)
  {
    $this->jwtCoder      = $jwtCoder;
    $this->roleHierarchy = $roleHierarchy;
  }
  /* =================================================================
   * The firewall
   */
  public function onKernelRequestAuthorize($event)
  {
    // This is basically the master firewall
    $requestRoles = $event->getRequest()->attributes->get('_roles');
    if (!$requestRoles) return;

    // Need a token
    $authToken = $event->getRequest()->attributes->get('authToken');
    if (!$authToken)
    {
      throw new AccessDeniedException();
    }
    $userRoles = $authToken->getRoles();

    if ($this->roleHierarchy->isAuthorized($requestRoles,$userRoles)) return;
    
    throw new AccessDeniedException();
    
  }
  /* =================================================================
   * This pull any auth token from the request header and 
   * tucks it into the request
   */
  public function onKernelRequestAuthToken($event)
  {
    if (!$event->isMasterRequest()) return;
    
    $jwt = $event->getRequest()->headers->get('Authorization');
    
    if (!$jwt) return;

    $jwtPayload = $this->jwtCoder->decode($jwt);
    
    $authToken = new AuthToken($jwtPayload['username'],$jwtPayload['roles']);
    
    $event->getRequest()->attributes->set('authToken',$authToken);
  }
  public function onKernelRequestRoleToken($event)
  {
    if (!$event->isMasterRequest()) return;
    
    $params = $event->getRequest()->getQueryParams();
    
    $role = isset($params['role']) ? $params['role'] : null;
    
    if (!$role) return;
      
    $authToken = new AuthToken('role',[strtoupper($role)]);
    
    $event->getRequest()->attributes->set('authToken',$authToken);
  }
}