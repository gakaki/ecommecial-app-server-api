<?php
/**
 * 核心方法类
 * @copyright Copyright (C) 2011, 上海包孜网络科技有限公司.
 * @license: BSD
 * @author: Luis Pater
 * @date: 2011-02-19
 * $Id: functions.php 5589 2012-03-09 13:53:37Z admin $
 */
if (@constant("START_SHOP_TIME")) {
    $GLOBALS["SHOP_TIME"] = microtime(true);
}

spl_autoload_register("autoload");

/**
 * 自动加载
 * @author Luis Pater
 * @date 2011-04-15
 * @param array 类名
 */
function autoload($str_class_name) {
    if ( file_exists( ROOTPATH."kernel/".$str_class_name.".class.php" ) ) {
        include_once( ROOTPATH."kernel/".$str_class_name.".class.php" );
    }
    elseif ( file_exists( ROOTPATH."kernel/".strtolower( $str_class_name )."/".$str_class_name.".class.php" ) ){
        include_once( ROOTPATH."kernel/".strtolower( $str_class_name )."/".$str_class_name.".class.php" );
    }
    elseif ( file_exists( ROOTPATH."libs/".$str_class_name.".class.php" ) ) {
        include_once( ROOTPATH."libs/".$str_class_name.".class.php" );
    }
    elseif ( file_exists( ROOTPATH."libs/".strtolower( $str_class_name )."/".$str_class_name.".class.php" ) ){
        include_once( ROOTPATH."libs/".strtolower( $str_class_name )."/".$str_class_name.".class.php" );
    }
    elseif (isset($GLOBALS["LIB_PATH"])) {
        foreach ($GLOBALS["LIB_PATH"] as $str_path) {
            if ( file_exists( $str_path."/".$str_class_name.".class.php" ) ) {
                include_once( $str_path."/".$str_class_name.".class.php" );
            }
            elseif ( file_exists( $str_path."/".strtolower( $str_class_name )."/".$str_class_name.".class.php" ) ){
                include_once( $str_path."/".strtolower( $str_class_name )."/".$str_class_name.".class.php" );
            }
        }
    }
}

/**
 * 增加路径到autoload路径中
 *
 * @author Luis Pater
 * @date 2011-09-19
 * @param string $str_path 路径
 */
function addLibPath($str_path) {
    $GLOBALS["LIB_PATH"][] = $str_path;
}

/**
 * 实例化对象
 * @author Luis Pater
 * @date 2011-04-15
 * @param string 类名
 * @param array 参数
 * @return mixed
 */
function newClass($str_name, $array_params) {
    $obj_ref = new ReflectionClass($str_name);
    if (count($array_params)>0) {
        return $obj_ref->newInstanceArgs($array_params);
    }
    return $obj_ref->newInstance();
}

function &Q() {
    if (!isset($GLOBALS["__QueueObject"])) {
        $str_class_file = ROOTPATH."/kernel/caches/CacheRedis.class.php";
        include_once($str_class_file);
        $GLOBALS["__QueueObject"] = NN("CacheRedis");
        $GLOBALS["__QueueObject"]->setHost(N("Config")->REDIS_HOST);
        $GLOBALS["__QueueObject"]->setPort(N("Config")->REDIS_PORT);
        $GLOBALS["__QueueObject"]->connect();
    }
    return $GLOBALS["__QueueObject"];
}

function &C() {
    if (!isset($GLOBALS["__CacheObject"])) {
        $GLOBALS["__CacheObject"] = null;
        $str_cache_type = ucfirst(N("Config")->CACHE_TYPE);
        $str_class_file = ROOTPATH."/kernel/caches/Cache".$str_cache_type.".class.php";
        $str_class = "Cache".$str_cache_type;
        if (file_exists($str_class_file)) {
            include_once($str_class_file);
            $GLOBALS["__CacheObject"] = N($str_class);
            switch (N("Config")->CACHE_TYPE) {
                case "none":
                case "secache":
                     break;
                case "memcache":
                case "redis":
                    $GLOBALS["__CacheObject"]->setHost(N("Config")->CACHE_HOST);
                    $GLOBALS["__CacheObject"]->setPort(N("Config")->CACHE_PORT);
                    break;
                default:
                    break;
            }
            $GLOBALS["__CacheObject"]->connect();
            return $GLOBALS["__CacheObject"];
        }
        else {
            return false;
        }
    }
    return $GLOBALS["__CacheObject"];
}

function DB_MONGO($int_db_name = 1) {
    switch ($int_db_name) {
        case 2: $str_db_name = N("Config")->DB_NAME_DAEMON;
            break;
        case 1:
        default:
            $str_db_name = N("Config")->DB_NAME;
            break;
    }

	try {
		$mongodb_uri = N("Config")->DB_MONGO_HOST.':'.N("Config")->DB_PORT;
    	$mongodb     = N("Mongo", $mongodb_uri , array("persist" => "persist") );
    	
   		$db          = $mongodb->selectDB($str_db_name);

   		return $db;
	} catch (Exception $e) {
		throw new Exception($e, 1);
	}

}

function DB() {
    return N("Database", N("Config")->DB_HOST, N("Config")->DB_USER, N("Config")->DB_PASSWORD, N("Config")->DB_NAME, N("Config")->DB_PORT);
}
function M($str_class) {
	 if (!$str_class) {
	 	throw new Exception(" str class argumnt must be fill you need fill the class name ", 1);
	 }
    $system_flag = SYSTEM_FLAG;
    $str_class= strtolower($str_class);
    $class_invoke_name = "api.model.$system_flag.$str_class";
    return  L($class_invoke_name);
}
function &A($str_name) {
    $str_name .= "Action";
    if (!isset($__Object)) {
        static $__Object = array();
    }

    $array_params = array();
    $str_params = "";
    for ($int_i = 1; $int_i < func_num_args(); $int_i++) {
        $array_params[] = func_get_arg($int_i);
        $str_params .= var_export(func_get_arg($int_i), true);
    }

    $str_store_name = md5($str_name, $str_params);
    if (!isset($__Object[$str_store_name])) {
        $__Object[$str_store_name] = newClass($str_name, $array_params);
    }
    return $__Object[$str_store_name];
}

/**
 * 实例化持久对象，该初始化函数为不定长参数，可根据类实例化操作进参，如果需要一个新对象，请使用方法NN
 * @author Luis Pater
 * @date 2011-04-15
 * @param string 类名
 * @param boolean 是否初始化对象
 * @return mixed
 */
function &N($str_name) {
    if (!isset($__Object)) {
        static $__Object = array();
    }

    $array_params = array();
    $str_params = "";


    for ($int_i = 1; $int_i < func_num_args(); $int_i++) {
        $array_params[] = func_get_arg($int_i);
        $str_params .= var_export(func_get_arg($int_i), true);
    }

    $str_store_name = md5($str_name, $str_params);
    if (!isset($__Object[$str_store_name])) {
        $__Object[$str_store_name] = newClass($str_name, $array_params);
    }

    return $__Object[$str_store_name];
}



/**
 * 与N方法一致，但是每次新建对象，该初始化函数为不定长参数，可根据类实例化操作进参
 * @author Luis Pater
 * @date 2011-04-15
 * @param string 类名
 * @param boolean 是否初始化对象
 * @return mixed
 */
function &NN($str_name) {
    $array_params = array();
    for ($int_i = 1; $int_i < func_num_args(); $int_i++) {
        $array_params[] = func_get_arg($int_i);
    }
    return newClass($str_name, $array_params);
}

/**
 * 过滤数组
 * @author Luis Pater
 * @date 2010-12-19
 * @param array 数组
 * @param array 过滤列表
 * @return array
 */
function key_filter(&$array_params, $array_keys) {
    $array_result = array();
    foreach ($array_keys as $str_key) {
        if (array_key_exists($str_key, $array_params)) {
            $array_result[$str_key] = $array_params[$str_key];
        }
    }


    $array_params = $array_result;
}

/**
 * 写日志
 * @author Luis Pater
 * @date 2011-04-15
 * @param string 日志内容
 * @param string 日志文件名
 */
function writeLog($str_content, $str_log_file) {
    error_log(date("c")."\t".$str_content."\n", 3, LOG_PATH.$str_log_file);
}

/**
 * 转换数组为XML文档
 *
 * @author Luis Pater
 * @date 2009-05-07
 * @param array 需要转换的数组
 * @param boolean 字符串是否要适用CDATA标签
 * @param string 根节点名称 default: shopex
 * @param SimpleXMLElement 复用simpleXML节点对象
 * @return string 输出的XML
 */
function toXml($data, $use_cdata = true, $rootNodeName = 'baozi', $xml=null) {
    if (ini_get('zend.ze1_compatibility_mode') == 1) {
        ini_set('zend.ze1_compatibility_mode', 0);
    }

    if ($xml == null) {
        $xml = simplexml_load_string("<?xml version='1.0' encoding='UTF-8'?><$rootNodeName />");
    }

    foreach($data as $key => $value) {
        if (is_numeric($key)) {
            $key = "_item_";
        }

        $key = preg_replace('/[^0-9a-zA-Z\_]/i', '', $key);

        if (is_array($value)) {
            $node = $xml->addChild($key);
            toXml($value, $use_cdata, $rootNodeName, $node);
        }
        elseif (is_bool($value)) {
            if ($value) {
                $xml->addChild($key, "true");
            }
            else {
                $xml->addChild($key, "false");
            }
        }
        elseif (is_numeric($value)) {
            $xml->addChild($key, $value);
        }
        elseif (strlen($value)==0) {
            $xml->addChild($key);
        }
        else {
            if ($use_cdata) {
                $node = $xml->addChild($key);
                $node = dom_import_simplexml($node);
                $no = $node->ownerDocument;
                $node->appendChild($no->createCDATASection($value));
            }
            else {
                $xml->addChild($key, $value);
            }

        }
    }

    $str_xml_replace = $xml->asXML();
    do {
        $str_xml = $str_xml_replace;
        $str_xml_replace = str_replace('<_item_><_item_>', '<_item_>', $str_xml_replace);
        $str_xml_replace = str_replace('</_item_></_item_>', '</_item_>', $str_xml_replace);
    }
    while ($str_xml!=$str_xml_replace);
    return $str_xml_replace;
}

/**
 * 转换XML文档为数组
 *
 * @author Luis Pater
 * @date 2011-09-06
 * @param string xml内容
 * @return mixed 返回的数组，如果失败，返回false
 */
function xml2array($xml) {
    $xml = simplexml_load_string($xml, "SimpleXMLElement", LIBXML_NOCDATA);
    return simplexml2array($xml);
}

/**
 * 转换XML文档为数组（辅助方法）
 *
 * @author Luis Pater
 * @date 2011-09-06
 * @param string xml内容
 * @return mixed 返回的数组，如果失败，返回false
 */
function simplexml2array($a) {
    if (is_object($a)) {
        settype($a, "array");
    }
    foreach ($a as $k=>$v) {
        if ((count($a)==1) && ($k=="_item_")) {
            if (is_array($v)) {
                $a = simplexml2array($v);
            }
            elseif (is_object($v)) {
                $a = simplexml2array($v);
            }
            else {
                $a = array($v);
            }
        }
        else {
            if (is_array($v)) {
                if (count($v)) {
                    $a[$k] = simplexml2array($v);
                }
                else {
                    $a[$k] = "";
                }
            }
            elseif (is_object($v)) {
                if (count($v)) {
                    $a[$k] = simplexml2array($v);
                }
                else {
                    $a[$k] = "";
                }
            }
        }
    }
    return $a;
}

/**
 * 判断字符是否是整形
 *
 * @author Luis Pater
 * @date 2009-05-11
 * @param string 需要判断的字符
 * @return boolean
 */
function isInt($str_text) {
    $str_preg = '/^(\d+)$/';
    if ($int_matched = preg_match($str_preg, $str_text, $array_match)) {
        return true;
    }
    return false;
}

/**
 * 判断字符是否是浮点数
 *
 * @author Luis Pater
 * @date 2009-05-11
 * @param string 需要判断的字符
 * @param string 浮点数长度标示，例如：8,2
 * @return boolean
 */
function isFloat($str_text, $str_len) {
    $int_int = substr($str_len, 0, strpos($str_len, ","));
    $int_float = substr($str_len, strpos($str_len, ",")+1);
    $str_preg = '/^(\d{1,'.$int_int.'})\.?(\d{1,'.$int_float.'})?$/';
    if ($int_matched = preg_match($str_preg, $str_text, $array_match)) {
        return true;
    }
    return false;
}

/**
 * 判断字符是否在枚举类型中
 *
 * @author Luis Pater
 * @date 2009-05-11
 * @param string 需要判断的字符
 * @param string 枚举文本，例如：enum('a', 'b', 'c')
 * @return boolean
 */
function inEnum($str_text, $str_enum) {
    $str_enum = str_replace("enum", "array", $str_enum);
    eval('$array_enum = '.$str_enum.';');
    if (in_array($str_text, $array_enum)) {
        return true;
    }
    return false;
}

function unixSeparator($str_path) {
    return str_replace(DIRECTORY_SEPARATOR, "/", $str_path);
}

/**
 * 数组过滤，只保留指定的key
 *
 * @author Luis Pater
 * @date 2009-05-11
 * @param array $array_data 数据
 * @param array $array_filter key列表
 * @return array 过滤完的数据
 */
function data_filter($array_data, $array_filter) {
    return array_intersect_key($array_data, array_flip($array_filter));
}

/**
 * 格式化XML
 *
 * @author Luis Pater
 * @date 2011-09-15
 * @param string $str_xml xml数据
 * @return string 格式化完的xml
 */
function formatXML($str_xml) {
    $obj_doc = N("DOMDocument");
    $obj_doc->preserveWhiteSpace = false;
    $obj_doc->formatOutput = true;
    $obj_doc->loadXML($str_xml);
    return $obj_doc->saveXML();
}


//===========1.5结构方法==================
/**
 * 分割包名称为数组
 *
 * 拆解字符串
 * 如果包含括号，则将最后一个元素拆解成方法名，剔除最后一个元素
 * 将所有元素倒置后首字母大写，组合成类名
 * 将第一个元素拆解成类文件名
 *
 * @author Luis Pater
 * @date 2011-11-12
 * @param string 包名称
 * @return array class_path 不含类名方法名的路径层级 class_file 类文件名 class 类名 method 方法名
 */
function splite($str_name) {
	$array_class_path = explode(".", $str_name);

	if (substr($str_name, -2)=="()") {
        $str_last_name = array_pop($array_class_path);
        $str_method = substr($str_last_name, 0, -2);
    }else {
        $str_method = null;
    }

    $array_ucfirst_class_path = array_map(ucfirst, array_reverse($array_class_path));
    $str_class_file = ucfirst($array_ucfirst_class_path[0]);
    $str_class = implode($array_ucfirst_class_path);

	unset($array_class_path[count($array_class_path)-1]);

	return array("class_path"=>$array_class_path, "class_file"=>$str_class_file, "class"=>$str_class, "method"=>$str_method);
}

/**
 * 导入Action Module，并且返回方法名称
 * @author Luis Pater
 * @date 2011-11-12
 * @param string action value source urls.xml
 * @param string 方法名称（引用返回）
 * @return object
 */
function P($str_name, &$str_method = null) {
	$array_splite = splite($str_name.'()');
	$str_path = implode("/", $array_splite["class_path"]);
	$str_method = $array_splite["method"];
	//找packages下对应配置文件
	$str_file = ROOTPATH."packages/".ltrim($str_path, "/").".php";

	if (file_exists($str_file)) {
		include_once($str_file);
		foreach ($bunshop["module"] as $array_module) {
			if ($array_module["name"]==$array_splite["class_file"]) {
				foreach ($array_module["event"] as $array_event) {
					if ($array_event["name"]==$array_splite["method"]) {
						$array_event_splite = splite($array_event["physical"]);
						$str_event_path = implode("/", $array_event_splite["class_path"]);

						$obj_class = importAction(ROOTPATH."modules/".$str_event_path."/".$array_event_splite["class_file"].".class.php", $array_event_splite["class"]);
						if (method_exists($obj_class, $array_event_splite["method"])) {
							return array("class"=>$obj_class, "method"=>$array_event_splite["method"], "views"=>$array_event["view"]);
						}
						else {
							trigger_error("Method ".$array_event_splite["method"]." not found.", E_USER_ERROR);
						}
					}
				}
			}
		}
		//trigger_error("module ".$array_splite["class_file"]." is not exists.", E_USER_ERROR);
	}
	else {
		//trigger_error("package file ".$str_file." is not exists.", E_USER_ERROR);
	}
}

/**
 * 导入动作包
 * @author Luis Pater
 * @date 2011-11-12
 * @param string 包名称
 * @return object
 */
function AA($str_name, &$str_method = NULL) {
	$array_splite = splite($str_name);
	$str_path = implode("/", $array_splite["class_path"]);
	if (is_null($array_splite["method"])) {
		$str_method = NULL;
	}
	else {
		$str_method = $array_splite["method"];
	}
	return importAction(ROOTPATH."modules/".$str_path."/".$array_splite["class_file"].".class.php", $array_splite["class"]);
}

/**
 * 导入库包
 * @author Luis Pater
 * @date 2011-11-12
 * @param string 包名称
 * @return object
 */
function L($str_name, &$str_method = NULL, $array_params = array()) {
	$array_splite = splite($str_name);
	$str_path = implode("/", $array_splite["class_path"]);
	if (is_null($array_splite["method"])) {
		$str_method = NULL;
	}
	else {
		$str_method = $array_splite["method"];
	}
	return importLibary(ROOTPATH."modules/".$str_path."/".$array_splite["class_file"].".class.php", $array_splite["class"], $array_params);
}

/**
 * 导入视图包
 * @author Luis Pater
 * @date 2011-11-12
 * @param string 包名称
 * @return object
 */
function V($str_name, &$str_method = NULL) {
	$array_splite = splite($str_name);
	$str_path = implode("/", $array_splite["class_path"]);
	if (is_null($array_splite["method"])) {
		$str_method = NULL;
	}
	else {
		$str_method = $array_splite["method"];
	}
	return importView(ROOTPATH."modules/".$str_path."/".$array_splite["class_file"].".class.php", $array_splite["class"]);
}

/**
 * 导入Action Module
 * @author Luis Pater
 * @date 2011-11-12
 * @param string 文件路径
 * @param string 类名称
 * @return object 堆栈对象
 */
function importAction($str_file, $str_class) {
	if (file_exists($str_file)) {
		include_once($str_file);
		if (is_subclass_of($str_class, "NActionModule")) {
			if (class_exists($str_class)) {
				return N($str_class);
			}
			else {
                throw new Exception("Class ".$str_class." not found.");
			}
		}
		else {
            throw new Exception("Class ".$str_class." is not a ActionModule.");
		}
	}
	else {
		//var_dump($str_file, $str_class);
		throw new Exception("Action module file ".$str_file." is not exists.");
	}
}

/**
 * 导入View Module
 * @author Luis Pater
 * @date 2011-11-12
 * @param string 文件路径
 * @param string 类名称
 * @return object view module对象
 */
function importView($str_file, $str_class) {
	if (file_exists($str_file)) {
		include_once($str_file);
		if (is_subclass_of($str_class, "NViewModule")) {
			if (class_exists($str_class)) {
				return N($str_class);
			}
			else {
				trigger_error("Class ".$str_class." not found.", E_USER_ERROR);
			}
		}
		else {
			trigger_error("Class ".$str_class." is not a ViewModule.", E_USER_ERROR);
		}
	}
	else {
		trigger_error("view module file ".$str_file." is not exists.", E_USER_ERROR);
	}
}

function &LN($str_name, $array_params = array()) {
    if (!isset($__Object)) {
        static $__Object = array();
    }

    $str_params = var_export($array_params, true);
    $str_store_name = md5($str_name, $str_params);
    if (!isset($__Object[$str_store_name])) {
        $__Object[$str_store_name] = newClass($str_name, $array_params);
    }

    return $__Object[$str_store_name];
}


/**
 * 导入Libary Module
 * @author Luis Pater
 * @date 2011-11-12
 * @param string 文件路径
 * @param string 类名称
 * @return object view module对象
 */
function importLibary($str_file, $str_class, $array_params) {
    if (file_exists($str_file)) {
        include_once($str_file);
        if (class_exists($str_class)) {
            return LN($str_class, $array_params);
        }
        else {
            trigger_error("Class ".$str_class." not found.", E_USER_ERROR);
        }
    }
    else {
        trigger_error("libary module file ".$str_file." is not exists.", E_USER_ERROR);
    }
}

/**
 * 标准化返回
 *
 * @author Luis Pater
 * @date 2011-11-14
 * @return string 返回状态
 */
function result($str_status) {
	$array_params = array();
	for ($int_i = 1; $int_i < func_num_args(); $int_i++) {
		$array_params[] = func_get_arg($int_i);
	}

	return array("_status"=>$str_status, "_data"=>$array_params);
}



/**
 * HTML标签检查关闭
 *
 * @author Luis Pater
 * @date 2012-02-20
 * @param string HTML原文
 * @return string 返回状态
 */
function closeTags($html) {
    // 不需要补全的标签
    $arr_single_tags = array('meta','img','br','link','area');
    // 匹配开始标签
    preg_match_all('#<([a-z]+)(?: .*)?(?<![/|/ ])>#iU',$html,$result);
    $openedtags = $result[1];
    // 匹配关闭标签
    preg_match_all('#</([a-z]+)>#iU',$html,$result);
    $closedtags = $result[1];
    // 计算关闭开启标签数量，如果相同就返回html数据
    $len_opened = count($openedtags);
    if (count($closedtags) == $len_opened) {
        return $html;
    }
    // 把排序数组，将最后一个开启的标签放在最前面
    $openedtags = array_reverse($openedtags);
    // 遍历开启标签数组
    for($i=0; $i<$len_opened; $i++) {
        // 如果标签不属于需要不全的标签
        if(!in_array($openedtags[$i],$arr_single_tags)) {
            // 如果这个标签不在关闭的标签中
            if(!in_array($openedtags[$i],$closedtags)) {
                // 如果在这个标签之后还有开启的标签
                if(isset($openedtag[$i+1]) && $next_tag = $openedtags[$i+1]) {
                    // 将当前的标签放在下一个标签的关闭标签的前面
                    $html = preg_replace('#</'.$next_tag.'#iU','</'.$openedtags[$i].'></'.$next_tag,$html);
                }
                else {
                    // 直接补全闭合标签
                    $html .= '</'.$openedtags[$i].'>';
                }
            }
        }
    }
    return $html;
}
