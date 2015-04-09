<?php
namespace Cerad\Component\Http;

class Headers
{
  protected $headers = []; // Array of arrays
  
  public function __construct($headers)
  {
    if (!is_array($headers)) return;
    
    $headers = $this->extractFromServer($headers);
    
    $this->set($headers);
  }
  public function has($key)
  {
    return isset($this->headers[$key]) ? true : false;
  }
  public function hasValue($key,$value)
  {
    if (!isset($this->headers[$key])) return false;
    $values = explode(',',$this->headers[$key]);
    return array_search($value,$values) === false ? false : true;
  }
  public function set($key, $value = null)
  {
    if (is_array($key))
    {
      foreach($key as $keyx => $value)
      {
        $this->set($keyx,$value);
      }
      return;
    }
    if ($value === null)
    {
      if (isset($this->headers[$key])) unset($this->headers[$key]);
      return;
    }
    $value = is_array($value) ? implode(',',$value) : $value;
    
    // An empty string is okay
    $this->headers[$key] = $value;
  }
  public function setLine($key,$value)
  {
    // new key
    if (!isset($this->headers[$key])) return $this->set($key,$value);
    
    // Multiple lines
    $value = is_array($value) ? implode(',',$value) : $value;
    
    // Don't add empty lines including ''
    if (!$value) return;
    
    // In case have an empty existing value
    $valueExisting = $this->headers[$key];
    
    // Not checking for duplicate values, could explode but ok for now
    $this->headers[$key] = $valueExisting ? $valueExisting . ',' . $value : $value;
  }
  public function get($key = null, $default = null)
  {
    if ($key === null) return $this->headers;
    
    return isset($this->headers[$key]) ? $this->headers[$key] : $default;
  }
  public function getLines($key)
  {
    return isset($this->headers[$key]) ? explode(',',$this->headers[$key]) : [];
  }
  /* ========================================================
   * If this came from $_SERVER then extract HTTP_ headers
   */
  protected function extractFromServer($server)
  {
    if (!isset($server['SERVER_NAME'])) return $server;
    
    $headers = [];
    
    foreach($server as $key => $value)
    {
      if (substr($key,0,5) == 'HTTP_')
      {
        $key  = strtr(strtolower(substr($key,5)),'_','-');
        $keyx = implode('-', array_map('ucfirst', explode('-', $key)));
        
        $headers[$keyx] = $value;
      }
    }
    return $headers;
  }
}