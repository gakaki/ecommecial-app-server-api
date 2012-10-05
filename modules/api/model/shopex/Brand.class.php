<?php

//http://appapi.loc/api.php?method=shopcats.brand.get&page=1&page_size=3
class BrandShopexModelApi extends ModelModule {
	
	public function top_five() {
	
		$sql = " select * from #pr#brand ";
		$res = DB()->fetchAll($sql);
		return $res;
	}

	public function all($where="",$is_need_wrap=false)
	{
        $table_name='#pr#brand';
        $field_for_assoc = array(
            "image"   => "brand_logo" ,
            "name"    => "brand_name" ,
            "brandId" => "brand_id"
        );
        if ($where) {
          $where = "  $where ";
        }
        
        $page = new ModelPageHelper($table_name,$field_for_assoc,$page = 1,$page_size = 999999999,$where);   
        $paged = $page->page();
        if ($is_need_wrap) {
         $res  =  $paged->Wrap()->res();
        }else{
          $res  =  $paged->res();
        }

        return $res;
	}
  
	public function group_by_custom()
  {
    /*
{ classify_name: '运动户外',
  hot: ''耐克','阿迪达斯','Kappa','匡威','李宁','NINE WEST','安踏'',
  normal: ''彪马','迈乐','锐步','K-SWISS','茵宝','Aderson','斯凯奇','CAT','Skomart','VANS','New Balance','easyspirit','回力','骆驼','暇步士','Clarks','朗蒂维','渥弗林'' }
res { classify_name: '男鞋',
  hot: ''Kappa','李宁'',
  normal: ''暇步士','Clarks','Skomart','朗蒂维','骆驼','渥弗林','圣大保罗'' }
res { classify_name: '女鞋',
  hot: ''达芙妮','NINE WEST','星期六'',
  normal: ''迈乐','暇步士','Aderson','斯凯奇','CAT','Clarks','Skomart','Millies','ELLE','BCBG','Scholl','VAGO','Nina','EQ:IQ','ENZO ANGIOLINI','BANDOLINO','AK ANNE KLEIN','CAROLINNA ESPINOSA','easyspirit','STEVE MADDEN','JOAN DAVID','ANNE KLEIN NEW YORK','茵奈儿','他她','百丽','天美意','骆驼','SAFIYA','SIXTY NINE'' }

    */

      $outdoor_sport_hot = array('耐克','阿迪达斯','Kappa','匡威','李宁','NINE WEST','安踏');
      $outdoor_sport_all = array( '彪马','迈乐','锐步','K-SWISS','茵宝','Aderson','斯凯奇','CAT','Skomart','VANS','New Balance','easyspirit','回力','骆驼','暇步士','Clarks','朗蒂维','渥弗林' );
      
      $male_hot          = array('Kappa','李宁');
      $male_all          = array('暇步士','Clarks','Skomart','朗蒂维','骆驼','渥弗林','圣大保罗');
      
      $female_hot        = array('达芙妮','NINE WEST','星期六');
      $female_all        = array('迈乐','暇步士','Aderson','斯凯奇','CAT','Clarks','Skomart','Millies','ELLE','BCBG','Scholl','VAGO','Nina','EQ:IQ','ENZO ANGIOLINI','BANDOLINO','AK ANNE KLEIN','CAROLINNA ESPINOSA','easyspirit','STEVE MADDEN','JOAN DAVID','ANNE KLEIN NEW YORK','茵奈儿','他她','百丽','天美意','骆驼','SAFIYA','SIXTY NINE');
      
      
      $outdoor_sport_hot = arr2sqlin($outdoor_sport_hot,"brand_name");
      $outdoor_sport_all = arr2sqlin($outdoor_sport_all,"brand_name");
      
      $male_hot          = arr2sqlin($male_hot,"brand_name");
      $male_all          = arr2sqlin($male_all,"brand_name");
      
      $female_hot        = arr2sqlin($female_hot,"brand_name");
      $female_all        = arr2sqlin($female_all,"brand_name");
      

      $classifysy        = array();
      $classifysy_vars   = get_defined_vars();
      foreach ($classifysy_vars as $k => $in_sql) {

        $classifysy[$k]  = $this->all( $in_sql );
      }


    {
      // replace all data image with new image add ress
      foreach ($classifysy as $k => $in_sql) {
        $tmp = $classifysy[$k];
        for ($i=0; $i < count($tmp); $i++) { 
          $tmp[i]['image'] = str_replace("45", '103',$tmp[i]['image']);
        }
      }
    }

      
      return $classifysy;
  }
   public function group_by_alphabetically()
   {	
       
       $brands  = $this->all();

       $py = new PYInitials();
       $letters = array();

       foreach ($brands as $brand) {	
           $brand_name   = $brand['name'];
           $first_letter = $py->get_first_letter( $brand_name );
           if(empty($letters[$first_letter])) {
                $letters[$first_letter]   = array();
           }
           $letters[$first_letter][] = $brand;
       }
       ksort($letters);
       
       $brands = $letters;
      

       $alphabetically_keys = array_keys($brands);
       $alphabetically_res_arr = array();
       foreach ($brands as $k => $v) {
          $alphabetically_res_arr[] = $v;
       }

       return array(
            'sections'=>$alphabetically_keys,
            'rows'=>$alphabetically_res_arr
       );
   }

   public function get_cats($brand_id)
   {
      $sql = " select * from #pr#brand ";
      $res = DB()->fetchAll($sql);
      return $res;
   }
   public function filter_info($brand_id)
   {
      $cats = $this->get_cats($brand_id);



   }

}





