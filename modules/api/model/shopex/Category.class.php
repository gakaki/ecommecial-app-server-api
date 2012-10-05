<?php
class CategoryShopexModelApi extends ModelModule {
	
	public function top_five() {
	
		$sql = " select * from #pr#brand ";
		$res = DB()->fetchAll($sql);
		return $res;
		
	}

	
	public function all($parent_id=0, $page,$page_size)
	{	
			$field_for_assoc = array(
				"categoryId" => "cat_id" ,
				"name"       => "cat_name" ,
				"fatherId"   => "parent_id" ,

				"childNum"   => "child_count" ,
				"disabled"   => "disabled",
				"is_leaf"    => "is_leaf"
			);

			$where = "  parent_id = $parent_id and disabled = 'false' ";

			$page = new ModelPageHelper($table_name='#pr#goods_cat',$field_for_assoc,$page,$page_size,$where);       
        	$res = $page->page()->res();

        	
			return $res;
	}


	public function get_fileds_by_ids($ids, $fields=array('*')) {

		$sql = "SELECT ".gen_fields($fields)." FROM sdb_goods_cat WHERE ";

        if(is_array($ids)){
            $sql = $sql.arr2sqlin( $ids,"cat_id" );
            return db_fetch_all($sql);
        }else{
            $sql = $sql." cat_id = ".intval($ids);
            return db_fetch_one($sql);
        }
    }
    
	public function get_cats_child_and_self($cat_ids)
    {   
		$arr_cats     = $this->get_fileds_by_ids( $cat_ids , array('cat_path','cat_id') );
		$cat_id_insql = arr2sqlin( $cat_ids,"cat_id" );

		$pathplus =" cat_path LIKE '";
        if(count($arr_cats)){
            foreach($arr_cats as $v){

            	if ( $v['cat_path']==',' ) {
            		
            	}else{
            		$pathplus .= $v['cat_path'];
            	}

            	$pathplus .= $v['cat_id'].",%' OR";
            }
        }

        $res_cat_ids = array();
        if($arr_cats){
        	$sql      = " SELECT cat_id FROM sdb_goods_cat WHERE '.$pathplus.' or  $cat_id_insql ";
        	$res	  = db_fetch_all($sql);
            foreach($res as $rows){
                $res_cat_ids[] = $rows['cat_id'];
            }
        }

        if(in_array('0', $cat_ids){
            $res_cat_ids[] = 0;
        }

        $where_sql  = arr2sqlin( $res_cat_ids,"cat_id" );
        return $where_sql;
    }


}

