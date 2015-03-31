<?php
namespace Cerad\Module\AuthModule;

use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class AuthUserProvider
{
  private $users;
  
  public function __construct($users)
  {
    $this->users = $users;
  }
  public function loadUserByUsername($username)
  {
    if (isset($this->users[$username]))
    {
      $user = $this->users[$username];
      $user['username'] = $username;
      return $user;
    }
    $ex = new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
    $ex->setUsername($username);
    throw $ex;
  }
}