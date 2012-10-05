<?php
/**
 * 根配置基类
 * @copyright Copyright (C) 2011, 上海包孜网络科技有限公司.
 * @license: BSD
 * @author: Luis Pater
 * @date: 2011-02-19
 * $Id: Config.class.php 5046 2012-02-22 22:31:55Z rice $
 */
class Config {
    private $REWRITE = "";
    private $DOMAIN = "";
    private $DB_HOST = "";
    private $DB_NAME = "";
    private $DB_PORT = "";
    private $DB_USER = "";
    private $DB_PASSWORD = "";

    private $CACHE_TYPE = "";
    private $CACHE_FILE = "";
    private $CACHE_HOST = "";
    private $CACHE_PORT = "";

    private $TEMPLATE_DIR = "";
    private $COMPILE_DIR = "";

    public function __get($str_name) {
        return $this->$str_name;
    }

    public function __construct() {
        if (file_exists(ROOTPATH."config/setting.php")) {
            @include(ROOTPATH."config/setting.php");
        }
        else {
            HttpHandler::redirect("/install/");
            exit;
        }
        define("DEBUG", $_GLOBAL["settings"]["debug"]);
        if (@constant("DEBUG")) {
            @ini_set("display_errors", "On");
        }
        @ini_set("memory_limit", $_GLOBAL["settings"]["memory_limit"]);
        if (PHP_SAPI !== 'cli') {
            @ini_set("max_execution_time", $_GLOBAL["settings"]["max_execution_time"]);
        }
        else {
            @ini_set("max_execution_time", 0);
        }
	    $timezone = $_GLOBAL['settings']['timezone'];
	    if($timezone){
		    date_default_timezone_set($timezone);
	    }else{
		    date_default_timezone_set('Asia/Shanghai');
	    }

        $this->REWRITE = isset($_GLOBAL["settings"]["rewrite"]) ? $_GLOBAL["settings"]["rewrite"] : false;
        $this->USE_PATH_INFO = $_GLOBAL["settings"]["use_pathinfo"];

        $this->DB_PORT = $_GLOBAL["settings"]["db_port"];
        $this->DB_NAME = $_GLOBAL["settings"]["db_name"];
        $this->DB_USER = $_GLOBAL["settings"]["db_user"];
        $this->DB_PASSWORD = $_GLOBAL["settings"]["db_pass"];

//mongodb
        $this->DB_MONGO_HOST 		= $_GLOBAL["settings"]["mongodb_host"];
        $this->DB_MONGO_PORT 		= $_GLOBAL["settings"]["mongodb_port"];
        $this->DB_MONGO_USER		= $_GLOBAL["settings"]["mongodb_user"];
        $this->DB_MONGO_PASSWORD 	= $_GLOBAL["settings"]["mongodb_pass"];

		  
		  $this->SYSTEM_FLAG 		= $_GLOBAL["settings"]["system_flag"];
		  
		  $db_flag_key					= $this->SYSTEM_FLAG;//shopex ecshop guanyib2c guanyib2b
		  //如果配置文件的 system flash 不在如下声明列表的话报错
		  if ( !in_array( $db_flag_key , array( 'shopex','ecshop','guanyib2c','guanyib2b'  ) )) {
		  		throw new Exception("db system is not in shopex ecshop guanyib2c guanyib2b", 1);
		  }
		  
	   $this->DB_HOST 			= $_GLOBAL["settings"][$db_flag_key]["db_host"];
		$this->DB_PORT 			= $_GLOBAL["settings"][$db_flag_key]["db_port"];
		$this->DB_NAME 			= $_GLOBAL["settings"][$db_flag_key]["db_name"];
		$this->DB_USER 			= $_GLOBAL["settings"][$db_flag_key]["db_user"];
		$this->DB_PASSWORD 		= $_GLOBAL["settings"][$db_flag_key]["db_pass"];
		$this->DB_PREFIX 			= $_GLOBAL["settings"][$db_flag_key]["prefix"];
		
		
		define('SYSTEM_FLAG',$this->SYSTEM_FLAG );
		define('DB_PREFIX',$this->DB_PREFIX );
		
		
        $this->CACHE_TYPE = $_GLOBAL["settings"]["cache_type"];
        switch ($_GLOBAL["settings"]["cache_type"]) {
            case "secache":
                $this->CACHE_FILE = ROOTPATH."cache/cachefile.php";
                break;
            case "memcache":
                $this->CACHE_HOST = $_GLOBAL["settings"]["memcache_host"];
                $this->CACHE_PORT = $_GLOBAL["settings"]["memcache_port"];
                break;
            case "redis":
                $this->CACHE_HOST = $_GLOBAL["settings"]["redis_host"];
                $this->CACHE_PORT = $_GLOBAL["settings"]["redis_port"];
                break;
            default:
                $this->CACHE_TYPE = "none";
                break;
        }
        $this->REDIS_HOST = $_GLOBAL["settings"]["redis_host"];
        $this->REDIS_PORT = $_GLOBAL["settings"]["redis_port"];
        $this->COMPILE_BASE_DIR = ROOTPATH."cache/smarty/";
    }
}