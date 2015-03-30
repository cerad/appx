<?php

require __DIR__  . '/../vendor/autoload.php';

use Cerad\Module\AppModule\AppKernel;

use Symfony\Component\HttpFoundation\Request;

class AppTest extends \PHPUnit_Framework_TestCase
{
  private $app;
  private $resource = '/api/referees';
  
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
    
    $request  = Request::create($this->resource . '/' . $id,'GET');

    $response = $this->app->handle($request);
    
    $item = json_decode($response->getContent(),true);
    
    $this->assertEquals($id, $item['id']);
  
  }
  public function testGetOneByUssfId()
  {
    $ussfId = '2014100800555735';
    
    $url1 = $this->resource . '/' . $ussfId;
    
    $request  = Request::create($url1,'GET');

    $response = $this->app->handle($request);
    
    $item = json_decode($response->getContent(),true);
    
    $this->assertEquals($ussfId, $item['ussf_id']);
    
    $container = $this->app->getContainer();
    $routeGenerator = $container->get('route_generator');
    
    $url2 = $routeGenerator->generate('referee_resource_one', ['id' => $ussfId]);
    $this->assertEquals($url1,$url2);
     
  }
  public function testAll()
  {
    $request  = Request::create($this->resource,'GET');

    $response = $this->app->handle($request);
    
    $items = json_decode($response->getContent(),true);
    
    $this->assertEquals(3, count($items));
  
  }
}
