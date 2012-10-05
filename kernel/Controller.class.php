<?php
/**
 * 控制器
 * @copyright Copyright (C) 2011, 上海包孜网络科技有限公司.
 * @license: BSD
 * @author: Luis Pater
 * @date: 2011-02-19
 * $Id: Controller.class.php 5620 2012-03-12 11:49:43Z admin $
 */
class Controller {

    private $url_pattern = null;
    private $run_time_start = 0;

    public function __construct() {
        
        @session_start();

        $this->url_pattern = N("UrlPatterns")->getUrlPattern(HttpHandler::get_path_info());
       
        //这个p方法用来定位的 这里可以写上版本号来对应版本来着
        $array_stack = P($this->url_pattern, $str_method);

        if('NActionModule' == get_parent_class($array_stack["class"])) {
   
            $array_result = $array_stack["class"]->$array_stack["method"]();
            
            if (is_array($array_result) && isset($array_result["status"]) && isset($array_result["data"])) {
            	 
                if (isset($array_stack["views"][$array_result["status"]])) {
                    $obj_view = V($array_stack["views"][$array_result["status"]], $str_method);
              
                    if (method_exists($obj_view, $str_method)) {
                        if (!is_null($str_method)) {
                     		 
                            call_user_func_array(array($obj_view, $str_method), is_array($array_result["data"]) ? $array_result["data"] : array($array_result["data"]));
                        }
                        else {
                            trigger_error("I don't know which method to call.", E_USER_ERROR);
                        }
                    }
                    else {
                        trigger_error("Method ".$str_method." isn't exist in class ".get_class($obj_view).".", E_USER_ERROR);
                    }
                }
                elseif (isset($array_stack["views"]["DEFAULT"])) {
                    $obj_view = V($array_stack["views"]["DEFAULT"], $str_method);
                    if (method_exists($obj_view, $str_method)) {
                        if (!is_null($str_method)) {
                            call_user_func_array(array($obj_view, $str_method), is_array($array_result["data"]) ? $array_result["data"] : array($array_result["data"]));
                        }
                        else {
                            trigger_error("I don't know which method to call.", E_USER_ERROR);
                        }
                    }
                    else {
                        trigger_error("Method ".$str_method." isn't exist in class ".get_class($obj_view).".", E_USER_ERROR);
                    }
                }
                else {
                    
                    trigger_error("Action module result a unknow status.", E_USER_ERROR);
                }
            }
            else {
                trigger_error("Controller result is not a vaild value.", E_USER_ERROR);
            }
        }
    }
}