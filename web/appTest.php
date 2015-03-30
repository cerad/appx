<?php

require __DIR__  . '/../vendor/autoload.php';

use Cerad\Module\AppModule\AppKernel;

use Symfony\Component\HttpFoundation\Request;

class AppTest extends \PHPUnit_Framework_TestCase
{
  private $app;
  
  public function setUp()
  {
    $this->app = new AppKernel();
  }
  public function testCorsPreflight()
  {
    $request  = Request::create('/referees/42','OPTIONS');
    
    // Need these for CORS
    $origin = 'test';
    $request->headers->set('Origin',$origin);

    $response = $this->app->handle($request);
    
    $this->assertEquals($origin, $response->headers->get('Access-Control-Allow-Origin'));
  
  }
  public function testGetOneById()
  {
    $id = 42;
    
    $request  = Request::create('/referees/' . $id,'GET');

    $response = $this->app->handle($request);
    
    $item = json_decode($response->getContent(),true);
    
    $this->assertEquals($id, $item['id']);
  
  }
  public function testGetOneByUssfId()
  {
    $ussfId = '2014100800555735';
    
    $request  = Request::create('/referees/' . $ussfId,'GET');

    $response = $this->app->handle($request);
    
    $item = json_decode($response->getContent(),true);
    
    $this->assertEquals($ussfId, $item['ussf_id']);
  
  }
}
