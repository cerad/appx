<?php
namespace Cerad\Component\Http;

class HttpTestBase extends \PHPUnit_Framework_TestCase
{
  const acceptImage = 'image/webp';
  const acceptJson  = 'application/json';
  
  protected $headersData =
  [
    'Accept'        => ['text/html','application/xml;q=0.9',self::acceptImage,'*/*;q=0.8'],
    'Cache-Control' => 'max-age=0',
    'Host'          => 'local.ang2.zayso.org:8002',
    'Authorization' => 'TOKEN',
  ];
  protected $cors =
  [
    'Access-Control-Allow-Headers' => 'Content-Type, Authorization',
    'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, PATCH, OPTIONS',
    'Access-Control-Allow-Origin'  => 'zayso.org'     
  ];
  protected $serverData =
  [
    'SERVER_PROTOCOL' => 'HTTP/1.1',
    'SERVER_NAME'     => 'local.ang2.zayso.org',
    'SERVER_PORT'     => '8002',
    'REQUEST_URI'     => '/xxx/123?role=xxx',
    'REQUEST_METHOD'  => 'OPTIONS',
    'PATH_INFO'       => '/xxx/123',
    'QUERY_STRING'    => 'role=xxx',
    'HTTP_HOST'       => 'local.ang2.zayso.org:8002',
    'HTTP_ACCEPT'     => 'text/html,application/xml;q=0.9,image/webp,*/*;q=0.8',
    'HTTP_USER_AGENT' => 'Mozilla/5.0 Chrome/41.0.2272.101 Safari/537.36',
  ];
  protected $urlString = 'https://user:pass@api.zayso.org:8080/referees?project=ng2016&Title=NG+2016#42';
}