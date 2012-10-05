<?php
class ApiLogicApi extends LibaryModule {
    public function checkSign($array_data, $str_token) {
        if (isset($array_data["hash"])) {
            $str_hash = $array_data["hash"];
            unset($array_data["hash"]);
            ksort($array_data);

            $str_verfy_string = "";
            foreach ($array_data as $str_key=>$str_value) {
                $str_verfy_string .= $str_key.$str_value;
            }
            return $str_hash==strtoupper(md5($str_token.$str_verfy_string.$str_token));
        }
        return false;
    }
}