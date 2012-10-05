<?php
class CacheNone extends CacheEngine {
    private $cache_path = "";

    public function setHost($str_host) {}
    public function setPort($int_port) {}
    public function connect() {
        $this->cache_path = ROOTPATH."/cache/cachenone/";
    }

    public function set($str_key, $mixed_value) {
        $str_contents = '<?php /*'.time().'*/exit;?>'."\n".serialize($mixed_value);
        if (file_put_contents($this->getFilePath($str_key), $str_contents)) {
            return true;
        }
        return false;
    }

    public function get($str_key) {
        $str_contents = @file_get_contents($this->getFilePath($str_key));
        if ($str_contents===false) {
            return false;
        }
        $str_contents = substr($str_contents, strpos($str_contents, "\n")+1);
        return unserialize($str_contents);
    }

    public function delete($str_key) {
        return unlink($this->getFilePath($str_key));
    }

    public function flush_all() {
        return $this->deleteDirectory($this->cache_path);
    }

    private function getFilePath($str_key) {
        $str_hash = md5($str_key);
        $str_path = $this->cache_path.$str_hash[0]."/".$str_hash[1].$str_hash[2]."/";
        if (!file_exists($str_path)) {
            mkdir($str_path, 0777, true);
        }
        return $str_path.$str_hash.".php";
    }

    private function deleteDirectory($dir) {
        if (!file_exists($dir)) {
            return true;
        }
        if (!is_dir($dir) || is_link($dir)) {
            return unlink($dir);
        }
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            if (!$this->deleteDirectory($dir . "/" . $item)) {
                chmod($dir . "/" . $item, 0777);
                if (!$this->deleteDirectory($dir . "/" . $item)) {
                    return false;
                }
            }
        }
        return rmdir($dir);
    }

}