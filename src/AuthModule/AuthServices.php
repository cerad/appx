<?php

namespace Cerad\Module\AuthModule;

class AuthServices
{
  public function __construct($container)
  {
    // Users
    $users = 
    [
      'ahundiak@gmail.com' 
        => ['password' => 'zzz','roles' => 'ROLE_ADMIN','person_name' => 'Art gmail Hundiak'],
      'ahundiak' => ['password' => 'zzz',      'roles' => 'ROLE_ADMIN',    'person_name' => 'Art Hundiak'],
      'sra'      => ['password' => 'sra',      'roles' => 'ROLE_SRA',      'person_name' => 'Pat Miller'],
      'assignor' => ['password' => 'assignor', 'roles' => 'ROLE_ASSIGNOR', 'person_name' => 'Andy Dye'],
      'user'     => ['password' => 'user',     'roles' => 'ROLE_USER',     'person_name' => 'Bill Steely'],
    ];
    $container->set('auth_users_data',$users);
    $container->set('auth_user_provider_in_memory',function($c)
    {
      return new \Cerad\Module\AuthModule\AuthUserProviderInMemory($c->get('auth_users_data'));
    });
    $container->set('auth_user_password_encoder_plain_text',function($c)
    {
      return new \Cerad\Module\AuthModule\AuthUserPasswordEncoderPlainText();
    });
    // Roles
    $hierarchy = 
    [
        'ROLE_USER'        => [],
        'ROLE_ASSIGNOR'    => ['ROLE_USER'],
        'ROLE_SRA'         => ['ROLE_ASSIGNOR'],
        'ROLE_ADMIN'       => ['ROLE_USER','ROLE_ASSIGNOR','ROLE_SRA'],
        'ROLE_SUPER_ADMIN' => ['ROLE_ADMIN'],
    ];
    $container->set('auth_role_hierarchy_data',$hierarchy);
    $container->set('auth_role_hierarchy',function($c)
    {
      return new \Cerad\Module\AuthModule\AuthRoleHierarchy($c->get('auth_role_hierarchy_data'));
    });
    $container->set('jwt_coder',function($c)
    {
      return new \Cerad\Component\Jwt\JwtCoder($c->get('secret'));
    });
    $container->set('auth_token_listener',function($c)
    {
      return new \Cerad\Module\AuthModule\AuthTokenListener
      (
        $c->get('auth_role_hierarchy'),
        $c->get('jwt_coder')
      );
    },'event_listener');
    
    $container->set('auth_token_controller',function($c)
    {
      return new \Cerad\Module\AuthModule\AuthTokenController
      (
        $c->get('jwt_coder'),
        $c->get('auth_user_provider_dao'),
        $c->get('auth_user_password_encoder_dao')
      );
    });
    $container->set('database_connection_users',function($c)
    {
      $config = new \Doctrine\DBAL\Configuration();
      
      $connectionParams = 
      [
        'url' => $c->get('db_url_users'),
        'driverOptions' => [\PDO::ATTR_EMULATE_PREPARES => false], // For limits
      ];
      $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
      
      return $conn;
    });    
    $container->set('auth_user_provider_dao',function($c)
    {
      return new \Cerad\Module\AuthModule\AuthUserProviderDao
      (
        $c->get('database_connection_users')
      );
    });
    $container->set('auth_user_password_encoder_dao',function($c)
    {
      return new \Cerad\Module\AuthModule\AuthUserPasswordEncoderDao
      (
        $c->get('cerad_user_master_password')
      );
    });
  }
}