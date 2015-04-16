<?php

namespace Cerad\Module\AuthModule\Tests;

use Cerad\Module\KernelModule\Event\KernelRequestEvent;

use Cerad\Module\AuthModule\AuthToken;
use Cerad\Module\AuthModule\AuthTokenListener;
use Cerad\Module\AuthModule\AuthRoleHierarchy;

use Cerad\Component\HttpMessage\Request;

class AuthTokenTest extends AuthTests
{
  public function testNewToken()
  {
    $token = new AuthToken('ahundiak',['ROLE_USER','ROLE_SRA']);
    
    $this->assertEquals(2, count($token->getRoles()));
  }
  public function testPostToken()
  {
    $jwtCoder   = $this->container->get('jwt_coder');
    $controller = $this->container->get('auth_token_controller');

    $content = json_encode(['username' => 'ahundiak@testing.com','password'=>'zzz']);
    $headers = ['Content-Type' => 'application/json'];
    $request = new Request('POST /auth/tokens',$headers,$content);
    
    $response = $controller->postAction($request);
    $this->assertEquals(201, $response->getStatusCode());
    
    $responsePayload = json_decode($response->getBody()->getContents(),true);
    
    $authJwt = $responsePayload['auth_token'];
    
    $authPayload = $jwtCoder->decode($authJwt);
    $this->assertEquals('ahundiak@testing.com',$authPayload['username']);
  }
  /**
   * @expectedException Symfony\Component\Security\Core\Exception\UsernameNotFoundException
   */
  public function testPostTokenUsernameNotFound()
  {
    $container  = $this->container;
    $jwtCoder   = $this->container->get('jwt_coder');
    $controller = $this->container->get('auth_token_controller');

    $content  = json_encode(['username' => 'ahundiakx','password'=>'zzz']);
    $headers = ['Content-Type' => 'application/json'];
    $request  = new Request('POST /auth/tokens',$headers,$content);
    $response = $controller->postAction($request);
  }
  /**
   * @expectedException Symfony\Component\Security\Core\Exception\BadCredentialsException
   */
  public function testPostTokenInvalidPassword()
  {
    $jwtCoder   = $this->container->get('jwt_coder');
    $controller = $this->container->get('auth_token_controller');

    $content  = json_encode(['username' => 'ahundiak@testing.com','password'=>'zzzx']);
    $headers = ['Content-Type' => 'application/json'];
    $request  = new Request('POST /auth/tokens',$headers,$content);
    $response = $controller->postAction($request);
  }
  protected function createRoleHeirarchy()
  {
    $hierarchy = 
    [
        'ROLE_USER'     => [],
        'ROLE_ASSIGNOR' => ['ROLE_USER'],
        'ROLE_SRA'      => ['ROLE_ASSIGNOR'],
    ];
    return new AuthRoleHierarchy($hierarchy);
  }
  public function testAuthTokenListener()
  {
    $jwtCoder = $this->container->get('jwt_coder');

    $roleHierarchy = $this->createRoleHeirarchy();
    $listener = new AuthTokenListener($roleHierarchy,$jwtCoder);
    $jwt = $jwtCoder->encode(['username' => 'ahundiak@testing.com','roles' => ['ROLE_USER']]);
    
    $headers = ['Authorization' => $jwt];
    $request = new Request('GET /api/referees',$headers);
    $request->setAttribute('_roles','ROLE_USER');
    
    $event = new KernelRequestEvent($request);
    $listener->onKernelRequestAuthToken($event);
    
    $authToken = $request->getAttribute('authToken');
    $this->assertEquals('ahundiak@testing.com',$authToken->getUsername());
    
    $listener->onKernelRequestAuthorize($event);
  }
  public function testRoleHierarchy()
  {
    $roleHierarchy = $this->createRoleHeirarchy();
    
    $roles1 = $roleHierarchy->getReachableRoles(['ROLE_ASSIGNOR']);
    
    $allowedFalse = $roleHierarchy->isAuthorized(['ROLE_SRA'],['ROLE_ASSIGNOR']);
    $this->assertEquals(false,$allowedFalse);
    
    $allowedTrue = $roleHierarchy->isAuthorized(['ROLE_ASSIGNOR'],'ROLE_SRA');
    $this->assertEquals(true,$allowedTrue);
  }
}