<?php
$_GLOBAL["settings"] = array(

	 "system_flag" => 'shopex', // shopex,ecshop,guanyib2b,guanyib2c

    //数据库设置
    "db_host" => "localhost",
    "db_port" => "3306",
    "db_name" => "service",
    "db_user" => "root",
    "db_pass" => "",
    "prefix"  => "spl_",

    //PHP环境设置
    "timezone" => "Asia/Shanghai",
    "memory_limit" => "512M",
    "max_execution_time" => "300",
    //系统设置
    "debug" => true,
    "rewrite" => true,
    //缓存设置
    "cache_type" => "none",//none,secache,memcache,redis
    "memcache_host" => "127.0.0.1",
    "memcache_port" => "11211",
    "redis_host" => "localhost",
    "redis_port" => "6379",


    //mongodb
    "mongodb_host" => "192.168.0.154",
    "mongodb_port" => "27017",
    "mongodb_user" => "root",
    "mongodb_pass" => "root",


    //shopex
    'shopex'=> array(

	    "db_host" => "localhost",
	    "db_port" => "3306",
	    "db_name" => "shopex",
	    "db_user" => "root",
	    "db_pass" => "",
	    "prefix"  => "sdb_"

    ),


    //ecshop
    'ecshop'=> array(
	    "db_host" => "localhost",
	    "db_port" => "3306",
	    "db_name" => "ec",
	    "db_user" => "root",
	    "db_pass" => "",
	    "prefix"  => "ecs_"
    ),


    //guanyib2c
    'guanyib2c'=> array(
	    "db_host" => "localhost",
	    "db_port" => "3306",
	    "db_name" => "muses",
	    "db_user" => "root",
	    "db_pass" => "",
	    "prefix"  => "cbd_"
    ),

    //guyanyib2b
    'guyanyib2b'=> array(
	    "db_host" => "localhost",
	    "db_port" => "3306",
	    "db_name" => "b2b",
	    "db_user" => "root",
	    "db_pass" => "",
	    "prefix"  => "sdb_"
    )



);
