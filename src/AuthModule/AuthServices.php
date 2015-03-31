<?php

namespace Cerad\Module\AuthModule;

class AuthServices
{
  public function __construct($container)
  {
    // Users
    $users = 
    [
      'ahundiak' => ['password' => 'zzz',      'roles' => 'ROLE_ADMIN'],
      'sra'      => ['password' => 'sra',      'roles' => 'ROLE_SRA'],
      'assignor' => ['password' => 'assignor', 'roles' => 'ROLE_ASSIGNOR'],
      'user'     => ['password' => 'user',     'roles' => 'ROLE_USER'],
    ];
    $container->set('auth_users_data',$users);
    $container->set('auth_user_provider',function($c)
    {
      return new \Cerad\Module\AuthModule\AuthUserProvider($c->get('auth_users_data'));
    });
    $container->set('auth_user_password_encoder',function($c)
    {
      return new \Cerad\Module\AuthModule\AuthUserPasswordEncoder();
    });
    // Roles
    $hierarchy = 
    [
        'ROLE_USER'     => [],
        'ROLE_ASSIGNOR' => ['ROLE_USER'],
        'ROLE_SRA'      => ['ROLE_ASSIGNOR'],
        'ROLE_ADMIN'    => ['ROLE_USER','ROLE_ASSIGNOR','ROLE_SRA'],
    ];
    $container->set('auth_role_hierarchy_data',$hierarchy);
    $container->set('auth_role_hierarchy',function($c)
    {
      return new \Cerad\Module\AuthModule\AuthRoleHierarchy($c->get('auth_role_hierarchy_data'));
    });
    $container->set('jwt_coder',function($c)
    {
      return new \Cerad\Component\JWT\JWTCoder($c->get('secret'));
    });
    $container->set('auth_token_listener',function($c)
    {
      return new \Cerad\Module\AuthModule\AuthTokenListener
      (
        $c->get('auth_role_hierarchy'),
        $c->get('jwt_coder')
      );
    },'kernel_event_listener');
    
    $container->set('auth_token_controller',function($c)
    {
      return new \Cerad\Module\AuthModule\AuthTokenController
      (
        $c->get('jwt_coder'),
        $c->get('auth_user_provider'),
        $c->get('auth_user_password_encoder')
      );
    });
  }
}