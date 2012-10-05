<?php
define("VM_HTML", 1);
define("VM_XML", 2);
define("VM_JSON", 3);
define("VM_SMARTY", 4);
class NViewModule {
    private $output_mode = VM_SMARTY;
    private $data = array();
    private $bln_disable_display = null;
    protected $obj_smarty = null;

    /**
     * 构造函数
     *
     * @author Luis Pater
     * @date 2011-04-19
     * @param string $str_package_name 包名
     * @param boolean $bln_disable_display 是否捕获输出
     */
    public function __construct($bln_disable_display = false) {
    	$this->bln_disable_display = $bln_disable_display;
    	$this->obj_smarty = NN("Smarty");
    	$this->obj_smarty->compile_dir = N("Config")->COMPILE_BASE_DIR;
    	$this->obj_smarty->force_compile = false;
    	$this->obj_smarty->left_delimiter = "{!";
    	$this->obj_smarty->right_delimiter = "!}";
    	$this->obj_smarty->allow_php_templates = true;

    	$this->assign("WEBROOT", WEB_ROOT);
        $this->assign("WEBENTRY", WEB_ENTRY);
        $this->assign("WEBROOTJS", WEB_ROOT.'statics/js/');
        $this->assign("WEBROOTCSS", WEB_ROOT.'statics/css/');
        $this->assign("ADMINCSS", WEB_ROOT.'modules/admin/statics/css/');
        $this->assign("ADMINJS", WEB_ROOT.'modules/admin/statics/js/');
    }


    public function assign($tpl_var, $value = null, $nocache = false) {
        if (!$this->bln_disable_display) {
            $this->obj_smarty->assign($tpl_var, $value, $nocache);
        }
        else { //如果不捕获输出的话，则将当前内送入堆栈
            N("Stack", "Smarty")->push("assign", array($tpl_var, $value, $nocache));
        }
    }

    public function display($template, $cache_id = null, $compile_id = null, $parent = null) {
        $str_result = "";
        if ($this->output_mode==VM_HTML) {
            $obj_xmldoc = NN("DOMDocument");
            $obj_xsldoc = NN("DOMDocument");
            $obj_xmldoc->loadXML(toXml($this->data));
            $obj_xsldoc->load(ROOTPATH."statics/xslt/".$template);
            $obj_proc = NN("XSLTProcessor");
            $obj_proc->registerPHPFunctions();
            $obj_proc->importStyleSheet($obj_xsldoc);
            $str_result = $obj_proc->transformToXML($obj_xmldoc);
        }
        elseif ($this->output_mode==VM_XML) {
            $str_result = toXml($this->data);
        }
        elseif ($this->output_mode==VM_JSON) {
            $str_result = json_encode($this->data);
        }elseif ($this->output_mode==VM_SMARTY) {
        	while ($array_assign = N("Stack", "SmartyEx")->pop("assign")) {
        		$this->assign($array_assign[0], $array_assign[1], $array_assign[2]);
        	}
        	$this->obj_smarty->display('modules/'.$template, $cache_id, $compile_id, $parent);
            return ;
        }
        echo str_replace(' xmlns:php="http://php.net/xsl"', "", $str_result);
    }

	//后台分页的实现
	public function assign_pagenav( $int_count, $int_page_limit, $int_page=1, $query_url="",$filter=array()) {
		$str_condition = '';
        if ($int_count) {
            if(is_array($filter) && count($filter) > 0) {
                foreach($filter as $key=>$value) {
                    //$query_url .="&".$key."=".$value;
                    $str_condition .= $key . ":'" . trim($value) . "'";
                    $str_condition .= ',';
                }
            }
            $obj_smarty = NN("SmartyEx", "base");
            $obj_smarty->assign("int_page", $int_page);//当前要输出的页
            $obj_smarty->assign("int_last_page", ceil($int_count / $int_page_limit));//尾页
            $obj_smarty->assign("str_query", $query_url);//url拼接
            $obj_smarty->assign("str_js_data", $str_condition);//url拼接
            $obj_smarty->assign("max_show_num", 5);
            $str_page_nav = $obj_smarty->fetch("statics/common/pagenav_1.5.html");
            unset($obj_smarty);
            $this->assign("pagelist", $str_page_nav);
            return $int_page;
        }
    }
}