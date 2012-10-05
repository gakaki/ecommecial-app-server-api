<?php

	ini_set('display_errors',1);
	require_once('before_each.php');
	
	for ($i=1; $i < 3; ++$i) { 
		
		$image_num 					= $i+2;
		$array_params 				= get_base_data_array($i);
		$array_params["method"] 	= "taobao.item.update";
		
				
		$array_params["json"] 		= json_encode(array(
			//图片参数 //必须参数
			'num_iid'		 => ($i==1)?"14569658871":'14573157686',
			'image'			 => "http://service_api.loc/images/pic_{$image_num}.jpg",
			'num'            => 3,
			'price'          => 2400, //注意修改的时候这里的值至少要和下面的sku prices 里有一个重合否则淘宝报错
			'type'           =>'fixed',
			'stuff_status'   =>'second',
			'title'          =>'ZTE/中兴 C6100 跳楼价 大甩卖啊',
			'desc'           =>'ZTE/中兴 C6100 10月1日大酬宾 无敌跳楼价',
			'location.state' => '上海',
			'location.city'  => '上海',
			'cid'            => 1512,#手机
			'props'          =>"20000:11208;31103:3941766;1627207:3232483;1630696:6536025;10004:10023;10000:10000;1627099:76876820;10002:29836;20710:21958;20879:32561;20930:32998;",			
			
			'sku_properties' =>'1627207:3232483;1630696:6536025',#属于sku原属性
			'sku_quantities' =>'200',#数量不可少 sku的
			'sku_prices'     =>'2400',#价格不可少sku的
	
		));	

		echo request_curl( $array_params );
	}
