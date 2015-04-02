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
    $password = $requestPayload['password'];
    
    $user = $this->userProvider->loadUserByUsername($username);
    $salt = isset($user['salt']) ? $user['salt'] : null;
    
    $this->userPasswordEncoder->isPasswordValid($user['password'],$password,$salt);
    
    // Need array_values because index can get messed up
    $roles = is_array($user['roles']) ? array_values($user['roles']) : [$user['roles']];

    $jwtPayload =
    [
      'iat'         => time(),
      'username'    => $username,
      'roles'       => $roles,
      'person_name' => $user['person_name'],
      'person_guid' => $user['person_guid'],
    ];
    $jwt = $this->jwtCoder->encode($jwtPayload);
    
    $jwtPayload['auth_token'] = $jwt;
    return new JsonResponse($jwtPayload,202);
  }
}