<?php
/**
 * HTTP通用处理工具
 * @copyright Copyright (C) 2011, 上海包孜网络科技有限公司.
 * @license: BSD
 * @author: Luis Pater
 * @date: 2011-02-19
 * $Id: HttpHandler.class.php 3345 2012-01-10 07:01:44Z rice $
 */
class HttpHandler {
    /**
     * 浏览器缓存控制
     *
     * @author Luis Pater
     * @date 2011-02-19
     * @param string $str_etag ETAG信息
     * @param integer $int_modifiedTime 最后修改时间
     * @param integer $int_expires 过期时间
     * @return integer 符合条件的数据的总数
     */
    public static function cache_control($str_etag, $int_modifiedTime, $int_expires) {
        self::expires($int_expires);
        $bln_etag = self::etag($str_etag);
        $bln_modified = self::lastModified($int_modifiedTime);
        if ($bln_etag || $bln_modified) {
            header("HTTP/1.1 304 Not Modified");
            exit;
        }
    }

    /**
     * 比对ETAG信息
     *
     * @author Luis Pater
     * @date 2011-02-19
     * @param string $str_etag ETAG信息
     * @return boolean 比对ETAG信息是否一致
     */
    public static function etag($str_etag) {
        header('ETag: ' . $str_etag);
        if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $str_etag == $_SERVER['HTTP_IF_NONE_MATCH']) {
            return true;
        }
        return false;
    }

    /**
     * 比对Last-Modified信息
     *
     * @author Luis Pater
     * @date 2011-02-19
     * @param integer $int_modifiedTime Last-Modified信息
     * @return boolean 比对Last-Modified信息是否一致
     */
    public static function lastModified($int_modifiedTime) {
        $str_modifiedTime = date('D, d M Y H:i:s', $int_modifiedTime) . ' GMT';
        header("Last-Modified: ".$str_modifiedTime);
        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && $str_modifiedTime == $_SERVER['HTTP_IF_MODIFIED_SINCE']) {
            return true;
        }
        return false;
    }

    /**
     * 输出过期时间
     *
     * @author Luis Pater
     * @date 2011-02-19
     * @param integer $int_time 过期时间
     */
    public static function expires($int_time) {
        $int_time = date('D, d M Y H:i:s', $int_time) . ' GMT';
        header("Cache-Control: public");
        header("Pragma: cache");
        header("Date: ".date('D, d M Y H:i:s', time()) . ' GMT');
        header("Expires: ".$int_time);
    }

    /**
     * 使用GZIP压缩输出内容
     *
     * @author Luis Pater
     * @date 2011-02-19
     * @param string $str_data 要出输出的内容
     * @param string $str_content_type content type
     */
    public static function gzip_output($str_data, $str_content_type) {
        header("Content-Encoding: gzip");
        header("Content-Type: ".$str_content_type);
        echo gzencode($str_data, 9);
    }

    /**
     * 301永久跳转
     *
     * @author Luis Pater
     * @date 2011-02-19
     * @param string $str_url 目标网址
     */
    public static function permanent_redirect($str_url) {
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: ".$str_url);
        exit;
    }

    /**
     * 302临时跳转
     *
     * @author Luis Pater
     * @date 2011-02-19
     * @param string $str_url 目标网址
     */
    public static function redirect($str_url) {
        header("Location: ".$str_url);
        exit;
    }

    /**
     * 返回来源页
     *
     * @author Luis Pater
     * @date 2011-02-19
     */
    public static function gotoReferer() {
        if (isset($_SERVER["HTTP_REFERER"])) {
            header("Location: ".$_SERVER["HTTP_REFERER"]);
            exit;
        }
    }

    /**
     * 输出404页面无法找到
     *
     * @author Luis Pater
     * @date 2011-02-19
     */
    public static function not_found() {
        header("HTTP/1.0 404 Not Found");
        header("Status: 404 Not Found");
        exit;
    }

    /**
     * 获得当前的PATH_INFO信息
     *
     * @author Luis Pater
     * @date 2011-02-19
     * @return string 当前的PATH_INFO信息
     */
    public static function get_path_info() {
        $path_info = '';
        if (isset($_SERVER['PATH_INFO']) && strlen($_SERVER['PATH_INFO'])) { //如果存在PATH_INFO信息
            $path_info = $_SERVER['PATH_INFO'];
        }
        elseif (isset($_SERVER['ORIG_PATH_INFO'])) { //如果存在ORIG_PATH_INFO信息
            $path_info = $_SERVER['ORIG_PATH_INFO'];
            $script_name = self::get_script_name();
            $request_uri = self::get_request_uri();
            if ($path_info==$script_name) {
                return $request_uri;
            }
            elseif ($request_uri=="/") {
                $path_info = "/";
            }
            elseif(substr($script_name, -1, 1) == '/') {
                $path_info = $path_info . '/';
            }
        }
        else { //如果都不存在，则自己拼接PATH_INFO信息
            $script_name = self::get_script_name();
            $script_dir = preg_replace('/[^\/]+$/', '', $script_name);
            $request_uri = self::get_request_uri();
            $urlinfo = parse_url($request_uri);
            if (strpos($urlinfo['path'], $script_name) === 0) {
                $path_info = substr($urlinfo['path'], strlen($script_name));
            }
            elseif ( @strpos($urlinfo['path'], $script_dir) === 0 ) {
                $path_info = substr($urlinfo['path'], strlen($script_dir));
            }
        }
        if ($path_info) {
            $path_info = "/".ltrim($path_info,"/");
        }
        return $path_info;
    }

    /**
     * 获得当前脚本名称
     *
     * @author Luis Pater
     * @date 2011-02-19
     * @return string 当前的脚本名称
     */
    public static function get_script_name() {
        return isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : (isset($_SERVER['ORIG_SCRIPT_NAME']) ? $_SERVER['ORIG_SCRIPT_NAME'] : '');
    }

    /**
     * 获得当前求情的url地址
     *
     * @author Luis Pater
     * @date 2011-02-19
     * @return string 当前求情的url地址
     */
    public static function get_request_uri() {
        if (isset($_SERVER['HTTP_X_REWRITE_URL'])) {
            return $_SERVER['HTTP_X_REWRITE_URL'];
        }
        elseif (isset($_SERVER['REQUEST_URI'])) {
            return $_SERVER['REQUEST_URI'];
        }
        elseif (isset($_SERVER['ORIG_PATH_INFO'])) {
            return $_SERVER['ORIG_PATH_INFO'] . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '');
        }
    }

    public static function get_base_url(){
        $filename = (isset($_SERVER['SCRIPT_FILENAME'])) ? basename($_SERVER['SCRIPT_FILENAME']) : '';
        if (isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME']) === $filename) {
            $base_url = $_SERVER['ORIG_SCRIPT_NAME'];
        }
        elseif (isset($_SERVER['SCRIPT_NAME']) && basename($_SERVER['SCRIPT_NAME']) === $filename) {
            $base_url = $_SERVER['SCRIPT_NAME'];
        }
        elseif (isset($_SERVER['PHP_SELF']) && basename($_SERVER['PHP_SELF']) === $filename) {
            $base_url = $_SERVER['PHP_SELF'];
        }
        else {
            $path    = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : '';
            $file    = isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : '';
            $segs    = explode('/', trim($file, '/'));
            $segs    = array_reverse($segs);
            $index   = 0;
            $last    = count($segs);
            $base_url = '';
            do {
                $seg = $segs[$index];
                $base_url = '/' . $seg . $base_url;
                ++$index;
            } while (($last > $index) && (false !== ($pos = strpos($path, $base_url))) && (0 != $pos));
        }

        $request_uri = self::get_request_uri();
        if (0 === strpos($request_uri, $base_url)) {
            $base_url = self::dirname($base_url);
            return $base_url ? $base_url : "/";
        }
        if (0 === strpos($request_uri, strstr(PHP_OS, "WIN") ? str_replace('\\', '/', dirname($base_url)) : dirname($base_url))) {
            $base_url = self::dirname($base_url);
            return $base_url ? $base_url : "/";
        }

        $truncatedrequest_uri = $request_uri;
        if (($pos = strpos($request_uri, '?')) !== false) {
            $truncatedrequest_uri = substr($request_uri, 0, $pos);
        }

        $basename = basename($base_url);
        if (empty($basename) || !strpos($truncatedrequest_uri, $basename)) {
            return "/";
        }

        if ((strlen($request_uri) >= strlen($base_url))
        && ((false !== ($pos = strpos($request_uri, $base_url))) && ($pos !== 0)))  {
            $base_url = substr($request_uri, 0, $pos + strlen($base_url));
        }
        $base_url = rtrim(self::dirname($base_url), '/');
        return $base_url ? $base_url : "/";
    }

    public static function dirname($dir){
        return substr($dir,0,strrpos($dir,"/"));
    }

}