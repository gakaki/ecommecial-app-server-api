<?php
/**
 * 淘宝OAuth 2.0登录
 *
 * @package oauth2
 * @stage 1.0
 * @author Luis Pater
 * @date 2012-03-09
 * @license MIT
 * @copyright Copyright (C) 2011, Shanghai GuanYiSoft Co., Ltd.
 */
class TaobaoActionOauth extends NActionModule {
    public function subControler() {
        return $this->runSubControler();
    }

    protected function runSubControler() {
        if (isset($_GET["act"]) && method_exists($this, $_GET["act"])) {
            return $this->$_GET["act"]();
        }
        else {
            return $this->callback();
        }
    }

    public function create() {
        if ($int_oa_id = L("oauth.model.taobao")->createSession($_GET["callback"])) {
            $str_url = L("oauth.logic.taobao")->createOAuthUrl($int_oa_id);
            return result("LOCATION", $str_url);
        }
        return result("OAUTH_ERROR");
    }

    public function callback() {
        if (isset($_GET["act"]) && $_GET["act"]=="done") {
            if ($array_callback_info = L("oauth.model.taobao")->getCallbackParams($_GET["state"])) {
                return result("LOCATION", L("oauth.logic.taobao")->buildResultUrl($array_callback_info["url"], json_decode($array_callback_info["params"], true)));
            }
        }
        elseif (!isset($_GET["error"])) {
            if ($array_top_result = L("oauth.logic.taobao")->getSessionInfo($_GET["code"])) {
                if (!isset($array_top_result["taobao_user_id"])) {
                    $array_top_result["taobao_user_id"] = L("oauth.model.taobao")->getMaxAvailableSandboxUserId();
                    $array_top_result["taobao_user_nick"] = "sandbox";
                }
                L("oauth.model.taobao")->saveSesstionToDB($array_top_result);
                Q()->rpush("taobao_user", $array_top_result["taobao_user_id"]);
                $str_params = json_encode($array_top_result);
                if ($str_callback_url = L("oauth.model.taobao")->getCallbackUrl($_GET["state"])) {
                    if (L("oauth.model.taobao")->storeCallbackParams($_GET["state"], $str_params)) {
                        return result("LOCATION", L("oauth.logic.taobao")->getDoneUrl($_GET["state"]));
                    }
                }
            }
        }
        return result("OAUTH_ERROR");
    }
}