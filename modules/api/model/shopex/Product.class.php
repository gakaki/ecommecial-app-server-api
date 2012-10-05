<?php

class ProductShopexModelApi extends ModelModule {

	
	public function get_extend_info_by_id($goods_id)
	{

		$goods_id             = intVal($goods_id);
		$sql                  = "select * from sdb_goods where goods_id = $goods_id";
		$good_info            = db_fetch_one($sql);
		
		//找出扩展属性 p_1到p_28 然后过滤掉value为空的扩展属性
		$new_extend_info	  = array();
		for ($i=1; $i < 29; $i++) { 
			$key_name = "p_$i";
			$value    = $good_info[$key_name];
			if ( isset($value) ) { //这里扩展属性可能为0 第一个来着
				$new_extend_info[] = array(  
					"info_name_index"  => $i,
					"info_value_option_index" => intVal($value) 
				); //这里用int因为后面用数组下标的所以int一下
			}
		}
		$new_extend_info      = array_filter($new_extend_info);
		
		
		$type_id              = intVal($good_info['type_id']);
		$sql                  = "select * from sdb_goods_type where type_id = $type_id";
		$type_info            = db_fetch_one($sql);
		$type_info['props']   = unserialize($type_info['props'] );
		$type_info['setting'] = unserialize($type_info['setting'] );
		$props				  = $type_info['props'];

		$full_new_extend_info = array();
		foreach ($new_extend_info as $info) {
			
			$k = $info['info_name_index'];
			$v = $info['info_value_option_index'];
			$extend_info_one_row = $props[$k];
			// ChromePHP::log( '$extend_info_one_row' ,$extend_info_one_row  );
			// die(var_dump($props,$extend_info_one_row, $extend_info_one_row['name'], $extend_info_one_row['options'][$v] ,$v,$k));
			$row 				 = array(
				"info_name_index" => $k,
				'name'            => $extend_info_one_row['name'],
				'value'           => $extend_info_one_row['options'][$v]
			);
			$full_new_extend_info[] = $row;		 
		}

		// ChromePHP::log( '$good_info' , $good_info );
		// ChromePHP::log( '$type_info' , $type_info );
		// ChromePHP::log( '$full_new_extend_info' , $full_new_extend_info );
		return 	  	$full_new_extend_info;
	}

	public function get_info_by_id($good_id,$member_lv)
	{
		$info 			= $this->get_info($good_id,$member_lv);
		$tags           = $this->get_tag_names($good_id);

		$images         = $this->get_images($good_id);

		$colors         = $this->get_colors($good_id);
	
		$sizes          = $this->get_sizes($good_id);

		$shares         = $this->get_shares($good_id);

		die(var_dump('$shares' , $shares ));
		
		$data['info']   = $info;
		$data['tags']   = $tags;
		$data['images'] = $images;
		$data['colors'] = $colors;
		$data['sizes']  = $sizes;
		$data['shares'] = $shares;

		return $data;
	}
	public function get_images($good_id)
	{
		$sql  = "select * from  sdb_gimages where goods_id = ".intval($good_id);
		$rows = db_fetch_all($sql);

		$res    = array();
		foreach ($rows as $row) {
			$res[] = array(
				"normal"     =>  $row['source'],
				"bigImage"   =>  $row['big'],
				"smallImage" =>  $row['small'],
				"thumb"      =>  $row['thumbnail']
			);
		}

		return $res;
	}
	public function get_shares()
	{	
		$share_urls = array();
		$share_urls[] =  array(
			"url" =>  "http://v.t.sina.com.cn/share/share.php",
			"name" => "新浪"
		);
		$share_urls[] =  array(
			"url" =>  "http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey",
			"name" => "QQ空间"
		);

		return $share_urls;
	}
	public function get_keywords($good_id)
	{
		$sql  = "select * from  sdb_goods_keywords where goods_id = ".intval($good_id);
		$tags = db_fetch_all($sql);
		return $tags;
	}
	public function get_keywords_names($good_id)
	{
		$keywords     = $this->get_keywords($good_id);
		$new_keywords = array();
		foreach ($keywords as $keyword) {
			$new_keywords[] = $keyword['keyword'];
		}
		return $new_keywords;
	}
	public function get_info($good_id,$member_lv)
	{
		$good                    = db_fetch_one('SELECT * FROM sdb_goods WHERE goods_id='.intval($good_id));
		
		if(!$good) return null;
		
		$new_good                = array();
		$new_good['categoryid']  = $good['cat_id'];
		$new_good['pstatus']     = 0;//pstatus 的问恶
		$new_good['marketPrice'] = $good['mktprice'];
		$new_good['sellPrice']   = $good['price'];
		$new_good['bn']          = $good['bn'];
		$new_good['name']        = $good['name'];
		$new_good['brand']       = $good['brand'];
		$new_good['intro']       =str_replace('\r','<br>',stripslashes($good['intro']));
        return $new_good;
	}
	/*
		select * from 
		sdb_tag_rel sr,sdb_tags t
		where sr.tag_id = t.tag_id and
		sr.rel_id = 3
	*/
	public function get_tags($good_id)
	{
		$sql  = "select * from  sdb_tag_rel sr,sdb_tags t where sr.tag_id = t.tag_id and sr.rel_id = ".intval($good_id);
		$tags = db_fetch_all($sql);
		return $tags;
	}
	public function get_tag_names($good_id)
	{
		$tags       = $this->get_tags($good_id);
		$new_tags   = array();
		foreach ($tags as $tag) {
			$new_tags[] = $tag['tag_name'];
		}
		return $new_tags;
	}
	public function filter_data($brand_id,$type_id) {
		
		$filter_info = array();
		# 进参 type_id 暂时没有用到
		$sql = "select * from 
				sdb_brand b,
				sdb_type_brand sb,
				sdb_goods_type st 
				where b.brand_id = :brand_id 
				and sb.brand_id = b.brand_id
				and sb.type_id = st.type_id
				and st.name != '通用商品类型'
				";
				
		$sql_params = array(
            'brand_id'=> $brand_id
        );
		$res = db_fetch_all($sql,$sql_params);


		# 商品类型  //1  该品牌的商品类型有哪些 例如 newblance下有什运动鞋 男 运动鞋女
		$good_types = array();
		foreach ($res as $k => $v) {
			$good_types[] = array(
				"id" => $v['type_id'] ,
				"name" => $v['name']
			);
		}
		// good_types
		$filter_info[] = array(
			"name" => "type",
			"displayname" => "类型",
			"items" => $good_types
		);
		
		# brand info 品牌信息 
		$brand_info = array();
		foreach ($res as $k => $v) {
			$brand_info['id']   = $v['brand_id'];
			$brand_info['name'] = $v['brand_name'];
		}
		// brand
		$filter_info[] = array(
			"name" => "brand",
			"displayname" => "品牌",
			"items" => array($brand_info)
		);

		// 商品分类 
		$type_ids = array();
		foreach ($res as $k => $v) {
			$type_ids[] = $v['type_id'];
		}

		$type_id_in_sql = gen_in_sql($type_ids);
		if (empty($type_id_in_sql)){
			throw new Exception("filter_info type id in sql can not be null your type id may be null", 1);
		}

		$sql = " select cat_id as id,cat_name as name from sdb_goods_cat where child_count = 0 and type_id $type_id_in_sql ";
		// cats
		$filter_info[] = array(
			"name" => "cat",
			"displayname" => "分类",
			"items" => db_fetch_all($sql)
		);


		# spec_infos 规格信息 with typeid 这里的规格 可以定制了 因此也可以写成嵌套的不止一个规格的
		$sql = "select sv.spec_value_id,sv.spec_value from 
				sdb_goods_type_spec ss,
				sdb_specification s,
				sdb_spec_values sv
				where 
				ss.spec_id = s.spec_id and
				ss.spec_id = sv.spec_id and
				ss.type_id {$type_id_in_sql} and
				s.spec_name != '颜色'";

		$res = db_fetch_all($sql,$sql_params);
		$specs = array();
		foreach ($res as $k => $v) {
			$tmp = array(
				"id"    =>  $v['spec_value_id'],
				"name"  =>  $v['spec_value']
			);
			$specs[] = $tmp;
		}

		// specs
		$filter_info[] = array(
			"name" => "size",
			"displayname" => "鞋码",
			"items" => $specs
		);

		# 虚拟分类  价格区间 这里写死了 到时候要改的不知道价格会如何呢
		$sql = " SELECT sc.* FROM 
				sdb_goods_virtual_cat sc,sdb_goods_virtual_cat sc1 
				where sc1.virtual_cat_id = sc.parent_id
				and sc1.virtual_cat_name like  '%价格%' ";

		$res = db_fetch_all($sql);

		$virtual_cats_price = array();
		foreach ($res as $k => $v) {
			$tmp = "";
			parse_str($v['filter'],$tmp);

			$tmp_arr = array(
				"id" 		=>  $v['virtual_cat_id'],
				"name" 		=>  $tmp['pricefrom']."-".$tmp['priceto']."元"
			); 
			$virtual_cats_price[] = $tmp_arr;
		}
		
		//virtual_cats_price
		$filter_info[] = array(
			"name" => "price",
			"displayname" => "价格",
			"items" => $virtual_cats_price
		);

		// die(var_dump('$filter_info' ,$filter_info  ));
		return $filter_info;
       	
	}

	// todo 暂时没有使用 请完善需要使用的时候
	public function get_user_member_lv()
	{
		$member_id = intVal($_SESSION['uid']);
		$sql       = " SELECT member_lv from sdb_members where uid = $member_id";
		$member_lv = db_get_one($sql);
		if (!$member_lv) {
			$member_lv = 0;
		}
		return $member_lv;
	}

	 //读取商品评论回复列表
    function getCommentsReply($arr_ids, $display=false){

		if($display) $sql = " AND display = 'true' ";
		$insql            = arr2sqlin( $arr_ids , "for_comment_id" );
		$res              =  db_fetch_all("SELECT * FROM sdb_comments WHERE $insql".$sql." and disabled='false' ORDER BY time");
        foreach ($res as &$row) {
			$row['create_time'] = format_ts($row['time']);
			$row['shop_name']   = get_shop_name(); //这里应该是放店名在config里。
			$row['user_name']   = $row['author'];  
			$row['reply']   	= $row['comment'];  //为了iphone和php端key统一吧
	
		}
		return $res;
    }

    // ios 里ask 代表 comment list, discuss 代表 discuss list
    public function get_comments_count_by_type($goods_id,$item_type="ask")
    {
    	$goods_id									  = intval($goods_id);
    	$sql                                          = "SELECT count(*) FROM sdb_comments WHERE goods_id = $goods_id AND for_comment_id IS NULL AND object_type = '$item_type' and disabled='false' AND display = 'true'";
		$res                                 		  = intVal(db_get_one($sql));
		return 											$res;
    }

    // alter table sdb_comments add column star int(3) not null DEFAULT 1 COMMENT '评论星级' shopex的comments 评论表增加该字段
	 //读取商品首页评论列表
    function getGoodsIndexComments($goods_id, $item='ask'){

		$res                                          = array();
		$res['total']                                 = $this->get_comments_count_by_type($goods_id, $item='ask');
		// 设置为首页20个吧
		$sql                                          = "SELECT * FROM sdb_comments WHERE goods_id = ".intval($goods_id)
		." AND for_comment_id IS NULL AND object_type = '".$item."' and disabled='false' AND display = 'true' ORDER BY p_index ASC, time DESC LIMIT 20 ";
		$res['data']                                  = db_fetch_all($sql);

		// 这里的评论还是得到时候具体说
		foreach ($res['data'] as &$row) {
			$star     = intVal($row['star']);
			$star_chs = "";
			switch ($star) {
				case '1':
					$star_chs = "好评";
					break;
				case '2':
					$star_chs = "中评";
					break;
				case '3':
					$star_chs = "差评";
					break;
				default:
					$star_chs = "好评";
					break;
			}
			$row['star_chs']    = $star_chs;
			$row['create_time'] = format_ts($row['time']);
			$row['user_name']   = $row['author'];  
			$row['ask']   		= $row['comment'];  //为了iphone和php端key统一吧
		}

		return $res;
    }

	// 评论 和 咨询 comment type 有 ask 和 discuss  询问（售前咨询） 和 讨论（评价,评论）
	public function get_comments($goods_id , $comment_type="ask")
	{
		$goods_id  = intVal($goods_id);

		// 这里默认ios 和  android 移动设备打开 评论功能
		$res       = array();

		$comment_list = $this->getGoodsIndexComments($goods_id, $comment_type);
		$res[$comment_type]   = array(
			"data"        => $comment_list['data'] ,
			"total"       => $comment_list['total']
		);
			
		$arr_ids     = array();
		if ($comment_list['total']){
			foreach($comment_list['data'] as $rows){
                $arr_ids[]  = $rows['comment_id'];
            }

            $arr_replys		= array();
            if(count($arr_ids)) {
            	$arr_replys = $this->getCommentsReply($arr_ids, true);
            }
			foreach($comment_list['data'] as $key => $rows){
			 foreach($arr_replys as $rkey => $rrows){
			    if($rows['comment_id'] == $rrows['for_comment_id']){
			        $res[$comment_type]['data'][$key]['replys'][] = $arr_replys[$rkey];
			    }
			 }
			}
		}
		ChromePHP::log( '$res' , $res );
		$data  = $res[$comment_type]['data'];
		return $data;
	}


}

