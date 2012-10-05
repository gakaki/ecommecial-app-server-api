<?php
class CacheMemcache extends CacheEngine {
    private $str_host = "";
    private $int_port = 0;
    private $obj_memcache = null;

    public function setHost($str_host) {
        $this->str_host = $str_host;
    }

    public function setPort($int_port) {
        $this->int_port = $int_port;
    }

    public function connect() {
        $this->obj_memcache = N("Memcache");
        $this->obj_memcache->connect($this->str_host, $this->int_port);
    }

    public function set($str_key, $mixed_value) {
        return $this->obj_memcache->set($str_key, $mixed_value);
    }

    public function get($str_key) {
        return $this->obj_memcache->get($str_key);
    }

    public function delete($str_key) {
        return $this->obj_memcache->del($str_key);
    }

    public function flush_all() {
        return $this->obj_memcache->flush();
    }
}