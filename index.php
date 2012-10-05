<?php

ini_set('display_errors',0);
include_once('me.func.php');



error_reporting(E_ALL ^ E_NOTICE ^ E_USER_NOTICE ^ E_STRICT ^ E_WARNING);
define( "ROOTPATH", dirname(__FILE__)."/" );
define( "DS",DIRECTORY_SEPARATOR);
define( "PICUPLOADPATH", dirname(__FILE__).DS."upload".DS."images".DS);
define( "LOG_PATH", ROOTPATH."logs/" );
define( "FACTORY_MODE", true );
define( "START_SHOP_TIME", true );
define( "TEMPLATES_DIR", dirname(__FILE__));

@include(ROOTPATH."kernel/functions.php");
$str_base_url = HttpHandler::get_base_url();
$str_base_url .= $str_base_url[strlen($str_base_url)-1]=="/" ? "" : "/";

define( "WEB_ROOT", $str_base_url );
define("REWRITED", N("Config")->REWRITE);

define("WEB_ENTRY", REWRITED ? "" : WEB_ROOT."index.php");

N("Controller");
