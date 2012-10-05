<?php
class ShopplusModelApi extends LibaryModule {
    public function isAllowSystemAutomatic($int_cert_id) {
        $str_sql = "SELECT s_allow_system_automatic FROM sites WHERE s_id=:s_id AND s_status=1 AND s_expire>=NOW()";
        if (DB()->getOne($str_sql, array("s_id"=>$int_cert_id))) {
            return true;
        }
        return false;
    }

    public function getAutomaticClientCount($int_cert_id) {
        $str_sql = "SELECT count(*) AS count FROM automatcic_clents WHERE s_id=:s_id";
        return DB()->getOne($str_sql, array("s_id"=>$int_cert_id));
    }

    public function getAllowAutomaticClient($int_cert_id) {
        $str_sql = "SELECT s_allow_client_automatic_num FROM sites WHERE s_id=:s_id AND s_status=1";
        return DB()->getOne($str_sql, array("s_id"=>$int_cert_id));
    }

    public function addAutomaticClient($int_cert_id, $int_m_id) {
        $str_sql = "INSERT INTO s_allow_client_automatic_num SET s_id=:s_id, ac_m_id=:ac_m_id";
        return DB()->query($str_sql, array("s_id"=>$int_cert_id, "ac_m_id"=>$int_m_id));
    }
}