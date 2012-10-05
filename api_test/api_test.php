<?php
	
	$array_params = array("ts"=>time());
	/* 商品获取
	$array_params["cert_id"] = ck_cert_id();
	$array_params["method"] = "taobao.trade.get";
	$array_params["u_id"] = "2";
	*/
	
	/* 发货
	$array_params["cert_id"] = ck_cert_id();
	$array_params["method"]  = "taobao.logistics.offlinesend";
	$array_params["u_id"]    = "2";
	$array_params["tid"]     = "2"; //淘宝订单号
	$array_params["out_sid"] = "2121213123"; //物流单据号
	$array_params["company_code"] = "2121213123"; //物流公司 {"logistics_companies_get_response":{"logistics_companies":{"logistics_company":[{"code":"LTS","name":"联昊通"},{"code":"QFKD","name":"全峰快递"},{"code":"UNIPS","name":"发网"},{"code":"UAPEX","name":"全一快递"},{"code":"POST","name":"中国邮政平邮"},{"code":"EMS","name":"EMS"},{"code":"YTO","name":"圆通速递"},{"code":"ZTO","name":"中通速递"},{"code":"HZABC","name":"杭州爱彼西"},{"code":"ZJS","name":"宅急送"},{"code":"YUNDA","name":"韵达快运"},{"code":"TTKDEX","name":"海航天天快递"},{"code":"EBON","name":"一邦(CCES)"},{"code":"BEST","name":"百世物流"},{"code":"FEDEX","name":"联邦快递"},{"code":"DBL","name":"德邦物流"},{"code":"SHQ","name":"华强物流"},{"code":"WLB-STARS","name":"星辰急便"},{"code":"STARS","name":"星晨急便"},{"code":"HTKY","name":"汇通快运"},{"code":"CRE","name":"中铁快运"},{"code":"WLB-ABC","name":"浙江ABC"},{"code":"WLB-SAD","name":"赛澳递"},{"code":"SF","name":"顺丰速运"},{"code":"AIRFEX","name":"亚风"},{"code":"APEX","name":"全一"},{"code":"CYEXP","name":"长宇"},{"code":"DTW","name":"大田"},{"code":"YUD","name":"长发"},{"code":"ANTO","name":"安得"},{"code":"CCES","name":"CCES"},{"code":"STO","name":"申通E物流"},{"code":"ZY","name":"中远"},{"code":"LB","name":"龙邦物流"},{"code":"DFH","name":"东方汇"},{"code":"SY","name":"首业"},{"code":"YC","name":"远长"},{"code":"YCT","name":"黑猫宅急便"},{"code":"XB","name":"新邦物流"},{"code":"NEDA","name":"港中能达"},{"code":"XFHONG","name":"鑫飞鸿快递"},{"code":"FAST","name":"快捷速递"},{"code":"UC","name":"优速物流"},{"code":"QRT","name":"全日通快递"},{"code":"OTHER","name":"其他"}]}}}
	*/
	
	/* 获取退货
	$array_params["cert_id"] = ck_cert_id();
	$array_params["method"] = "taobao.refund.get";
	$array_params["u_id"] = "2";
	$array_params["status"] = "WAIT_SELLER_AGREE";
	$array_params["modified"] = "2012-04-06 15:18:22";
	*/
	
	//增加item商品 taobao.item.add

	$array_params["method"] 	= "taobao.item.add";
	$array_params["u_id"] 		= "2";
	$array_params["status"] 	= "0";
	$array_params["modified"] 	= date("Y-m-d H:i:s");
	$array_params["cert_id"] 	= ck_cert_id();
	$array_params["ts"] 		= date("Y-m-d H:i:s");
	$array_params["json"] 		= json_encode(array(
	
		//必须参数
		'num'            => 10,
		'price'          => 3 , 
		'price'          =>'fixed',
		'stuff_status'   =>'new',
		'title'          =>'小明想小红',
		'desc'           =>'就像大雄想小静',
		'location.state' => '上海',
		'location.city'  => '上海',
		//'image'			 => '',
		'pic_url'		 => 'http://gakaki.loc/test.bmp',
		'cid'            => '50019780' //平板电脑的分类id
			
	));
	
	
	
	ksort($array_params);
	$str_verfy_string = "";
	foreach ($array_params as $str_key=>$str_value) {
	$str_verfy_string .= $str_key.$str_value;
	}
	$array_params["hash"] = strtoupper(md5(ck_cert_token().$str_verfy_string.ck_cert_token()));
	
	
	$api_url 		= "http://service.guanyisoft.com/api.php";
	$local = 1;
	if ($local == 1) {
	$api_url 		= "http://service_api.loc:2345/api.php";
	}
	
	
	$obj_ch = curl_init();
	curl_setopt($obj_ch, CURLOPT_URL, $api_url);
	curl_setopt($obj_ch, CURLOPT_POST, 1);
	curl_setopt($obj_ch, CURLOPT_POSTFIELDS, $array_params);
	curl_setopt($obj_ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($obj_ch, CURLOPT_HEADER, 0);
	curl_setopt($obj_ch, CURLOPT_HTTPHEADER, array('Expect:'));
	echo $str_result = curl_exec($obj_ch);
	curl_close($obj_ch);