<?php
/**
 * 淘宝 OAuth 2.0 View
 *
 * @package oauth2
 * @license MIT
 * @author Luis Pater
 * @date 2012-03-09
 * @stage 1.0
 * @copyright Copyright (C) 2011, Shanghai GuanYiSoft Co., Ltd.
 */
class TaobaoViewOauth extends NViewModule {
    public function location($str_url) {
        header("Location: ".$str_url);
    }

    public function oauth_error() {
        echo "OAuth 2.0 Error";
    }
}
