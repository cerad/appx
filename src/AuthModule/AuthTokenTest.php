<?php

namespace Cerad\Module\AuthModule;

use Cerad\Module\KernelModule\Event\KernelRequestEvent;

use Cerad\Module\AuthModule\AuthToken;
use Cerad\Module\AuthModule\AuthTokenController;
use Cerad\Module\AuthModule\AuthTokenListener;
use Cerad\Module\AuthModule\AuthRoleHierarchy;

use Cerad\Component\JWT\JWTCoder;

use Symfony\Component\HttpFoundation\Request;

class AuthTokenTest extends \PHPUnit_Framework_TestCase
{
  public static function setUpBeforeClass()
  {
    require __DIR__  . '/../../vendor/autoload.php';
  }

  public function testNewToken()
  {
    $token = new AuthToken('ahundiak',['ROLE_USER','ROLE_SRA']);
    
    $this->assertEquals(2, count($token->getRoles()));
  }
  public function testPostToken()
  {
    $jwtCoder = new JWTCoder('secret');
    $controller = new AuthTokenController($jwtCoder);

    $content = json_encode(['username' => 'ahundiak','password'=>'zzz']);
    $request = Request::create('/auth/tokens','POST',[],[],[],[],$content);
    
    $response = $controller->postAction($request);
    $this->assertEquals(202,                $response->getStatusCode());
    $this->assertEquals('application/json', $response->headers->get('Content-Type'));
    
    $responsePayload = json_decode($response->getContent(),true);
    
    $authJWT = $responsePayload['authToken'];
    
    $authPayload = $jwtCoder->decode($authJWT);
    $this->assertEquals('ahundiak',$authPayload['username']);
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
    $jwtCoder = new JWTCoder('secret');
    $roleHierarchy = $this->createRoleHeirarchy();
    $listener = new AuthTokenListener($roleHierarchy,$jwtCoder);
    $jwt = $jwtCoder->encode(['username' => 'ahundiak','roles' => ['ROLE_USER']]);
    
    $request = Request::create('/api/referees','GET');
    $request->headers->set('Authorization',$jwt);
    $request->attributes->set('_roles','ROLE_USER');
    
    $event = new KernelRequestEvent($request);
    $listener->onKernelRequestToken($event);
    
    $authToken = $request->attributes->get('authToken');
    $this->assertEquals('ahundiak',$authToken->getUsername());
    
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