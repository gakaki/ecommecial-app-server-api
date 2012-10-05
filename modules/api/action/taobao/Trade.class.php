<?php
class TradeTaobaoActionApi extends NActionModule {
    public function get() {
        if (!isset($_POST["u_id"])) {
            return result("API_ERROR", "Api params error, u_id error.");
        }
        $array_params = array();
        if (isset($_POST["status"])) {
            $array_params["status"] = $_POST["status"];
        }
        if (isset($_POST["modified"])) {
            $array_params["modified"] = $_POST["modified"];
        }
        $int_page_no = 1;
        $int_page_size = 100;
        if (isset($_POST["page_no"])) {
            $int_page_no = $_POST["page_no"];
        }
        if (isset($_POST["page_size"])) {
            $int_page_size = $_POST["page_size"];
        }
        $int_count = L("api.model.taobao.trade")->getTradesCount($_POST["u_id"], $array_params);
        $array_trades = L("api.model.taobao.trade")->getTrades($_POST["u_id"], $array_params, $int_page_no, $int_page_size);
        return result("API_RESPONSE_COUNT", intval($int_count), $array_trades);
    }
}