<?php
namespace Cerad\Component\Http;
error_reporting(E_ALL);

use Cerad\Component\Http\Request;
use Cerad\Component\Http\Headers;

require __DIR__  . '/../../vendor/autoload.php';

$server = $_SERVER;

// Happens using php -S
if (!isset($server['PATH_INFO']))
{
  // Request uri contains query string
  $parts = explode('?',$server['REQUEST_URI']);
  $path  = $parts[0];
  
  /* ======================================
   * Test more with web sub directories
   * And implications for generating api prefixes
   */
       
  $server = array_merge(['PATH_INFOx' => $path],$server);
}
$headers = new Headers($server);
$request = new Request($server);
?>
<html>
  <head><title>HttpWebTest</title></head>
  <body>
    <table border="1">
      <tr><th colspan="2">Request</th></tr>
        <tr><td>Path:      </td><td><?php echo $request->getPath();           ?></td></tr>
        <tr><td>Authority: </td><td><?php echo $request->getAuthority();      ?></td></tr>
        <tr><td>Method:    </td><td><?php echo $request->getMethod();         ?></td></tr>
        <tr><td>Version:   </td><td><?php echo $request->getProtocolVersion();?></td></tr>
        <tr><td>Accept:    </td><td><?php echo $request->getHeader('Accept'); ?></td></tr>
    </table>
    <table border="1">
      <tr><th colspan="2">Headers: <?php echo count($headers->get()); ?></th></tr>
      <?php foreach($headers->get() as $key => $value) { ?>
        <tr><td><?php echo $key ?></td><td><?php echo $value; ?></td></tr>
      <?php } ?>
    </table>
    <br>
    <table border="1">
      <tr><th colspan="2">_SERVER: <?php echo count($server); ?></th></tr>
      <?php foreach($server as $key => $value) { ?>
        <tr><td><?php echo $key ?></td><td><?php echo $value; ?></td></tr>
      <?php } ?>
    </table>
  </body>
</html>