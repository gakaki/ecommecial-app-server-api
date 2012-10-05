<?php
class LogisticsTaobaoLogicApi extends TopCommunications {
    public function taobaoLogisticsOfflineSend($int_tid, $str_out_sid, $str_company_code) {
        $array_params = array();
        $array_params["tid"] = $int_tid;
        $array_params["out_sid"] = $str_out_sid;
        $array_params["company_code"] = $str_company_code;
        return $this->requestAPI("taobao.logistics.offline.send", $array_params);
    }
}