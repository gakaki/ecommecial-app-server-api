<?php
/**
 * 数据库扩展
 * @copyright Copyright (C) 2011, 上海包孜网络科技有限公司.
 * @license: BSD
 * @author: Luis Pater
 * @date: 2011-02-19
 * $Id: Database.class.php 3881 2012-02-10 06:36:12Z luis $
 */
class Database extends PDO {
	

    /**
     * 构造函数
     *
     * @author Luis Pater
     * @date 2011-02-19
     */
    public function __construct($str_host, $str_user, $str_pass, $str_dbname = NULL, $int_port = 3306) {
        $str_conn = "mysql:host=".$str_host;
        if (!is_null($str_dbname)) {
            $str_conn .= ";dbname=".$str_dbname;
        }
        if ($int_port!=3306) {
            $str_conn .= ";port=".$str_dbname;
        }
        parent::__construct($str_conn, $str_user, $str_pass, array(PDO::ATTR_PERSISTENT=>false));
   
        $this->setAttribute(PDO::ATTR_STATEMENT_CLASS, array('DBStatement', array($this)));
        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->exec("SET NAMES 'UTF8'");
        $this->exec("SET GROUP_CONCAT_MAX_LEN = 1048576"); //设置GROUP_CONCAT的最大长度为1M
    }

    public function prepare($str_sql, $array_params = array(), $driver_options = array()) {
    	  $str_sql 		= str_replace("#pr#", DB_PREFIX, $str_sql);//替换掉 db的prefix的问题

          return parent::prepare($str_sql, $driver_options);
    }
    /**
     * 返回所有查询结果
     *
     * @author Luis Pater
     * @date 2011-02-19
     * @param string $str_sql 查询的SQL语句
     * @param arrray $array_params 查询的参数
     * @return mixed 返回查询的完整结果集，如果SQL语法错误等查询失败，则返回false
     */
    public function fetchAll($str_sql, $array_params = array(),$fetch_style=PDO::FETCH_ASSOC) {

        $obj_stmt = $this->prepare($str_sql, $array_params);

        foreach ($array_params as $str_key=>$str_values) {
            $obj_stmt->bindValue($str_key, $str_values);
        }
        if ($obj_stmt->execute()) {
            return $obj_stmt->fetchAll($fetch_style);
        }
        
        return false;
    }

    /**
     * 返回第一条查询结果
     *
     * @author Luis Pater
     * @date 2011-02-19
     * @param string $str_sql 查询的SQL语句
     * @param arrray $array_params 查询的参数
     * @return mixed 返回查询的第一条结果，如果SQL语法错误等查询失败，则返回false
     */
    public function fetchOne($str_sql, $array_params = array(),$fetch_style=PDO::FETCH_ASSOC) {
        $obj_stmt = $this->prepare($str_sql, $array_params);
        foreach ($array_params as $str_key=>$str_values) {
            $obj_stmt->bindValue($str_key, $str_values);
        }
        if ($obj_stmt->execute()) {
	        $mixed_result = $obj_stmt->fetch($fetch_style);
			if ($mixed_result===false) {
				return array();
			}
			return $mixed_result;

        }
        return false;
    }

    /**
     * 获得查询的PDO Statement
     *
     * @author Luis Pater
     * @date 2011-02-19
     * @param string $str_sql 查询的SQL语句
     * @param arrray $array_params 查询的参数
     * @return mixed 返回查询的PDO Statement，如果SQL语法错误等查询失败，则返回false
     */
    public function getStatement($str_sql, $array_params = array()) {
        $obj_stmt = $this->prepare($str_sql, $array_params);
        foreach ($array_params as $str_key=>$str_values) {
            $obj_stmt->bindValue($str_key, $str_values);
        }
        if ($obj_stmt->execute()) {
            return $obj_stmt;
        }
        return false;
    }

    /**
     * 查询SQL，不获取查询结果
     *
     * @author Luis Pater
     * @date 2011-02-19
     * @param string $str_sql 查询的SQL语句
     * @param arrray $array_params 查询的参数
     * @return  boolean
     */
    public function query($str_sql, $array_params = array()) {
        
        $obj_stmt = $this->prepare($str_sql, $array_params);
        foreach ($array_params as $str_key=>$str_values) {
            $obj_stmt->bindValue($str_key, $str_values);
        }
        if ($obj_stmt->execute()) {
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * 返回第一条查询结果的第一个字段
     *
     * @author Luis Pater
     * @date 2011-02-19
     * @param string $str_sql 查询的SQL语句
     * @param arrray $array_params 查询的参数
     * @return mixed 返回查询的第一条结果的第一个字段，如果SQL语法错误等查询失败，则返回false
     */
    public function getOne($str_sql, $array_params = array()) {
    	$obj_stmt = $this->prepare($str_sql, $array_params);
    	foreach ($array_params as $str_key=>$str_values) {
    		$obj_stmt->bindValue($str_key, $str_values);
    	}
    	if ($obj_stmt->execute()) {
    		$array_result = $obj_stmt->fetch(PDO::FETCH_NUM);
    		return $array_result[0];
    	}
    	return false;
    }
}