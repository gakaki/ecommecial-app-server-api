<?php
class ShopplusSysActionApi extends NActionModule {
    public function is_allow_sys_automatic() {
        return result("API_RESPONSE", L("api.model.shopplus")->isAllowSystemAutomatic($_POST["cert_id"]));
    }

    public function add_client_automatic() {
        $int_max_client = L("api.model.shopplus")->getAutomaticClientCount($_POST["cert_id"]);
        $int_used_clients = L("api.model.shopplus")->getAllowAutomaticClient($_POST["cert_id"]);
        if ($int_used_clients<$int_max_client) {
            return result("API_ERROR", "CLIENTS_NUM_EXCEED");
        }
        elseif (L("api.model.shopplus")->addAutomaticClient($_POST["cert_id"], $_POST["m_id"])) {
            return result("API_RESPONSE", "ADD_CLIENT_SUCCESS");
        }
        else {
            return result("API_ERROR", "ADD_CLIENT_FAILED");
        }
    }
}