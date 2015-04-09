<?php

interface Psr7
{
  // MessageInterface
  public function  getProtocolVersion();
  public function withProtocolVersion($version);
  
  public function  getBody();  
  public function withBody(StreamableInterface $body);
  
  public function  getHeaders();
  public function  hasHeader     ($name);
  public function  getHeader     ($name); // Comma delimited
  public function  getHeaderLines($name);
  public function withHeader     ($name, $value);
  public function withAddedHeader($name, $value);
  
  // RequestInterface extends MessageInterface
  public function getHeadersHost    ();  
  public function  getHeaderHost    ($name);
  public function getHeaderLinesHost($name);
  
  public function  getRequestTarget();
  public function withRequestTarget($requestTarget);
  
  public function  getMethod();
  public function withMethod($method);
  
  public function  getUri();
  public function withUri(UriInterface $uri);
  
  // ServerRequestInterface extends RequestInterface
  public function  getServerParams();
  public function  getCookieParams();
  public function withCookieParams(array $cookies);
  public function  getFileParams  ();
  
  public function  getQueryParams ();
  public function withQueryParams (array $query);
  
   public function  getParsedBody();
   public function withParsedBody($data);
   
   public function     getAttributes();
   public function     getAttribute ($name, $default = null);
   public function    withAttribute ($name, $value);
   public function withoutAttribute ($name); // ???
   
   // ResponseInterface extends MessageInterface
   public function  getStatusCode();
   public function  getReasonPhrase();
   public function withStatus($code, $reasonPhrase = null);
   
   // UriInterface
   public function getScheme();
   public function getAuthority();
   public function getUserInfo();
   public function getHost();
   public function getPort();
   public function getPath();
   public function getQuery();
   public function getFragment();
   
   public function withScheme  ($scheme);
   public function withUserInfo($user, $password = null);
   public function withHost    ($host);
   public function withPort    ($port);
   public function withPath    ($path);
   public function withQuery   ($query);
   public function withFragment($fragment);
   
   public function __toString();
   
   // StreamableInterface
   public function getContents();
   public function getMetadata($key = null);
   
   public function __toStringx();
   public function close();
   public function detach();
   public function getSize();
   public function tell();
   public function eof();
   public function isSeekable();
   public function isWritable();
   public function isReadable();
   
   public function seek($offset, $whence = SEEK_SET);
   public function rewind();
   public function write($string);
   public function read($length);
}