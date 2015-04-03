<?php
namespace Cerad\Component\HttpMessage;

use Psr\Http\Message\StreamableInterface;

class TextStream implements StreamableInterface
{
  protected $opened = true;
  protected $eof = false;
  
  protected $text;
  
  public function __construct($text)
  {
    $this->text = $text;
  }
  public function __toString() 
  { 
    $this->eof = true;
    return $this->text; 
  }
  
  public function close() { $this->opened = false; }
  
  public function detach() { $this->text = null; return null; }
  
  public function getSize() { return strlen($this->text); }
  
  public function tell() { return 0; }
  
  public function eof() { return $this->eof; }
  
  public function isSeekable() { return false; }
  
  public function seek($offset, $whence = SEEK_SET) { return false; }
  
  public function rewind() { return false; }
  
  public function isWritable() { return true; }
  
  public function write($string)
  {
    $this->text = $string;
    $this->eof = false;
  }
  public function isReadable() { return true; }
  
  public function read($length)
  {
    $this->eof = true;
    return $this->text;
  }
  public function getContents()
  {
    $this->eof = true;
    return $this->text;
  }
  public function getMetadata($key = null) 
  { 
    throw new \BadMethodCallException('TextStream::getMetadata is not implemented');
  }
}