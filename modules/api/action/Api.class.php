<?php
class ApiActionApi extends NActionModule {
    /**
     * 子控制器代码
     *
     * @author Luis Pater
     * @date 2011-11-29
     * @return array 标准流程返回结构
     */
    public function subcontroler() {
        $str_action = $_REQUEST["method"];
        return $this->runSubControler($str_action);
    }

    protected function runSubControler($str_method) {
			
    	/*	
        if (!L("api.logic.api")->checkSign($_POST, $array_cert_info["TOKEN"])) {
            return result("API_ERROR", "Request hash invaild.");
        }
        

        if (abs(time()-strtotime($_POST["ts"]))>300) {
            return result("API_ERROR", "Timestamp error, please check localtime.");
        }
*/

        if (!method_exists($this, $str_method)) {

            try {
                $array_method = explode(".", $str_method);

                if (count($array_method)>=2) {
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
                    $array_full_path = array_reverse($array_full_path);
                    array_pop($array_full_path);
                    $array_subaction = array_merge($array_full_path, $array_method);
                    $str_full_path = implode(".", $array_subaction)."()";

                    return AA($str_full_path, $str_method)->$str_method();
                }
            }
            catch (Exception $e) {
                ChromePhp::log('exception in api',$e);
                throw new Exception("API_ERROR but Exception is ".$e);              
            }
            return result("API_ERROR", "Method ".$str_method." is not exits.");
        }
        return $this->$str_method();
    }
}