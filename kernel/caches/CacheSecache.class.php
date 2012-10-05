<?php
require(dirname(__FILE__)."/libs/secache/secache.php");
class CacheSecache extends CacheEngine {
    private $obj_secache = null;

    public function setHost($str_host) {}

    public function setPort($int_port) {}

    public function connect() {
        $this->obj_secache = N("secache");
        $this->obj_secache->workat(ROOTPATH."/cache/secache/cache");
    }

    public function set($str_key, $mixed_value) {
        return $this->obj_secache->store(md5($str_key), serialize($mixed_value));
    }

    public function get($str_key) {
        $this->obj_secache->fetch(md5($str_key), $value);
        return unserialize($value);
    }

    public function delete($str_key) {
        return $this->obj_secache->delete(md5($str_key));
    }

    public function flush_all() {
        return $this->obj_secache->clear();
    }
}