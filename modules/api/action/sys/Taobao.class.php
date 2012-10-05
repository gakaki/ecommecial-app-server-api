<?php
class TaobaoSysActionApi extends NActionModule {
    public function gettoken() {
        return result("API_RESPONSE", L("api.model.top")->getTopAppInfo($_POST["cert_id"]));
    }
}