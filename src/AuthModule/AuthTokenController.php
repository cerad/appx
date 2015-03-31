<?php
namespace Cerad\Module\AuthModule;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class AuthTokenController
{
  private $jwtCoder;
  private $userProvider;
  private $userPasswordEncoder;
  
  public function __construct($jwtCoder,$userProvider,$userPasswordEncoder)
  {
    $this->jwtCoder = $jwtCoder;
    $this->userProvider = $userProvider;
    $this->userPasswordEncoder = $userPasswordEncoder;
  }
  public function postAction(Request $request)
  {
    $requestPayload = json_decode($request->getContent(),true);
    
    $username = $requestPayload['username'];
    
    $user = $this->userProvider->loadUserByUsername($username);
    
    $this->userPasswordEncoder->isPasswordValid($user,$requestPayload['password']);
    
    $jwtPayload =
    [
      'iat'      => time(),
      'username' => $username,
      'roles'    => $user['roles'],
    ];
    $jwt = $this->jwtCoder->encode($jwtPayload);
    
    return new JsonResponse(['authToken' => $jwt],202);
  }
}