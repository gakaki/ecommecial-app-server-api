<?php
class ShopplusShopplusActionApi extends NActionModule {
    public function start_automatic_task() {
        if (L("api.model.shopplus")->isAllowSystemAutomatic($_POST["cert_id"])) {
            Q()->rpush("automatic_task", $_POST["url"]);
            return result("API_RESPONSE", true);
        }
        else {
            return result("API_ERROR", "DENY_AUTOMATIC");
        }
    }
}