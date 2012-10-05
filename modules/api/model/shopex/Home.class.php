<?php
//http://appapi.loc/api.php?method=home.view.index
class HomeShopexModelApi extends ModelModule {
	
  public function get_custom_hot_keywords() {
  
    // 这里写死了热门 这个要给用户做后台哦 最好没有的话 那么取虚拟分类吧
    $custom_keywords = array('安踏','李宁','匡威','新百伦','朗蒂维');

    return $custom_keywords;
  }

  public function post_keyword_autocomplete($keyword)
  {
      
      //查商品的关键词吧 以后扩展到对分类等的搜索
      $str_sql  = "SELECT name FROM #pr#goods where name like :keyword or brand like :keyword limit 50 ";
      $str_sql  = rep_db_prefix($str_sql);
      $params   = array('keyword'=>"%".$keyword."%");
      $keywords = DB(2)->fetchAll($str_sql,$params);
      
      $res      = array();  
      foreach ($keywords as $v) {
        $res[] = $v['name'];
      }
      // ChromePHP::log( '$keywords' , $keywords );

      return $res;
  }
	public function get_cached_home() {
	
    $activity_brands  = $this->fake_get_activity_brands();
    $hot_goods        = $this->get_hot_goods();
    $recommend_brands = $this->get_recommend_brands();
    

    $res  = array(

        'activity_brands'        => $activity_brands,
        'hot_goods'              => $hot_goods,
        'recommend_brands'       => $recommend_brands,
        'hot_goods_title'        => "热销TOP15",
        'recommend_brands_title' => "品牌推荐",
    );
    
		return $res;
	}
  public function fake_get_activity_brands()
  {
    $field_for_assoc = array(
      "brand_logo"  => "brand_logo" ,
      "brand_name"  => "brand_name" ,
      "brand_id"    => "brand_id"
    );
    $in_arr          = array('安踏','李宁','匡威','New Balance','朗蒂维');
    $in_arr_sql      = arr2sqlin($in_arr,"brand_name");

    $page            = new ModelPageHelper("sdb_brand",$field_for_assoc,$page = 1,$page_size = 5,$in_arr_sql);   
    $paged           = $page->page();
    $res             = $paged->res();

    foreach ($res as $k  => $item) {
      unset($res[$k]['brand_logo']);
      if ($item['brand_name']=="朗蒂维") {
        $res[$k]['brand_image'] = "http://images.s.cn/images/mobi/20120426/a5cc0e53b762a5bd_320.jpg";
      }
      if ($item['brand_name']=="匡威") {
        $res[$k]['brand_image'] = "http://images.s.cn/images/mobi/20120426/54eae5efc372652c_320.jpg";
      }
      if ($item['brand_name']=="李宁") {
        $res[$k]['brand_image'] = "http://images.s.cn/images/mobi/20120426/3e5abb9dc97df1c5_320.jpg";
      }
      if ($item['brand_name']=="安踏") {
        $res[$k]['brand_image'] = "http://images.s.cn/images/mobi/20120426/db3e0af135d55b11_320.jpg";
      }
      if ($item['brand_name']=="New Balance") {
        $res[$k]['brand_image'] = "http://images.s.cn/images/mobi/20120426/176d07e64a156a2d_320.jpg";
      }


    }
    return $res;
  }
  //热卖精品 促销 这个需要做后台的 这里的图片让他先显示吧 没办法
  public function get_activity_brands()
  {
    $page_size = 6;
    $table_name      ='brand';

    $field_for_assoc = array(
      "brand_logo"  => "brand_logo" ,
      "brand_name"  => "brand_name" ,
      "brand_id"    => "brand_id"
    );
    
    $page            = new ModelPageHelper($table_name,$field_for_assoc,$page = 1,$page_size = 4 );   
    $paged           = $page->page();
    $res             = $paged->res();
    
    return $res;
  }
  //热销TOP15
  public function get_hot_goods()
  {

      $str_sql = "SELECT g.goods_id as good_id,g.name as good_name,g.thumbnail_pic as good_logo,CAST(g.price AS unsigned) as good_price FROM #pr#tags t,#pr#tag_rel tr,#pr#goods g where t.tag_id = tr.tag_id and tr.rel_id = g.goods_id and t.tag_name = '热卖排行' and g.disabled <> 1 order by g.p_order desc limit 15";

      $top_goods = db_fetch_all($str_sql);

      return $top_goods;
  }

  //品牌推荐
  public function get_recommend_brands()
  {

    $table_name      ='#pr#brand';

    $field_for_assoc = array(
      "brand_logo"  => "brand_logo" , 
      "brand_name"  => "brand_name" ,
      "brand_id"    => "brand_id"
    );
    
    $page            = new ModelPageHelper($table_name,$field_for_assoc,$page = 1,$page_size = 4 );   
    $paged           = $page->page();
    $res             = $paged->res();
 
    return $res;
  }


 
}






