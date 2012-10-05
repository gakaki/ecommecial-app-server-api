<?php
class RefundTaobaoModelApi extends LibaryModule {
    public function getRefundsCount($int_tu_id, $array_params) {
        $str_sql = "SELECT count(*) AS count FROM top_refund WHERE tu_id=:tu_id";
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

    public function getRefunds($int_tu_id, $array_params, $int_page_no = 1, $int_page_size = 100) {
        $str_sql = "SELECT refund_id, tt_id, shipping_type, oid, alipay_no, total_fee, buyer_nick, seller_nick, created, modified, order_status, status, good_status, has_good_return, refund_fee, payment, reason, `desc`, title, price, num, good_return_time, company_name, sid, address, refund_remind_timeout, num_iid FROM top_refund WHERE tu_id=:tu_id";
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
        $array_refunds = DB(2)->fetchAll($str_sql, $array_params);
        return $array_refunds;
    }

}