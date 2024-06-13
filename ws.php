<?php
set_time_limit(0);
header('Access-Control-Allow-Origin: *');
require( "config.php" );
$action = $_GET["action"];

foreach (glob(CONTROLLER_PATH."/*.php") as $filename) {
  include $filename;
}
?>