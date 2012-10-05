<?php
class CertificateModelApi extends LibaryModule {
    public function checkCertById($int_cert_id) {
        $str_sql = "SELECT s_cert FROM sites WHERE s_id=:s_id AND s_status=1 AND s_expire>=NOW()";
        if ($str_cert = DB()->getOne($str_sql, array("s_id"=>$int_cert_id))) {
            return json_decode($str_cert, true);
        }
        return false;
    }
}