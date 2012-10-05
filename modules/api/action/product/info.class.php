<?php
header("Content-type: text/html; charset=utf8");

class InfoProductActionApi extends NActionModule {

	public function check_goods_id_exist()
	{
		$goods_id     = intVal($_REQUEST['goods_id']);
		
		if (!$goods_id) {
			$err_arr = array(
				"error_code" =>  '012',
				"error_info" =>  "未找到该商品编号的商品！"
			);
			return jr("API_ERROR_DETAIL",$err_arr);
		}
		return $goods_id;
	}
	public function check_member_id_exist()
	{
		$member_id     = $_SESSION['uid'];
		
		if (!$member_id) {
			$err_arr = array(
				"error_code" =>  '014',
				"error_info" =>  "未找到登入用户！"
			);
			return jr("API_ERROR_DETAIL",$err_arr);
		}
		return $member_id;
	}

	// 产品添加到用户的收藏夹功能
	// http://appapi.loc/api.php?method=product.info.add_favotite&good_id=102
	public function add_favotite()
	{
		$_REQUEST['goods_id'] = 102;
		$_SESSION['uid']      = 4;
		
		$res_good_id          = $this->check_goods_id_exist();
		$res_member_id        = $this->check_member_id_exist();

		if (is_array($res_good_id)) {
			return $res_good_id;
		}
		if (is_array($res_member_id)) {
			return $res_member_id;
		}
		
		$update_res          = M('User')->add_favotite( $res_good_id , $res_member_id );
		$res  				 = array('add_fav_res' => $update_res );
        return jr("API_RESPONSE_WRAP",$res);
	}


	
	// 鞋子专用的计算尺码功能 需要给该商品 一个偏移值存起来 放字段吧
	// http://appapi.loc/api.php?method=product.info.size_calc&good_id=102
	/*

	
			select b.brand_id,b.brand_name,st.name from 
			sdb_brand b,
			sdb_type_brand sb,
			sdb_goods_type st
			where 
			b.brand_id = sb.brand_id and 
			st.name = '单鞋'

		给类型加上该死的尺码就可以了 然后所有的设置默认没有记录就是标准 如果有的话那么就不是了
	*/
	public function size_calc()
	{
		$good_id        = $this->check_goods_id_exist();//这里要是没获取到good_id 那么就表示出错

		if (is_array($good_id)) {
			return $res_good_id;
		}

		$offset			= $this->get_offset();

		$res 			= array(
			"offset"  => 	$extend_info ,
			"message" =>  	$comments_count //售前咨询
		);
		
        return jr("API_RESPONSE_WRAP",$res);
	}

	//商品具体信息 国扩展属性信息 可以cache掉结果
	// http://appapi.loc/api.php?method=product.info.product_extend_info&good_id=102
	public function product_extend_info()
	{
		$good_id        = $this->check_goods_id_exist();//这里要是没获取到good_id 那么就表示出错

		if (is_array($good_id)) {
			return $res_good_id;
		}

		$extend_info    = M('Product')->get_extend_info_by_id($good_id);
		$comments_count = M('Product')->get_comments_count_by_type($good_id);			//默认ask 售前咨询
		$consults_count = M('Product')->get_comments_count_by_type($good_id,"discuss"); //discuss 评论

		$res 			= array(
			"detail"         => 	$extend_info ,
			"comments_count" =>  	$comments_count, //售前咨询
			"consults_count" =>  	$consults_count, //评论数量
		);
		
        return jr("API_RESPONSE_WRAP",$res);
	}

	//商品具体信息 可以cache掉结果
	// http://appapi.loc/api.php?method=product.info.product_info&good_id=102
	public function product_info()
	{
		$good_id   = $this->check_goods_id_exist();//这里要是没获取到good_id 那么就表示出错
		
		if (is_array($good_id)) {
		return $res_good_id;
		}
		
		$good_info = M('Product')->get_info_by_id($good_id);
	
		$product_list = M('Good')->lists( $page,$page_size,$sort_type,$type_id,$brand_id,$cat_id,$price_from,$price_to,$spec_values);
		
		$res           = array(
		'filter'       => $filter_data,
		'product_list' => $product_list
		);
		
		
        return jr("API_RESPONSE_PRODUCTLIST_WRAP",$res);
	}


	// http://appapi.loc/api.php?method=product.info.comments&good_id=102
	public function comments()
	{
		$good_id        = $this->check_goods_id_exist();//这里要是没获取到good_id 那么就表示出错
		if (is_array($good_id)) {
			return $res_good_id;
		}

		$comment_type = $_REQUEST['comment_type']; // ask or discuss
		if (!$comment_type) {
			$comment_type = "ask";
		}

		$data               = M('Product')->get_comments($good_id , $comment_type );
		$res                = wrap_data_api_like($data);
		$res['can_comment'] = $this->get_user_can_comment(); //用户是否可以评论
		
        return jr("API_RESPONSE_SIMPLE",$res);
	}


	public function get_user_can_comment()
	{
		$uid = $_SESSION['uid'];
		if (!$uid) {
			return 0;
		}
		return 1;
	}


	// http://appapi.loc/api.php?method=product.info.test_product_info&good_id=102
	public function test_product_info()
	{
		$good_id          = intVal($_REQUEST['good_id']);
		$sql              = "select * from  sdb_goods where goods_id = ".intval($good_id);
		$row              = db_fetch_one($sql);
		
		$row['spec']      = unserialize($row['spec']);
		$row['pdt_desc']  = unserialize($row['pdt_desc']);
		$row['spec_desc'] = unserialize($row['spec_desc']);

		ChromePHP::log( '$row' , $row );
		/*

			pdt_desc: Object
						463: "白色、42"
						464: "白色、43"
						466: "卡其色、46"
						467: "白色、47"
						468: "卡其色、45"

			spec_desc: Object
					
				13444985511: Object
					spec_goods_images: ""
					spec_image: "http://images.s.cn/images/goods/20111023/2cdf9eab5d0932d0.jpg"
					spec_type: "image"
					spec_value: "卡其色"
					spec_value_id: "11"
			
				13461392261: Object
					spec_goods_images: ""
					spec_image: "http://images.s.cn/images/goods/20120730/c65c0019f976f3f3.jpg"
					spec_type: "image"
					spec_value: "白色"
					spec_value_id: "1"

			spec: Object
					1: "颜色"
					4: "尺码"
		
		*/
		
	}
}

