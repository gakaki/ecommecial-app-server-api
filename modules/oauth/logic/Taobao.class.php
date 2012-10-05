<?php
/**
 * 淘宝OAuth 2.0登录
 *
 * @package oauth
 * @stage 1.0
 * @author Luis Pater
 * @date 2012-03-09
 * @license MIT
 * @copyright Copyright (C) 2011, Shanghai GuanYiSoft Co., Ltd.
 */
class TaobaoLogicOauth extends LibaryModule {
    private $app_key = "";
    private $app_oauth_domain = "";
    private $app_secret = "";
    private $callback_url = "";

    public function __construct() {
        $this->callback_url = "http://".$_SERVER["HTTP_HOST"].WEB_ENTRY."/oauth.php";
        $ary_thd_config = N('SysConfig')->getGroupConfig('SITE');
        $this->app_key = $ary_thd_config['taobao_app_key'];
        $this->app_secret = $ary_thd_config['taobao_app_secret'];
        
        $this->app_key = '12000273';
        $this->app_secret = '917f40791f9d22e100a0b8ea48c8f87a';
        
        $this->app_oauth_domain = $ary_thd_config['taobao_oauth_domain'];
    }

    public function createOAuthUrl($int_oa_id) {
        return "https://".$this->app_oauth_domain."/authorize?response_type=code&client_id=".$this->app_key."&redirect_uri=".$this->callback_url."&state=".$int_oa_id;
    }

    public function getSessionInfo($str_token) {
        $obj_conn = N("Communications");
        $array_params = array();
        $array_params["grant_type"] = "authorization_code";
        $array_params["code"] = $str_token;
        $array_params["redirect_uri"] = $this->callback_url;
        $array_params["client_id"] = $this->app_key;
        $array_params["client_secret"] = $this->app_secret;

        $str_json = $obj_conn->httpPostRequest("https://".$this->app_oauth_domain."/token", $array_params, array(CURLOPT_SSL_VERIFYPEER=>0, CURLOPT_SSL_VERIFYHOST=>0), false);
     
        $array_result = json_decode($str_json, true);

        if (!isset($array_result["error"])) {
            return $array_result;
        }
        return false;
    }

    public function buildResultUrl($str_callback_url, $array_params) {
        $str_result = $str_callback_url.((strpos($str_callback_url, "?")===false) ? "?" : "&");
        $array_param = array();
        foreach ($array_params as $str_key=>$str_value) {
            $array_param[] = $str_key."=".rawurlencode($str_value);
        }
        return $str_result.implode("&", $array_param);
    }

    public function getDoneUrl($int_state) {
        $str_url = "https://".$this->app_oauth_domain."/logoff?client_id=".$this->app_key."&redirect_uri=".rawurlencode($this->callback_url."?act=done&state=".$int_state)."&view=web";
        return $str_url;
    }
}