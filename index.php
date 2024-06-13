<?php
set_time_limit(0);
require( "config.php" );
session_start();
$media = isset($_GET["media"]) ? $_GET["media"] : "display";
$action = isset($_GET["action"]) ? strtolower($_GET["action"]) : "home";
$user = new User();
if(!isset($_SESSION[LOGIN_SESSION])) {
  $action = "login";
}
foreach (glob(CONTROLLER_PATH."/*.php") as $filename) {
  include $filename;
}
?>