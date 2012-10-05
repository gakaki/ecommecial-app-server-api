<?php
class LogisticsTaobaoActionApi extends NActionModule {
    public function offlinesend() {
        if (!isset($_POST["u_id"])) {
            return result("API_ERROR", "Api params error, u_id error.");
        }
        elseif (!isset($_POST["tid"])) {
            return result("API_ERROR", "Api params error, tid error.");
        }
        elseif (!isset($_POST["out_sid"])) {
            return result("API_ERROR", "Api params error, out_sid error.");
        }
        elseif (!isset($_POST["company_code"])) {
            return result("API_ERROR", "Api params error, company_code error.");
        }
        $int_tid = $_POST["tid"];
        $str_out_sid = $_POST["out_sid"];
        $str_company_code = $_POST["company_code"];
        $str_session = L("api.model.system")->getSessionByUserId($_POST["u_id"]);
        $array_result = L("api.logic.taobao.logistics", $str_method, array($str_session))->taobaoLogisticsOfflineSend($int_tid, $str_out_sid, $str_company_code);
        if (isset($array_result["logistics_offline_send_response"]["shipping"]["is_success"])) {
            return result("API_RESPONSE", "");
        }
        else {
            return result("API_ERROR", $array_result["error_response"]["sub_msg"]);
        }
    }
}