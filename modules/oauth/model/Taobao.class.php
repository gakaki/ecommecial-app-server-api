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
class TaobaoModelOauth extends LibaryModule {
    public function createSession($str_callback_url) {
        $str_sql = "INSERT INTO oauth SET oa_type=1, oa_callback_url=:oa_callback_url";
        if (DB()->query($str_sql, array("oa_callback_url"=>$str_callback_url))) {
            return DB()->lastInsertId();
        }
        return false;
    }

    public function storeCallbackParams($int_oa_id, $str_callback_params) {
        $str_sql = "UPDATE oauth SET oa_callback_params=:oa_callback_params, oa_status=1 WHERE oa_id=:oa_id";
        return DB()->query($str_sql, array("oa_callback_params"=>$str_callback_params, "oa_id"=>$int_oa_id));
    }

    public function getCallbackParams($int_oa_id) {
        $str_sql = "SELECT oa_callback_url AS url, oa_callback_params AS params FROM oauth WHERE oa_id=:oa_id";
        return DB()->fetchOne($str_sql, array("oa_id"=>$int_oa_id));
    }

    public function getCallbackUrl($int_oa_id) {
        $str_sql = "SELECT oa_callback_url FROM oauth WHERE oa_id=:oa_id";
        return DB()->getOne($str_sql, array("oa_id"=>$int_oa_id));
    }

    public function saveSesstionToDB($array_session_info) {
        $str_sql = "REPLACE INTO top_session SET ts_user_id=:ts_user_id, ts_user_nick=:ts_user_nick, ts_access_token=:ts_access_token, ts_expires_in=:ts_expires_in, ts_refresh_token=:ts_refresh_token, ts_re_expires_in=:ts_re_expires_in";
        return DB()->query($str_sql, array("ts_user_id"=>$array_session_info["taobao_user_id"], "ts_user_nick"=>$array_session_info["taobao_user_nick"], "ts_access_token"=>$array_session_info["access_token"], "ts_expires_in"=>date("Y-m-d H:i:s", time()+$array_session_info["expires_in"]), "ts_refresh_token"=>$array_session_info["refresh_token"], "ts_re_expires_in"=>date("Y-m-d H:i:s", time()+$array_session_info["re_expires_in"])));
        //http://service.guanyisoft.com/oauth.php?act=create&callback=/
    }

    public function getMaxAvailableSandboxUserId() {
        $str_sql = "SELECT MAX(ts_user_id) AS max FROM top_session WHERE ts_user_id<100000";
        $int_max_user_id = DB()->getOne($str_sql);
        return ++$int_max_user_id;
    }
}

