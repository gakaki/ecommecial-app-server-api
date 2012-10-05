<?php
/**
 * 淘宝通讯控制基类
 * @copyright Copyright (C) 2011, 上海管易软件科技有限公司.
 * @license: MIT
 * @author: Luis Pater
 * @date: 2012-04-06
 * $Id$
 */
class TopCommunications extends Communications {
    private $app_key = "";
    private $app_secret = "";
    private $app_taobao_api_domain = "";
    private $session_key = "";

    public function __construct($str_session_key) {
        $ary_thd_config = N('SysConfig')->getGroupConfig('SITE');
        $this->app_key = $ary_thd_config['taobao_app_key'];
        $this->app_secret = $ary_thd_config['taobao_app_secret'];
        $this->app_taobao_api_domain = $ary_thd_config['taobao_api_domain'];
        $this->session_key = $str_session_key;
        parent::__construct();
    }

    public function requestAPI($str_method, $array_request) {
        $array_params = $this->createRequestParams($str_method, $array_request);
        $array_params["sign"] = $this->generateSign($array_params);
        $str_result = $this->httpPostRequest("http://".$this->app_taobao_api_domain."/router/rest", $array_params, array(), false);
        $array_result = json_decode($str_result, true);
        if (isset($array_result["error_response"])) {
            if ($array_result["error_response"]["code"]==27) {
                return false;
            }
        }
        return $array_result;
    }

    public function createRequestParams($str_method, $array_params) {
          $array_params['app_key'] = $this->app_key;
          $array_params['method'] = $str_method;
          $array_params['format'] = 'json';
          $array_params['v'] = '2.0';
          $array_params['timestamp'] = date("Y-m-d H:i:s");
          $array_params['session'] = $this->session_key;
          $array_params['sign_method'] = 'md5';
          return $array_params;
    }

    /**
     * 生成验证串
     *
     * @author Luis Pater
     * @date 2009-08-27
     * @param array 需要生成验证串的数据
     * @return string 验证串
     */
    public function generateSign($array_data, $str_token = ""){
        ksort($array_data);
        $str_verfy_string = "";
        foreach ($array_data as $str_key=>$str_value) {
            $str_verfy_string .= $str_key.$str_value;
        }
        return strtoupper(md5($this->app_secret.$str_verfy_string.$this->app_secret));
    }

}
