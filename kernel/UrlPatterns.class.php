<?php
class UrlPatterns {
    private $str_xml = "";
    public function __construct() {
        $this->str_xml = file_get_contents(ROOTPATH."/config/urls.xml");
    }

    /**
     * @param string $str_request_url without index.php, without url's params
     */
    public function getUrlPattern($str_request_url) {
        $array_request_url = parse_url($str_request_url);
        $str_request_url = $array_request_url["path"];
        $array_xml = xml2array($this->str_xml);
        if (count($array_xml["patterns"]["pattern"])) {
            foreach ($array_xml["patterns"]["pattern"] as $array_pattern) {
                switch ($array_pattern["type"]) {
                    case "static":
                        if ($array_pattern["rule"] == $str_request_url) {
                            return $this->importAction($array_pattern["action"]);
                        }
                        break;
                    case "reg":
                        if (preg_match("/".$array_pattern["rule"]."/", $str_request_url, $array_matched)) {
                            if (count($array_matched)>1) {
                                $_SERVER["BASE_URL"] = $array_matched[1];
                            }
                            return $this->importAction($array_pattern["action"]);
                        }
                        break;
                }
            }
        }
        $str_path = trim($str_request_url, "/");
        return str_replace("/", ".", $str_path);
    }

    /**
     * exec redirect in urls.xml config
     *
     * @param string $str_action defined in urls.xml item action's value
     */
    private function importAction($str_action) {
        //urls.xml item action's value maybe is 'location(/admin/member/detail/)'
        if (preg_match("/location\((.*?)\)/", $str_action, $array_matched)) {
            HttpHandler::redirect(trim($array_matched[1]));
        }else {
            return $str_action;
        }
    }
}