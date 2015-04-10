<?php

require __DIR__  . '/../vendor/autoload.php';

use Cerad\Module\AppModule\AppKernel;

use Cerad\Component\HttpMessage\Request;

class AppTest extends \PHPUnit_Framework_TestCase
{
  private $app;
  private $resource = '/referees';
  
  public function setUp()
  {
    $this->app = new AppKernel();
  }
  public function testCorsPreflight()
  {
    $request = new Request('OPTIONS ' . $this->resource . '/42');
    
    // Need these for CORS
    $origin = 'test';
    $request->headers->set('Origin',$origin);

    $response = $this->app->handle($request);
    
    $this->assertEquals($origin, $response->headers->get('Access-Control-Allow-Origin'));
  
  }
  public function testGetOneById()
  {
    $id = 42;
    
    $request  = new Request('GET ' . $this->resource . '/' . $id . '?role=role_admin');

    $response = $this->app->handle($request);
    
    $item = json_decode($response->getContent(),true);
    
    $this->assertEquals($id, $item['id']);
  
  }
  public function testGetOneByUssfId()
  {
    $ussfId = '2014100800555735';
    
    $url = $this->resource . '/' . $ussfId  . '?role=role_admin';
    
    $request  = new Request('GET ' . $url);

    $response = $this->app->handle($request);
    
    $item = json_decode($response->getContent(),true);
    
    $this->assertEquals($ussfId, $item['ussf_id']);
    
    return;
    
    // Route generator not yet implemented
    $container = $this->app->getContainer();
    $routeGenerator = $container->get('route_generator');
    
    $url2 = $routeGenerator->generate('referee_resource_one', ['id' => $ussfId,'role'=>'role_admin']);
    $this->assertEquals($url,$url2);
     
  }
  public function testAll()
  {
    $request = new Request('GET ' . $this->resource . '?role=role_admin');

    $response = $this->app->handle($request);
    
    $items = json_decode($response->getContent(),true);
    
    $this->assertEquals(3, count($items));
  
  }
  public function testCreateAuthToken()
  {
    $content = json_encode(['username' => 'ahundiak@gmail.com','password'=>'zzz']);
    
    $headers = ['Content-Type' => 'application/json'];
    
    $request = new Request('POST /auth/tokens',$headers,$content);
    
    $response = $this->app->handle($request);
    $this->assertEquals(201,                $response->getStatusCode());
    $this->assertEquals('application/json', $response->headers->get('Content-Type'));

    $responsePayload = json_decode($response->getContent(),true);
    
    $jwt = $responsePayload['auth_token'];
    
    return $jwt;
  }
  /**
   * @depends testCreateAuthToken
   */
  public function testAuthRequestSuccess($jwt)
  {
    $headers = ['Authorization' => $jwt];
    $request = new Request('GET ' . $this->resource,$headers);
    
    $response = $this->app->handle($request);
    
    $responsePayload = json_decode($response->getContent(),true);
    
    $this->assertEquals(3,count($responsePayload));
  }
  /**
   * @depends testCreateAuthToken
   *  expectedException Symfony\Component\Security\Core\Exception\AccessDeniedException
   */
  public function testAuthRequestFailure($jwt)
  {
    $headers = [];
    $request = new Request('GET ' . $this->resource,$headers);

    $response = $this->app->handle($request);
    
    $this->assertEquals(401,$response->getStatusCode());
  }
}
