<?php

class GoodShopexModelApi extends ModelModule {
	
	public function lists( $page = 1,$page_size = 10 ,$sort_type=0,$type_id=null,$brand_id=null,$cat_id=null,$price_from=null,$price_to=null,$spec_values=null)
	{	

		// 左边是 最后显示出的 列名 右边是数据库自己的 注意shopex ecshop是不一样的
		$field_for_assoc = array(
			// "goods_id"    => "goods_id" ,		//商品ID
			// "productCode" => "bn" ,				//商品ID
		
			"image"       => "small_pic" ,	//图片路径
			"bigImage"    => "big_pic" ,	//大图片路径
			"name"        => "name" ,		//商品名
			
			"marketPrice" => "mktprice" ,	//市场价
			"pstatus"     => "marketable" ,	//?有些问题商品状态 0:普通 1:下架 2:秒杀 3:库存不足
			"sellPrice"   => "price" ,		//销售价
			
			"`comment`"   => "comments_count" ,	//评论数
			// "tag"         => "name" ,			//?商品标签(热卖|新品,热卖,新品)?这个去哪里找
			// 这个tag oc里是有的 但是如果加上去显示就不对了 注意清理oc的tableview cell吧
			
			"productCode" => "goods_id" ,	//商品ID
			"brand"       => "brand" ,		//品牌
			"brandId"     => "brand_id" ,	//品牌ID
			"categoryId"  => "cat_id" 		//分类ID
		);

		if (!$brand_id) {
			throw new Exception("brand id can not be null", 1);
		}
		$where        = "  brand_id = :brand_id and disabled = 'false' ";
		$where_params = array();

		if ( $brand_id ) {
			$where_params['brand_id'] = $brand_id;
		}
		// 对类型 还有 价格还有 分类进行查找
		if ( $cat_id ) {
			$where      .= " and cat_id = :cat_id ";
			$where_params['cat_id'] = $cat_id;
		}
		if ( $type_id ) {
			$where      .= " and type_id = :type_id ";
			$where_params['type_id'] = $type_id;
		}

		if ( $price_from ) {
			$where      .= " and price >= :price_from ";
			$where_params['price_from'] = $price_from;
		}
		if ( $price_to ) {
			$where      .= " and price <= :price_to ";
			$where_params['price_to'] = $price_to;
		}

		//尺寸的过滤来着 这里代码需要检查
		$spec_assoc_goods_ids = $this->get_assoc_spec_goods_ids($spec_values);
		// PDO LIKE BIND PARAMETERS
		if ( is_array($spec_assoc_goods_ids) ) {
			$where      .= " and ".arr2sqlin($spec_assoc_goods_ids,"goods_id");
		}

		//  排序问题
		$order_by   = array();
		switch (intval($sort_type)) {
			case 0: 		//按照商品 更新 降序
				$order_by[] = array("uptime" => "desc");
				break;
			case 1: 		//按照商品 更新 升序 暂时不用
				
				break;
			case 2: 		//价格 升序
				$order_by[] = array("price" => "asc");
				break;
			case 3: 		//价格 降序
				$order_by[] = array("price" => "desc");
				break;
			case 4: 		//销量 升序
				$order_by[] = array("price" => "desc");
				break;				
			case 5: 		//销量 升序 暂时不用
				$order_by[] = array("sell_count" => "desc");
				break;
			default:
				$order_by[] = array("uptime" => "desc");
				break;
		}
	
		$table_path = " sdb_goods ";
		$page = new ModelPageHelper($table_path,$field_for_assoc,$page,$page_size,$where,$order_by,$where_params );       
        $res = $page->page()->paged_res();

		return $res;
	}

	public function get_assoc_spec_goods_ids($spec_values)
	{
		if (!is_array($spec_values)) {
			return null;
		}

		$spec_ids = array();
		$good_ids = null;

        foreach( $spec_values as $n ){
            if( $n !== '_ANY_' && $n != false ){
                $spec_ids[] = $n;
            }
        }

        if( count( $spec_ids ) >0 ){

			$sql      = " SELECT goods_id FROM sdb_goods_spec_index WHERE ".arr2sqlin($spec_ids,"spec_value_id");
			$rows     = db_fetch_all($sql);
			$good_ids = array();
            foreach( $rows as $row ){
                $good_ids[] = $row['goods_id'];
        	}
        }
        return $good_ids;
	}

	// 获取规格 抄袭自shopex mdl.gtype.php getSpec 方法
	public function get_specs( $type_id )
	{
		$sql ="select spec_id,spec_style from sdb_goods_type_spec where type_id=".intval($type_id);
		$row = db_fetch_all($sql);
        if ($row){
            foreach($row as $key => $val){
                if($val['spec_style']<>'disabled'){
                    $attachment=array(
                        "spec_style"=>$val['spec_style']
                    );
                    $tmpRow[$val['spec_id']]=$this->getSpecName($val['spec_id'],$attachment);
                }
            }
            return $tmpRow;
        }
        else
            return false;
	}
	

}

