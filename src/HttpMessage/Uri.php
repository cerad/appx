<?php

namespace Cerad\Component\HttpMessage;

use Psr\Http\Message\UriInterface;

class Uri implements UriInterface
{
  protected $scheme; // http | https
  
  protected $userInfo; // user:password
  protected $user;
  protected $pass;
  
  protected $host;
  
  protected $port;
  
  protected $path;
  
  protected $query;
  
  protected $fragment;
  
  protected function  setScheme($scheme) { $this->scheme = $scheme; }
  public    function  getScheme() { return $this->scheme; }
  public    function withScheme($scheme)
  {
    $pos = strpos($scheme,'://');
    if ($pos !== false) $scheme = strlen($scheme,0,$pos);
    
    switch($scheme)
    {
      case '': case 'http': case 'https': break;
      default:
        throw new \InvalidArgumentException('Scheme: ' . $scheme);
    }
    $uri = clone $this;
    $uri->setScheme($scheme);
    return $uri;
  }  
  protected function  setUser($user) { $this->user = $user; }
  protected function  setPass($pass) { $this->pass = $pass; }
  public    function  getUserInfo() 
  { 
    $user = $this->user;
    if (!$user) return null;
    
    return $this->pass? $user . ':' . $this->pass : $user;
  }
  public    function withUserInfo($user,$pass = null)
  {
    $uri = clone $this;
    $uri->setUser($user);
    $uri->setPass($pass);
    return $uri;
  }
  protected function setHost($host) { $this->host = $host; }
  public    function getHost() 
  { 
    return $this->host !== null ? $this->host : '';   
  }
  public function withHost($host)
  {
    // If invalid \InvalidArgumentException    
    $uri = clone $this;
    $uri->setHost($host);
    return $uri;
  }  
  protected function setPort($port) { $this->port = $port; }
  public    function getPort() 
  { 
    if (($this->scheme == 'http')  && ($this->port ==  80)) return null;
    if (($this->scheme == 'https') && ($this->port == 443)) return null;
    return $this->port; 
  }
  public function withPort($port)
  {
    // If invalid \InvalidArgumentException    
    $uri = clone $this;
    $uri->setPort($port);
    return $uri;
  }
  protected function setPath($path) { $this->path = $path; }
  public    function getPath()
  {
    return $this->path !== null ? $this->path : '/';   
  }
  public function withPath($path)
  {
    $uri = clone $this;
    
    if ($path === null)
    {
      $uri->setPath(null);
      return $uri;
    }
    if (strpos($path,'/') !== 0) $path = '/' . $path;
    
    // If invalid \InvalidArgumentException 
    // I like my slashes
    $path = str_replace('%2F','/',rawurlencode($path));
    
    $uri->setPath($path);
    
    return $uri;
  }
  protected function setQuery($query) { $this->query = $query; }
  public    function getQuery() 
  { 
    return $this->query !== null ? $this->query : '';   
  }
  public function withQuery($query)
  {
    $uri = clone $this;
    
    // Remove leading ?
    if (isset($query[0]) && ($query[0] == '?')) $query = substr($query,1);
    
    if (!$query)
    {
      $uri->setQuery(null);
      return $uri;
    }
    /* ============================================
     * Not going to do anymore encoding here
     * Assume http_build_query was used
     */
    // If invalid \InvalidArgumentException 
    // percent encode
    // $path = str_replace('%2F','/',rawurlencode($path));
    
    $uri->setQuery($query);
    return $uri;
  }
  protected function setFragment($fragment) { $this->fragment = $fragment; }
  public    function getFragment() 
  { 
    return $this->fragment !== null ? $this->fragment : '';   
  }
  public function withFragment($fragment)
  {
    $uri = clone $this;
    
    // Remove leading ?
    if (isset($fragment[0]) && ($fragment[0] == '#')) $fragment = substr($fragment,1);
    
    if (!$fragment)
    {
      $uri->setFragment(null);
      return $uri;
    }
    $uri->setFragment($fragment);
    return $uri;
  }
  // A read only method
  public function getAuthority() 
  { 
    $user = $this->getUserInfo();
    $host = $this->getHost();
    
    $user_host = $user ? $user . '@' . $host : $host;
    
    $port = $this->getPort();
    
    $auth = $port ? $user_host . ':' . $port : $user_host;
    
    return $auth; 
  }
  public function __toString()
  {
    $uriString = $this->getScheme() . '://' . $this->getAuthority() . $this->getPath();
    
    $query = $this->getQuery();
    if ($query) $uriString .= '?' . $query;
    
    $fragment = $this->getFragment();
    if ($fragment) $uriString .= '#' . $fragment;
    
    return $uriString;
  }
  /* ===============================================================
   * Not part of the psr-7 spec
   */
  public function __construct($url = null)
  {
    if (!$url) return;
    
    $parts = parse_url($url);
    
    // This is bypassing some of the validation but okay for now
    if (isset($parts['scheme'  ])) $this->setScheme  ($parts['scheme']);
    if (isset($parts['host'    ])) $this->setHost    ($parts['host']);
    if (isset($parts['port'    ])) $this->setPort    ($parts['port']);
    if (isset($parts['user'    ])) $this->setUser    ($parts['user']);
    if (isset($parts['pass'    ])) $this->setPass    ($parts['pass']);
    if (isset($parts['path'    ])) $this->setPath    ($parts['path']);
    if (isset($parts['query'   ])) $this->setQuery   ($parts['query']);
    if (isset($parts['fragment'])) $this->setFragment($parts['fragment']);
  }
}
