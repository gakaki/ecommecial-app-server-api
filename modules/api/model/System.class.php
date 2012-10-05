<?php
class SystemModelApi extends LibaryModule {
    public function getSessionByUserId($int_user_id) {
        return DB()->getOne("SELECT ts_access_token FROM top_session WHERE ts_user_id=:ts_user_id", array("ts_user_id"=>$int_user_id));
    }
}