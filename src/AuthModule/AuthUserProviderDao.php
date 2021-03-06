<?php
namespace Cerad\Module\AuthModule;

use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class AuthUserProviderDao
{
  private $db;
  
  public function __construct($db)
  {
    $this->db = $db;
  }
  public function loadUserByUsername($username)
  {
    $sql = <<<EOT
SELECT 
  id,username,email,salt,password,roles, 
  person_guid, account_name as person_name
FROM users
WHERE username = ? OR email = ?;
EOT;
    $stmt = $this->db->executeQuery($sql,[$username,$username]);
    $rows = $stmt->fetchAll();
    if (count($rows) != 1) 
    {
      $ex = new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
      $ex->setUsername($username);
      throw $ex;
    }
    $user = $rows[0];
    
    $user['roles'] =   unserialize($user['roles']);
    if (count($user['roles']) < 1) $user['roles'] = ['ROLE_USER'];
    
    return $user;
  }
}