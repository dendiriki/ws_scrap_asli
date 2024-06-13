<?php
ini_set( "error_reporting" , E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED );
ini_set( "error_log" , "log/php-error.log" );
ini_set( "display_errors", true );

date_default_timezone_set( "Asia/Jakarta" );  // http://www.php.net/manual/en/timezones.php

define( "ENVIRONMENT", "PRD AZURE" ); // DEV||PRD
define( "LOGIN_SESSION", "ws-scrap");
define( "MANDT", "600" );
define( "DB_DSN", "oci:dbname=(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP)(HOST = 10.1.0.18)(PORT = 1521))(CONNECT_DATA = (SID = MAKESS20)))" ); //Local
//define( "DB_DSN", "oci:dbname=(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP)(HOST = 10.164.240.103)(PORT = 1521))(CONNECT_DATA = (SID = MAKESS30)))" ); //Local
define( "DB_USERNAME", "hrpay" );
define( "DB_PASSWORD", "hrpay" );

define( "CLASS_PATH", "classes" );
define( "CONTROLLER_PATH", "controllers" );
define( "TEMPLATE_PATH", "templates" );

define( "APP_VERION", "1.0.0" );

function handleException( $exception ) {
  error_log( $exception->getMessage() );
}
 
set_exception_handler( 'handleException' );

foreach (glob(CLASS_PATH."/*.php") as $filename){
  require( $filename );
}

?>