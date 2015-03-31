<?php
namespace Cerad\Module\AuthModule;

use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class AuthUserPasswordEncoder
{
  public function encodePassword($user, $plainPassword)
  {
    return $plainPassword;
  }
  public function isPasswordValid($user, $plainPassword)
  {
    if ($user['password'] == $plainPassword) return true;
    
    throw new BadCredentialsException('Invalid Password');
  }
}