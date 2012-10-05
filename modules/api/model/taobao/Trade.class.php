<?php
class TradeTaobaoModelApi extends LibaryModule {
    public function getTradesCount($int_tu_id, $array_params) {
        $str_sql = "SELECT count(*) AS count FROM top_trade WHERE tu_id=:tu_id";
        foreach ($array_params as $str_key=>$str_value) {
            if ($str_key=="modified") {
                $str_sql .= " AND ".$str_key.">=:".$str_key;
            }
            else {
                $str_sql .= " AND ".$str_key."=:".$str_key;
            }
        }
        $array_params["tu_id"] = $int_tu_id;
        return DB(2)->getOne($str_sql, $array_params);
    }

    public function getTrades($int_tu_id, $array_params, $int_page_no = 1, $int_page_size = 100) {
        $str_sql = "SELECT tt_id, num_iid, adjust_fee, buyer_nick, buyer_obtain_point_fee, buyer_rate, cod_fee, cod_status, created, end_time, consign_time, discount_fee, modified, num, pay_time, payment, pic_path, point_fee, post_fee, price, commission_fee, real_point_fee, received_payment, receiver_address, receiver_city, receiver_district, receiver_mobile, receiver_phone, receiver_name, receiver_state, receiver_zip, seller_nick, seller_rate, shipping_type, status, title, total_fee, type, alipay_id, alipay_no, available_confirm_fee, buyer_alipay_no, buyer_area, buyer_email, has_post_fee, is_3D, is_brand_sale, is_force_wlb, is_lgtype, seller_alipay_no, seller_email, seller_flag, seller_mobile, seller_name, seller_phone, snapshot_url, trade_from, seller_memo, buyer_message, invoice_name FROM top_trade WHERE tu_id=:tu_id";
        foreach ($array_params as $str_key=>$str_value) {
            if ($str_key=="modified") {
                $str_sql .= " AND ".$str_key.">=:".$str_key;
            }
            else {
                $str_sql .= " AND ".$str_key."=:".$str_key;
            }
        }
        $str_sql .= " ORDER BY modified ASC LIMIT ".(($int_page_no-1)*$int_page_size).", ".$int_page_size;
        $array_params["tu_id"] = $int_tu_id;
        $array_trades = DB(2)->fetchAll($str_sql, $array_params);
        foreach ($array_trades as $int_key=>$array_trade) {
            if ($array_orders = $this->getOrdersByTradeId($array_trade["tt_id"])) {
                $array_trades[$int_key]["orders"] = $array_orders;
            }
        }
        return $array_trades;
    }

    public function getOrdersByTradeId($int_tt_id) {
        $str_sql = "SELECT total_fee, discount_fee, adjust_fee, payment, modified, item_meal_id, status, refund_id, sku_id, sku_properties_name, item_meal_name, num, title, price, pic_path, seller_nick, buyer_nick, refund_status, oid, outer_iid, outer_sku_id, snapshot_url, snapshot, timeout_action_time, buyer_rate, seller_rate, seller_type, num_iid, cid, is_oversold FROM top_orders WHERE tt_id=:tt_id";
        return DB(2)->fetchAll($str_sql, array("tt_id"=>$int_tt_id));
    }

}