<?php
class SqlBuilder {

    protected $param_key = 0;

    protected function processConditionOR($extend, $operation = array()) {
        $result = array();
        $result['params'] = array();
        $result['conditon'] = '';
        $items = $extend['items'];
        if(is_array($items) && count($items) > 0){
            $array_tmp_condition = array();
            foreach($items as $express_key=>$express_value) {
                //嵌套and条件,处理[key] = array('a'=>1, 'b'=>'2') 转换成 (a=1 and b=2)
                if(is_array($express_value) && count($express_value) > 0) {
                    $array_tmp_and_condition = array();
                    foreach($express_value as $and_express_key => $and_express_value) {
                        $sign = '=';
                        if(isset($operation[$and_express_key])) {
                            $sign = $operation[$and_express_key];
                        }
                        $array_tmp_and_condition[] = "{$and_express_key} {$sign} :{$and_express_key}{$this->param_key}";
                        $result['params']["{$and_express_key}{$this->param_key}"] = $and_express_value;
                        $this->param_key++;
                    }
                    $array_tmp_condition[] = '('.implode(' AND ', $array_tmp_and_condition).')';
                }else{
                    //处理[key] = 'a,b,c' 转换成 key=a or key=b or key=c
                    $array_tmp_value = explode(',', $express_value);
                    if(is_array($array_tmp_value) && count($array_tmp_value) > 0) {
                        foreach($array_tmp_value as $tmp_key=>$tmp_value) {
                            $sign = '=';
                            if(isset($operation[$express_key])) {
                                $sign = $operation[$express_key];
                            }
                            $array_tmp_condition[] = "{$express_key} {$sign} :{$express_key}{$this->param_key}";
                            $result['params']["{$express_key}{$this->param_key}"] = $tmp_value;
                            $this->param_key++;
                        }
                    }
                }
            }
            $result['conditon'] = implode(' OR ', $array_tmp_condition);
        }

        return $result;
    }

    protected function processConditionIN($extend, $type = 'IN') {
        $result = array();
        $result['params'] = array();
        $result['conditon'] = '';
        $key = $extend['key'];
        $array_value = $extend['value'];
        if(is_array($array_value) && count($array_value) > 0) {
            $array_tmp_value = array();
            foreach($array_value as $item) {
                $array_tmp_value[] = ":{$key}{$this->param_key}";
                $result['params']["{$key}{$this->param_key}"] = $item;
                $this->param_key++;
            }
            $result['condition'] = "{$key} {$type} (".implode(',', $array_tmp_value).")";
        }
        return $result;
    }

    public function processCondition($condition_fields, $operation = array()) {
        $array_params = array();
        $condition_sql = '';
        if(is_array($condition_fields) && count($condition_fields) > 0) {
            $array_tmp_condition = array();
            foreach($condition_fields as $k => $v) {
                if('sql_condition_extend' == $k) {
                    foreach($v as $extend_key=>$extend) {
                        if('OR' == $extend['type']) {
                            $condition_or = $this->processConditionOR($extend, $operation);
                            $array_params = $array_params + $condition_or['params'];
                            $array_tmp_condition[] = ' ('.$condition_or['conditon'].') ';
                        }else if('IN' == $extend['type'] || 'NOT IN' == $extend['type']) {
                            $condition_in = $this->processConditionIN($extend, $extend['type']);
                            $array_params = $array_params + $condition_in['params'];
                            $array_tmp_condition[] = $condition_in['condition'];
                        }
                    }
                }else{
                    $sign = '=';
                    if(isset($operation[$k])) {
                        $sign = $operation[$k];
                    }
                    $array_tmp_condition[] = "{$k} {$sign} :{$k}{$this->param_key}";
                    $array_params[$k.$this->param_key] = $v;
                    $this->param_key++;
                }
            }
            $condition_sql = 'where '.implode(' AND ', $array_tmp_condition);
        }

        return array('params'=>$array_params, 'condition'=>$condition_sql);
    }


    public function buildFields($fields) {

        $array_params = array();
        $str_fields = '';
        if(is_array($fields) && count($fields) > 0) {
            $array_fields_result = array();
            foreach($fields as $k=>$v) {
                $array_fields_result[] = "{$k} =:{$k}{$this->param_key}";
                $array_params["{$k}{$this->param_key}"] = $v;
                $this->param_key++;
            }
            $str_fields = implode(",", $array_fields_result);
        }

        return array('params'=>$array_params, 'fields'=>$str_fields);
    }
}
//$params['aa'] = '01';
//$params['bb'] = '%02';
//$params['cc'] = '03';
//$params_extend = array('type'=>'OR', 'items'=>array('dd'=>'04,05,06','ee'=>'07',0=>array('ff'=>'08','gg'=>'09')));
//$params['sql_condition_extend'][] = $params_extend;
//$params_extend = array('type'=>'IN', 'key'=>'zz', 'value'=>array('a','b','c'));
//$params['sql_condition_extend'][] = $params_extend;
//$operation = array('gg'=>'>', 'ee'=>'<=', 'bb'=>'like');
//$test = new SqlBuilder();
//$result = $test->processCondition($params, $operation);
//var_dump($result);
