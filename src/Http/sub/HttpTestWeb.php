<?php
namespace Cerad\Component\Http;
error_reporting(E_ALL);

use Cerad\Component\Http\Headers;

require __DIR__  . '/../../vendor/autoload.php';

$server = $_SERVER;

// Happens using php -S
if (!isset($server['PATH_INFO']))
{
  // Request uri contains query string
  $parts = explode('?',$server['REQUEST_URI']);
  $path = $parts[0];
  
  $pos = strpos($server['REQUEST_URI'], $server['QUERY_STRING']);
   
  //$x1 = substr($server['REQUEST_URI'], 0, $pos - 2);
  //$x2 = substr($x1, strlen($server['SCRIPT_NAME']) + 1);
       
  $server = array_merge(['PATH_INFOx' => $path],$server);
}
$headers = new Headers($server);
?>
<html>
  <head><title>HttpWebTest</title></head>
  <body>
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