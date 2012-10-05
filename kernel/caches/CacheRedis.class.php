<?php
require(dirname(__FILE__)."/libs/RedisBase.class.php");
class CacheRedis extends CacheEngine {
    private $str_host = "";
    private $int_port = 0;
    private $obj_redisbase = null;

    public function setHost($str_host) {
        $this->str_host = $str_host;
    }

    public function setPort($int_port) {
        $this->int_port = $int_port;
    }

    public function connect() {
        $this->obj_redisbase = N("RedisBase", $this->str_host, $this->int_port);
    }

    public function set($str_key, $mixed_value) {
        return $this->obj_redisbase->set($str_key, serialize($mixed_value));
    }

    public function get($str_key) {
        if ($str_result = $this->obj_redisbase->get($str_key)) {
            return unserialize($str_result);
        }
        return false;
    }

    public function delete($str_key) {
        if ($this->obj_redisbase->del($str_key)) {
            return true;
        }
        return false;
    }

    public function flush_all() {
        if ($this->obj_redisbase->flushall()) {
            return true;
        }
        return false;
    }

    public function lpop($str_key) {
        return $this->obj_redisbase->lpop($str_key);
    }

    public function rpop($str_key) {
        return $this->obj_redisbase->rpop($str_key);
    }

    public function lpush($str_key, $mixed_value) {
        return $this->obj_redisbase->lpush($str_key, $mixed_value);
    }

    public function rpush($str_key, $mixed_value) {
        return $this->obj_redisbase->rpush($str_key, $mixed_value);
    }

    public function llen($str_key) {
        return $this->obj_redisbase->llen($str_key);
    }

    public function flush_db() {
        if ($this->obj_redisbase->flushdb()) {
            return true;
        }
        return false;
    }

    public function exists($str_key) {
        if ($this->obj_redisbase->exists($str_key)) {
            return true;
        }
        return false;
    }
}