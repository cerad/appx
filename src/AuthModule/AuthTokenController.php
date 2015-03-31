<?php
namespace Cerad\Module\AuthModule;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class AuthTokenController
{
  private $jwtCoder;
  
  public function __construct($jwtCoder)
  {
    $this->jwtCoder = $jwtCoder;
  }
  public function postAction(Request $request)
  {
    $requestPayload = json_decode($request->getContent(),true);
    
    $username = $requestPayload['username'];
    $roles = ['ROLE_SRA'];
    
    $jwtPayload =
    [
      'iat'      => time(),
      'username' => $username,
      'roles'    => $roles,
    ];
    $jwt = $this->jwtCoder->encode($jwtPayload);
    
    return new JsonResponse(['authToken' => $jwt],202);
  }
}