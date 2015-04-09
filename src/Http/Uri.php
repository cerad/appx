<?php

namespace Cerad\Component\Http;

use Psr\Http\Message\UriInterface;

class Uri implements UriInterface
{
  protected $parts =
  [
    'scheme'   => null,
    'user'     => null,
    'pass'     => null,
    'host'     => null,
    'port'     => null,
    'path'     => null,
    'query'    => null,
    'fragment' => null,     
  ];
  
  public function getScheme()
  {
    return $this->parts['scheme'] ? $this->parts['scheme'] : 'http';
  }
  public function getPath()
  {
    return $this->parts['path'] ? $this->parts['path'] : '/';
  }
  public function getHost()
  {
    return $this->parts['host'] ? $this->parts['host'] : 'UNKNOWN';
  }
  public function getPort()
  {
    return $this->parts['port'] ? $this->parts['port'] : null;
  }
  public function getQuery()
  {
    return $this->parts['query'] ? $this->parts['query'] : null;
  }
  public function getQueryParams()
  {
    $params = [];
    
    parse_str($this->getQuery(),$params);
    
    return array_change_key_case($params,CASE_LOWER);
  }
  public function getFragment()
  {
    return $this->parts['fragment'] ? $this->parts['fragment'] : null;
  }
  public function getUserInfo() 
  { 
    $user = $this->parts['user'];
    $pass = $this->parts['pass'];
    
    if (!$user) return null;
    
    return $pass ? $user . ':' . $pass : $user;
  }
  public function getAuthority() 
  { 
    $userInfo = $this->getUserInfo();
    $host     = $this->getHost();
    
    $userInfoHost = $userInfo ? $userInfo . '@' . $host : $host;
    
    $port = $this->getPort();
    
    $auth = $port ? $userInfoHost . ':' . $port : $userInfoHost;
    
    return $auth; 
  }
  public function __construct($url = null)
  {
    if (is_string($url)) return $this->parseUrl(   $url);
    if (is_array ($url)) return $this->parseServer($url);
   
    return;
    
  }
  protected function parseServer($server)
  {
    // Host an port TODO: Look for user info
    $host = isset($server['HTTP_HOST']) ? $server['HTTP_HOST'] : $server['SERVER_NAME'];
    $hostParts = explode(':',$host);
    $port = isset($hostParts[1]) ? $hostParts[1] : $server['SERVER_PORT'];
    
    $this->parts['host'] = strtolower($hostParts[0]);
    $this->parts['port'] = $port;
    
    $this->parts['query'] = isset($server['QUERY_STRING']) ? urldecode($server['QUERY_STRING']) : '';
    
    /* ======================================
     * Sometimes PATH_INFO is not set
     * Sometimes when using using php -S
     * 
     * Test more with web sub directories
     * And implications for generating api prefixes
     */
    if (isset($server['PATH_INFO'])) $path = $server['PATH_INFO'];
    else
    {
      // Request uri contains query string
      $parts = explode('?',$server['REQUEST_URI']);
      $path  = $parts[0];
  
      /* ======================================
       * Test more with web sub directories
       * And implications for generating api prefixes
      */
    }
    $this->parts['path'] = $path;
    
    /* ==================================================
     * scheme is funky
     * http://php.net/manual/en/reserved.variables.server.php
     */
    $https = isset($server['HTTPS']) ? strtolower($server['HTTPS']) : null;
    if ($https === 'off') $https = null;
    $this->parts['scheme'] = $https ? 'https' : 'http';
  }
  protected function parseUrl($url)
  {
    $parts = parse_url($url);
    
    $this->parts = array_merge($this->parts,$parts);
    
    $this->parts['query'] = urldecode($this->parts['query']);
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
  /* =============================================
   * PSR-7 Mutators
   */
  public function withHost    ($host)     { throw \BadMethodCallException(); }
  public function withPort    ($port)     { throw \BadMethodCallException(); }
  public function withPath    ($path)     { throw \BadMethodCallException(); }
  public function withQuery   ($query)    { throw \BadMethodCallException(); }
  public function withScheme  ($scheme)   { throw \BadMethodCallException(); }
  public function withFragment($fragment) { throw \BadMethodCallException(); }
  public function withUserInfo($user, $password = null) { throw \BadMethodCallException(); }
}
