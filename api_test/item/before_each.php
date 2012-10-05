<?php 

	function get_base_data_array()
	{
		$array_params = array("ts"=>time());
	
		//增加item商品 taobao.item.add
		$array_params["method"] 	= "";
		$array_params["tu_id"] 		= "13424524";
		$array_params["status"] 	= "0";
		//$array_params["modified"] 	= date("Y-m-d H:i:s");
		$array_params["cert_id"] 	= ck_cert_id();
		$array_params["ts"] 		= date("Y-m-d H:i:s");
		
		return $array_params;
	}
	function get_hash(&$array_params)
	{
		ksort($array_params);
		$str_verfy_string = "";
		foreach ($array_params as $str_key=>$str_value) {
			$str_verfy_string .= $str_key.$str_value;
		}
		$array_params["hash"] = strtoupper(md5(ck_cert_token().$str_verfy_string.ck_cert_token()));
	}
	function get_api_url($is_local=1)
	{
		$api_url   = "http://service.guanyisoft.com/api.php";
			$local     = 1;
		if ($local == 1) {
			$api_url   = "http://service_api.loc/api.php";
		}
		return $api_url;
	}
	function request_curl(  $array_params )
	{
		get_hash($array_params);
		$api_url         = get_api_url();
		
		$obj_ch          = curl_init();
		curl_setopt($obj_ch, CURLOPT_URL, $api_url);
		curl_setopt($obj_ch, CURLOPT_POST, 1);
		curl_setopt($obj_ch, CURLOPT_POSTFIELDS, $array_params);
		curl_setopt($obj_ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($obj_ch, CURLOPT_HEADER, 0);
		curl_setopt($obj_ch, CURLOPT_HTTPHEADER, array('Expect:'));
	    $str_result = curl_exec($obj_ch);
		curl_close($obj_ch);
		return $str_result;
	}
	