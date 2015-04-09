<?php
$server = $_SERVER;

// http://php.net/manual/en/reserved.variables.server.php
$server['SCHEME'] = 'http';
if (isset($server['HTTPS']) && $server['HTTPS'] != 'off') $server['SCHEME'] = 'https';

function row($key,$value)
{
  switch($key)
  {
    case 'DOCUMENT_ROOT':
    case 'REMOTE_ADDR':
    case 'REMOTE_PORT':
    case 'SERVER_SOFTWARE':
    case 'SCRIPT_NAME':
    case 'SCRIPT_FILENAME':
    case 'PHP_SELF':
    case 'HTTP_CONNECTION':
  //case 'HTTP_CACHE_CONTROL':
  //case 'HTTP_ACCEPT':
  //case 'HTTP_USER_AGENT':
    case 'HTTP_ACCEPT_ENCODING':
    case 'HTTP_ACCEPT_LANGUAGE':
    case 'REQUEST_TIME_FLOAT':
    case 'REQUEST_TIME':
      return;
      
    // http://local.ang2.zayso.org:8002/xxx/123?role=xxx
    case 'SERVER_PROTOCOL': // HTTP/1.1
    case 'SERVER_NAME':     // local.ang2.zayso.org
    case 'SERVER_PORT':     // 8002
    case 'REQUEST_URI':     // /xxx/123?role=xxx
    case 'REQUEST_METHOD':  // GET
    case 'PATH_INFO':       // /xxx/123 (always at least /
    case 'QUERY_STRING':    // role=xxx
    case 'HTTP_HOST':       // local.ang2.zayso.org:8002 - required for HTTP 1.1
    case 'HTTP_ACCEPT':     // text/html,application/xml;q=0.9,image/webp,*/*;q=0.8
    case 'HTTP_USER_AGENT': // Mozilla/5.0 Chrome/41.0.2272.101 Safari/537.36
    case 'SCHEME':
      break;
    default:
      sprintf("<tr><td>%s</td><td>%s</td></tr>\n",$key,'UNEXPECTED KET');
  }
  echo sprintf("<tr><td>%s</td><td>%s</td></tr>\n",$key,$value);
}
echo "<table border='1'>\n";
foreach($server as $key => $value)
{
  row($key,$value);
}
if (!isset($server['PATH_INFO']))    row('PATH_INFO','NOT SET');
if (!isset($server['QUERY_STRING'])) row('QUERY_STRING','NOT SET');
echo "</table>\n";

/* 
 * REQUEST_URI /sub/server.php/xxx/123?role=xxx
 * PATH_INFO   /xxx/123
 * 
 * To generate a prefix, drop the query string then strip the path_info
 * Not positive about this but does not really matter
 * 
 * Use php://input to retrieve raw content (json and forms?
 * file_get_contents('php://input');
 * Not available with:  enctype="multipart/form-data"
 * enable_post_data_reading to off
 */