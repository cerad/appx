<?php

class AppTest extends \PHPUnit_Framework_TestCase
{
  public function test1()
  {
    $matches = [];
    
    preg_match('#/referees#','/referees',$matches);
    $this->assertEquals('/referees',$matches[0]);
    
    preg_match('#/referees#','/games',$matches);
    $this->assertEquals(0,count($matches));

    preg_match('#/referees#','/referees/',$matches);
    $this->assertEquals('/referees',$matches[0]);
    
    preg_match('#/referees#','something/referees/',$matches);
    $this->assertEquals('/referees',$matches[0]);
    
    // ^ is start of line
    preg_match('#^/referees#','something/referees/',$matches);
    $this->assertEquals(0,count($matches));
    
    // $ is end of line
    preg_match('#^/referees$#','/referees/',$matches);
    $this->assertEquals(0,count($matches));
    
     // ?X is zero or one of X
    preg_match('#^/referees/?$#','/referees/',$matches);
    $this->assertEquals('/referees/',$matches[0]);
    
     // ?X is zero or one of X
    preg_match('#^/referees/?$#','/referees',$matches);
    $this->assertEquals('/referees',$matches[0]);
    
    // ( capture within) \d+ digits, the ? after the digit group makes it optional
    preg_match('#(^/referees/?)(\d+)?$#','/referees/123',$matches);
    $this->assertEquals('/referees/123',$matches[0]);
    $this->assertEquals('/referees/',   $matches[1]);
    $this->assertEquals('123',          $matches[2]);
    
    // w matches letter or number or underscore
    preg_match('#(^/referees/?)(\w+)?$#','/referees',$matches);
    $this->assertEquals('/referees',   $matches[1]);
    $this->assertEquals('/referees',   $matches[1]);
    
    // Anything after w will cause a failure
    preg_match('#(^/referees/?)(\w+)?$#','/referees/123/',$matches);
    $this->assertEquals(0,count($matches));
    
    // (?<name> allows naming the group, still get indexed value
    preg_match('#(^/referees/?)(?<id>\w+)?$#','/referees/123',$matches);
    $this->assertEquals('123',$matches[2]);
    $this->assertEquals('123',$matches['id']);
  }
  /*
   * a? Zero or one of a
   * a* Zero or more of a
   * a+ One or more of a
   * \w letter number underscore
   */
  public function test2()
  {
    $matches = [];
    $subject = '/program/:program_key/game/:game_num';
    
    preg_match('#([/\w-]*)*(:\w+)*#',$subject,$matches); // print_r($matches);
    
    preg_match('#([/\w-]+|:\w+)+#',$subject,$matches); //print_r($matches);
    
    preg_match('#((:\w+)+)#',$subject,$matches); print_r($matches);
  }
}
