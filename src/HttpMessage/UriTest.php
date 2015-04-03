<?php

namespace Cerad\Component\HttpMessage;

use Cerad\Component\HttpMessage\Uri;

require __DIR__  . '/../../vendor/autoload.php';

class UriTest extends \PHPUnit_Framework_TestCase
{
  public function testBasic()
  {
    $uri = new Uri();
    
    // TODO: Test exceptions later
    $uri1 = $uri->withScheme('https');
    $this->assertEquals('https',$uri1->getScheme());
    
    $uri2 = $uri1->withUserInfo('user','pass');
    $this->assertEquals('user:pass',$uri2->getUserInfo());
    
    $uri3 = $uri2->withHost('api.zayso.org');
    $this->assertEquals('api.zayso.org',$uri3->getHost());
    
    $uri4 = $uri3->withPort(8080);
    $this->assertEquals(8080,$uri4->getPort());
    
    $this->assertEquals('user:pass@api.zayso.org:8080',$uri4->getAuthority());
    
    $uri5 = $uri4->withPath('/referees');
    $this->assertEquals('/referees',$uri5->getPath());
    
    $uri6 = $uri5->withPath('/ref$erees');
    $this->assertEquals('/ref%24erees',$uri6->getPath());
    
    $query = http_build_query(['project' => 'ng2016','title' => 'NG 2016']);
    $uri7 = $uri5->withQuery($query);
    $this->assertEquals('project=ng2016&title=NG+2016',$uri7->getQuery());
    
    $params = [];
    parse_str($query,$params);
    $this->assertEquals('NG 2016',$params['title']);
    
    $uri8 = $uri7->withFragment('42');
    $this->assertEquals('42',$uri8->getFragment());
    
    $uri7String = 'https://user:pass@api.zayso.org:8080/referees?project=ng2016&title=NG+2016#42';
    $this->assertEquals($uri7String,$uri8->__toString());
    
    $urix = new Uri($uri7String);
    $this->assertEquals('/referees',$urix->getPath());
  }
}