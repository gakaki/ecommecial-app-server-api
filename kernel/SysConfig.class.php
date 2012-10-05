<?php
/**
 * 全局系统设置操作类
 *
 * 用于操作保存在数据库中的全局配置信息
 *
 * @license MIT
 * @author Mithern
 * @date 2012-01-13
 * @stage 1.0
 * @copyright Copyright (C) 2011, Shanghai GuanYiSoft Co., Ltd.
 */
class SysConfig {

	/**
	 * 读取配置项的值
	 *
	 * @param $str_module_name string 必选 配置模块名称
	 * @param $str_config_key  string 必选 配置项的KEY
	 *
     * @author Mithern
     * @modify 2012-03-08
     * @version 1.0
	 * @return string 配置值，如果没有读取到配置，则返回空字符串
	 */
	public function getConfig($str_module_name,$str_config_key){
		$str_sql = "SELECT `sc_value` FROM `sys_config` where `sc_module` = :sc_module AND `sc_key` = :sc_key";
		$array_tmp_config = DB()->fetchOne($str_sql, array("sc_module"=>$str_module_name, "sc_key"=>$str_config_key));
		if(is_array($array_tmp_config) && count($array_tmp_config)>0){
			return $array_tmp_config['sc_value'];
		}
		//没有读到配置值，则返回空字符串
		return '';
	}

	/**
	 * 读取配置项的值
	 *
	 * @param $str_module_name string 必选 配置模块名称
	 * @param $$bool_get_detail bool 可选，默认为false 是否需要获取全部数据
	 *
     * @author Mithern
     * @modify 2012-03-08
     * @version 1.0
	 * @return string 返回一组配置值，默认返回一维数组，array(sc_key => sc_value,sc_key => sc_value)
	 * 	如果$bool_get_detail = true 则返回包含多个配置项的二维数组（详细）
	 *  如果没有取到配置信息，则返回空数组
	 */
	public function getGroupConfig($str_module_name,$bool_get_detail = false){
		$str_sql = "SELECT * FROM `sys_config` WHERE `sc_module` = :sc_module";
		$array_tmp_config = DB()->fetchAll($str_sql, array("sc_module"=>$str_module_name));
		if(empty($array_tmp_config) || !is_array($array_tmp_config)){
			return array();
		}
		if(false === $bool_get_detail){
			$array_return = array();
			foreach($array_tmp_config as $key => $val){
				$array_return[$val['sc_key']] = $val['sc_value'];
			}
			return $array_return;
		}
		return $array_tmp_config;
	}

	/**
	 * 设置配置项的值
	 *
	 * @param $str_config_key  string 必选 配置项的KEY
	 * @param $str_config_value  string 配置项对应的值
	 * @param $str_module_name string 必选 配置模块名称
	 *
     * @author Mithern
     * @modify 2012-03-08
     * @version 1.0
	 * @return bool 返回true或者false
	 */
	public function setConfig($str_config_key,$str_config_value,$str_module_name){
		$str_sql = "UPDATE `sys_config` set `sc_value`=:sc_value where `sc_module` = :sc_module AND `sc_key` = :sc_key";
		return $array_tmp_config = DB()->query($str_sql, array("sc_value"=>trim($str_config_value), "sc_module"=>$str_module_name, "sc_key"=>$str_config_key));
	}
}