<?php

require __DIR__  . '/../vendor/autoload.php';

use Cerad\Module\AppModule\AppKernel;

use Symfony\Component\HttpFoundation\Request;

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
    $request  = Request::create($this->resource . '/42','OPTIONS');
    
    // Need these for CORS
    $origin = 'test';
    $request->headers->set('Origin',$origin);

    $response = $this->app->handle($request);
    
    $this->assertEquals($origin, $response->headers->get('Access-Control-Allow-Origin'));
  
  }
  public function testGetOneById()
  {
    $id = 42;
    
    $request  = Request::create($this->resource . '/' . $id . '?role=role_admin','GET');

    $response = $this->app->handle($request);
    
    $item = json_decode($response->getContent(),true);
    
    $this->assertEquals($id, $item['id']);
  
  }
  public function testGetOneByUssfId()
  {
    $ussfId = '2014100800555735';
    
    $url = $this->resource . '/' . $ussfId  . '?role=role_admin';
    
    $request  = Request::create($url,'GET');

    $response = $this->app->handle($request);
    
    $item = json_decode($response->getContent(),true);
    
    $this->assertEquals($ussfId, $item['ussf_id']);
    
    $container = $this->app->getContainer();
    $routeGenerator = $container->get('route_generator');
    
    $url2 = $routeGenerator->generate('referee_resource_one', ['id' => $ussfId,'role'=>'role_admin']);
    $this->assertEquals($url,$url2);
     
  }
  public function testAll()
  {
    $request  = Request::create($this->resource . '?role=role_admin','GET');

    $response = $this->app->handle($request);
    
    $items = json_decode($response->getContent(),true);
    
    $this->assertEquals(3, count($items));
  
  }
  public function testCreateAuthToken()
  {
    $content = json_encode(['username' => 'ahundiak@gmail.com','password'=>'zzz']);
    
    $request = Request::create('/auth/tokens','POST',[],[],[],[],$content);
    
    $response = $this->app->handle($request);
    $this->assertEquals(202,                $response->getStatusCode());
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
    $request = Request::create($this->resource . 'x','GET');
    $request->headers->set('Authorization',$jwt);
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
    $request  = Request::create($this->resource . 'x','GET');
    $response = $this->app->handle($request);
    $this->assertEquals(401,$response->getStatusCode());
  }
  public function testAuthRequestRole()
  {
    $uri = $this->resource . 'x' . '?role=role_sra';
    
    $request  = Request::create($uri,'GET');
    $response = $this->app->handle($request);
    
    $this->assertEquals(200,$response->getStatusCode());
  }
  public function testAuthRequestRoleFail()
  {
    $uri = $this->resource . 'x' . '?role=role_user';
    
    $request  = Request::create($uri,'GET');
    $response = $this->app->handle($request);
    
    $this->assertEquals(401,$response->getStatusCode());
  }
}
