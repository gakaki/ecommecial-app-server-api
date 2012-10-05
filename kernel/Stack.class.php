<?php
/**
 * 堆栈引擎
 * @copyright Copyright (C) 2011, Shanghai GuanYiSoft Co., Ltd.
 * @license: MIT
 * @author: Luis Pater
 * @date: 2011-09-19
 * $Id: Stack.class.php 68 2011-09-20 09:42:06Z luis $
 */
class Stack {
    private $array_stack = array();

    public function __construct($str_stack_name) {}

    /**
     * 送入堆栈
     *
     * @author Luis Pater
     * @date 2011-09-20
     * @param string $str_name 堆栈名
     * @param mixed 堆栈内容
     */
    public function push($str_name, $mixed_value) {
        if (!isset($this->array_stack[$str_name])) {
            $this->array_stack[$str_name] = array();
        }
        array_push($this->array_stack[$str_name], $mixed_value);
    }

    /**
     * 弹出堆栈
     *
     * @author Luis Pater
     * @date 2011-09-20
     * @param string $str_name 堆栈名
     * @return mixed 堆栈内弹出的数据
     */
    public function pop($str_name) {
        if (is_array($this->array_stack[$str_name])) {
            return array_pop($this->array_stack[$str_name]);
        }
        return false;
    }
}