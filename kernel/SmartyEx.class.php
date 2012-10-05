<?php
/**
 * SMARTY扩展类
 * @copyright Copyright (C) 2011, 上海包孜网络科技有限公司.
 * @license: BSD
 * @author: Luis Pater
 * @date: 2011-04-19
 * $Id: SmartyEx.class.php 2174 2011-12-07 12:45:25Z hxm $
 */
class SmartyEx extends Smarty {
    private $package_name = "";

    public function __construct($str_package_name) {
        parent::__construct();

        $this->package_name = $str_package_name;
        $this->registerResource("package", array(
                                            array($this, "package_get_template"),
                                            array($this, "package_get_timestamp"),
                                            array($this, "package_get_secure"),
                                            array($this, "package_get_trusted")
                                           )
                               );

        $this->compile_dir = N("Config")->COMPILE_BASE_DIR.md5($this->package_name);
        $this->force_compile = false;
        $this->left_delimiter = "{!";
        $this->right_delimiter = "!}";
        
        $this->allow_php_templates = true; 
        
    }

    public function package_get_template($tpl_name, &$tpl_source, $smarty_obj) {
        if ($str_file = N("PackageManager")->getTemplate($this->package_name, $tpl_name)) {
            $tpl_source = file_get_contents($str_file);
            return true;
        }
        return false;
    }

    public function package_get_timestamp($tpl_name, &$tpl_timestamp, $smarty_obj) {
        if ($str_file = N("PackageManager")->getTemplate($this->package_name, $tpl_name)) {
            $tpl_timestamp = filemtime($str_file);
            return true;
        }
        return false;
    }

    public function package_get_secure($tpl_name, $smarty_obj) {
        // assume all templates are secure
        return true;
    }

    public function package_get_trusted($tpl_name, $smarty_obj) {
        // not used for templates
    }
}