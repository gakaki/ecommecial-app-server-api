<?php
class TopModelApi extends LibaryModule {
    public function getTopAppInfo($int_cert_id) {
        $array_config = N('SysConfig')->getGroupConfig('SITE');
        $array_result = array();
        $array_result["appkey"] = $array_config['taobao_app_key'];
        $array_result["appsec"] = ck_encrypt_data($array_config['taobao_app_secret'], $int_cert_id);
        $array_result["date"] = date("Y-m-d");
        return $array_result;
    }
}