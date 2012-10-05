<?php

class ItemTaobaoActionApi extends NActionModule {
	
	
	public function add_or_update($type)
	{
		$array_params = array();
    	
        $operators = array("add", "update");
        
		if (!in_array($type, $operators)) {
			return result("API_ERROR", "Api params error, operator error. 'add' or 'update' must");
		}

        if (!isset($_POST["tu_id"])) {
            return result("API_ERROR", "Api params error, tu_id error.");
        }
        if (!isset($_POST["json"])) {
            return result("API_ERROR", "Api params error, params json can not be null or empty .");
        }
        
        try {
        	
        	$result = json_decode($_POST["json"],true);
        	
        	if ($type=='update') {
        		// if update need fill the num iid field
        		if (empty($result['num_iid'])) {
        			return result("API_ERROR", "Api params error, num_iid in  json can not be null or empty .");
        		}
        	}
        	
        } catch (Exception $e) {
        	return result("API_ERROR", "Api params error, params json can not be resolve .");
        }
        
        $array_params["json"]  		= $_POST["json"];
        $array_params["tu_id"]  	= $_POST["tu_id"];
        $array_params["status"]  	= 0;//默认没同步
        $array_params["created"] 	= date("Y-m-d H:i:s");

        
        $int_count = L("api.model.taobao.item")->add($array_params,$type);

        return result("API_RESPONSE_COUNT", intval($int_count), $array_params);
	}
    public function add(){
    	return $this->add_or_update('add');
    }
    
    
    public function update()
    {
    	return $this->add_or_update('update');
    }
    
  	 public function test()
  	 {
  	 	$data = 'yes  do it';
  	 	return result("API_RESPONSE_COUNT", intval(1), $data);
  	 }
}