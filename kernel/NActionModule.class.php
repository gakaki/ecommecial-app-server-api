<?php
class NActionModule {

	public function __construct(){}

    protected function registerSubControler($str_base_url) {
        $str_param_path = substr(HttpHandler::get_path_info(), strlen($str_base_url));
        $array_param_path = explode("/", trim($str_param_path, "/"));

        $array_stack = debug_backtrace(); //这是很奇怪的写法，原因是func_num_args不支持引用，referer: http://cstruter.com/blog/144
        for ($int_i = 1; $int_i < count($array_stack[0]["args"]); $int_i++) {
            $array_stack[0]["args"][$int_i] = $array_param_path[$int_i-1];
        }
    }

    protected function runSubControler($str_class, $str_method) {
        $str_self_name = get_class($this);
        $array_full_path = array();
        for ($int_i=0; $int_i<strlen($str_self_name); $int_i++) {
            if ($str_self_name[$int_i]>="A" && $str_self_name[$int_i]<="Z") {
                $array_full_path[count($array_full_path)] = strtolower($str_self_name[$int_i]);
            }
            else {
                $array_full_path[count($array_full_path)-1] .= $str_self_name[$int_i];
            }
        }
        $str_full_path = implode(".", array_reverse($array_full_path));
        //default load subControler's method __default
        if(!$str_method) {
            $str_method = '__default';
        }
        return AA($str_full_path.".".$str_class)->$str_method();
    }
}