<?php

namespace Cerad\Module\AuthModule;

class AuthServices
{
  public function __construct($container)
  {
    $hierarchy = 
    [
        'ROLE_USER'     => [],
        'ROLE_ASSIGNOR' => ['ROLE_USER'],
        'ROLE_SRA'      => ['ROLE_ASSIGNOR'],
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
    $container->set('auth_token_controller',function($c)
    {
      return new \Cerad\Module\AuthModule\AuthTokenController
      (
        $c->get('auth_role_hierarchy'),
        $c->get('jwt_coder')
      );
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
        $c->get('jwt_coder')
      );
    });
  }
}