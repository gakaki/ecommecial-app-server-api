<?php
header("Content-type: text/html; charset=utf8");


class ListProductActionApi extends NActionModule {

	//$filter 	 = "brand=28|price=0,99|type=132|cat=32|size=35";
	public function get_parsed_filter_arr($filter_str)
	{
		
		$filter_arr  = explode("|",$filter_str);
		$res 		 = array();
		foreach ($filter_arr as $filter_item ) {
			list($k,  $v) = explode("=",$filter_item);
			$res[$k]	  = $v;
		}

		if ( array_key_exists("price",$res) ) {
			list($res['price_from'],  $res['price_to']) = explode(",",$res['price']);
		}
		return $res;
	}
	//活动商品列表 可以cache掉结果
	//http://appapi.loc/api.php?method=product.list.activitybrand&page=1&is_filter=1&filter=brand=28|price=0,299|type=4|cat=20|size=33

	// 28 是 new balance 的 brand id
	public function activitybrand()
	{

		$filter           = $_REQUEST['filter'];	// name=gakaki|long=14| 类似这样的筛选关键词
		$is_filter        = intVal($_REQUEST['is_filter']);	// 是否进行筛选 如果为0的话就表示不需要返回筛选关键词了
		
		// $filter        = "brand=28|price=0,99|type=4|cat=20|size=33";//这样的方式只能支持单选 以后改良成多选的吧
		$filter           = $this->get_parsed_filter_arr($filter);//这样的方式只能支持单选 以后改良成多选的吧
		ChromePHP::log( '$filter' , $filter );
		// $filter Object {size: "35", cat: "32", price: "0,99", brand: "28", type: "132"} 
		
		$page             = intVal($_REQUEST['page']);
		$page_size        = intVal($_REQUEST['pageSize']);
		
		$brand_id_request = intVal($_REQUEST['brand_id']);
		$type_id_request  = intVal($_REQUEST['type_id']);//这个typeidrequest是指从专题还是从什么地方来的 不是指商品类型的意思，所以基本可以忽略了
		
		$type_id          = intVal($filter['type']);		// shopex 的分类 
		$brand_id         = intVal($filter['brand']);	// 这里应该是品牌id like brandid  sdb_goods typeid sdb_goods_type
		$cat_id           = intVal($filter['cat']);
		$price_from       = intVal($filter['price_from']);
		$price_to         = intVal($filter['price_to']);
		$spec_values      = array(intVal($filter['size']));	// spec value 表 id
		
		$sort_type        = intVal($_REQUEST['sorttype']); 			// 按什么字段进习排序
		$keyword          = trim($_REQUEST['keyword']);	//关键词 暂时没有使用

		
		if (!$brand_id) {
			$brand_id      = $brand_id_request;
		}
		
		$page_info =  array(
			'type'       => $type,
			'keyword'    => $filter,
			'page'       => $page,
			'page_size'  => $page_size,
			'total'      => $page_size,
			'total_page' => $page_size
		);
		// 从一个brand 查出去 看该品牌 的 分类 (sdb_goods_cat) 鞋子尺码 品牌 类型（sdb_goods_type） 价格
		$filter_info =  array();

		if ($is_filter) {
			$filter_data    = M('Product')->filter_data($brand_id,$type_id);	
		}
		
		$product_list  = M('Good')->lists( $page,$page_size,$sort_type,$type_id,$brand_id,$cat_id,$price_from,$price_to,$spec_values);
		
		$res           = array(
		'filter'       => $filter_data,
		'product_list' => $product_list
		);
		
		
        return jr("API_RESPONSE_PRODUCTLIST_WRAP",$res);
    }
    

    

    //获得商品列表的方法
	//http://appapi.loc/api.php?method=product.list.normal_list
	public function normal_list()
	{
		$filter        = M('Product')->filter_info();
		$product_list  = M('Product')->activity_product_list();
		


		$res           = array(
		'filter'       => $filter,
		'product_list' => $product_list
		);
		
        return jr("API_RESPONSE_WRAP",$res);
    }
	
}

