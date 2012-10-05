<?php
class BrandEcshopModelApi extends ModelModule {
	
	public function top_five() {
	
		$sql = " select * from #pr#brand ";
		$res = DB()->fetchAll($sql);
		return $res;

	}

	public function all($page,$page_size)
	{
			$page                                    = !$page?1:intval($page);
			//每页显示10条数据
			$page_size                               = !$page_size?PAGE_SIZE:intval($page_size);
			
			//获取limit的第一个参数的值 offset，假如第一页则为(1-1)*10 =0,第二页为(2-1)*10=10。
			//(传入的页数-1) * 每页的数据 得到limit第一个参数的值
			$offset                                  = ($page-1)*$page_size;
			
			
			$sql                                     = "SELECT SQL_CALC_FOUND_ROWS * from #pr#brand limit $offset,$page_size";
			$sql_total_row                           = "SELECT FOUND_ROWS()";
			
			$res                                     = DB()->fetchAll($sql);
			$total                                   = DB()->getOne($sql_total_row);
		
			//获得总页数 total_page
			$total_page                              =  ceil($total/$page_size);
			
			$data                                    =  array(
				'total'                                  => $total,
				'res'                                    => $res,	
				'page'                                   => $page,
				'page_size'                              => $page_size,
				'total_page'                             => $total_page
			);
			return $data;
	}
}




		
		/*
				SELECT SQL_CALC_FOUND_ROWS * FROM `sdb_brand` limit 0, 10;
	   		SELECT FOUND_ROWS();
		*/