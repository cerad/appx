<?php
namespace Cerad\Component\Http;

error_reporting(E_ALL);
require __DIR__  . '/../../vendor/autoload.php';

use Cerad\Component\Http\Request;
use Cerad\Component\Http\Response;

use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

$server = $_SERVER;

/* ======================================================
 * 1. Sending no headers, have the same headers as S2 text/html and Host
 * 2. Missing Cache-Control and date
 * 3. Added date
 * 4. If I don't send Content-Type then server adss in text/html
 * 5. Can;t figutr out where Cache-Control is being set so just added it
 */
$response = new Response('My Response');
$response->send();
return;

/* =======================================================
 * Symfony 2 test
 */
$symfonyResponse = new SymfonyResponse('Symfony Response');
$symfonyResponse->send();

/* General
 * Remote Address:127.0.0.1:8008
   Request URL:http://local.ang2.zayso.org:8008/referees/1?role=sra
   Request Method:GET
   Status Code:200 OK
 * 
 * Response Headers
   Cache-Control:no-cache
   Connection:close
   Content-type:text/html
   Date:Sun, 05 Apr 2015 01:14:33 GMT
   Host:local.ang2.zayso.org:8008
   X-Powered-By:PHP/5.4.19
 * 
 * Request Headers
   Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp
   Accept-Encoding:gzip, deflate, sdch
   Accept-Language:en-US,en;q=0.8
   Cache-Control:max-age=0
   Connection:keep-alive
   Host:local.ang2.zayso.org:8008
   User-Agent:Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.101 Safari/537.36
 * 
 * Query String Parameters
   role:sra
 */