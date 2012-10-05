<?php


class UserShopexModelApi extends ModelModule {
	
	public function validate_login($username,$password) {
	 

    $sql = " select * from #pr#members where uname =:uname and password =md5(:password) ";
      
    $params = array(
      'uname'    => $username,
      'password' => $password
    );
    $sql = rep_db_prefix($sql);
    $res = DB()->fetchOne($sql,$params);

    $last_res = array();
    if ($res && $res['password']) {
      unset($res['password']);
      $last_res['member_id']  = $res['member_id'];
      $last_res['username']   = $res['uname'];
      $last_res['class']      = $this->get_member_lv_name($res['member_lv_id']);
      $last_res['consume']    = $this->get_member_total_amount($res['member_id']);
      $last_res['couponNum']  = $this->get_member_coupon_count($res['member_id']);
      $last_res['newMessage'] = $this->get_new_message_num($res['member_id']);

    }

    // var_dump($last_res,$res,$sql,$params);die;
		return $last_res; 
	}


  //添加到收藏夹
  public function add_favotite( $goods_id,$member_id )
  {
    $fav_add_res = false;

    $sql = "SELECT addon FROM sdb_members WHERE member_id=:member_id";
    $params = array(
      'member_id'    => $member_id
    );
    
    $addon = db_get_one($sql,$params);

    if( !empty($addon) ){

        $addon   = unserialize($addon);
        
        $fav_ids = $addon['fav'];
        // die(var_dump('$fav_ids' , $fav_ids ));
        if( $fav_ids ){

              $fav_ids[]    = "$goods_id";   //不知道这里为啥数据库出来是string类型           
              $fav_ids      = array_unique($fav_ids); 
              
              // die(var_dump( $addon['fav'] , $fav_ids ,$goods_id) );
              $addon['fav'] = $fav_ids;
              $addon        = serialize( $addon );
              // $addon        = str_replace('"', '\""', $addon);
              $sql          = " update sdb_members set addon='$addon' where member_id=$member_id ";
              die(var_dump('$sql' , $sql ));
              // die(var_dump('$params' , $params ));
              $fav_add_res  = db_query( $sql,$params );//db query有问题的
              
        }
    }

    return $fav_add_res;
  }

  public function get_fav_list($page,$page_size,$member_id)
  {
    $fav_res = array();
    $sql = "SELECT addon FROM #pr#members WHERE member_id=:member_id";
    $params = array(
      'member_id'    => $member_id
    );
    $sql   = rep_db_prefix($sql);
    $addon = DB()->getOne($sql,$params);

    if( !empty($addon) ){

        $addon   = unserialize($addon);
        $fav_ids = $addon['fav'];
        if( $fav_ids ){

              $field_str      = " g.*,i.thumbnail ";
              $table_path     = " sdb_goods as g
              left join sdb_gimages as i on 
              g.image_default = i.gimage_id ";
              $where_sql      = "g.goods_id ".gen_in_sql( $fav_ids );
              $page           = new ModelPageHelper($table_path,$field_str,$page = 1,$page_size = 10,$where_sql);   
              $paged          = $page->page();
              $fav_res        = $paged->paged_res();
              
              
              $user_info      = $this->get_user_info($member_id);
              $member_lv_id   = $user_info['membmer_lv_id'];
              $fav_res        = $this->getSparePrice( $fav_res, $member_lv_id );
        }
        return $fav_res;
    }

    return $fav_res;
  }

  function getSparePrice(&$list,$memberLevel,$onMarketable = true ){
      if(count($list)>0){
          $level='';
          if($memberLevel){
              $level='and B.level_id='.$memberLevel;

              $oLv = $object->system->loadModel('member/level');
              $aLevel = $oLv->getFieldById($memberLevel, array('dis_count'));
              if(floatval($aLevel['dis_count']) <= 0) $aLevel['dis_count'] = 1;
          }
          $id=array();
          foreach($list as $p=>$q){
              $id[]=intval($q['goods_id']);
          }
          $all=implode(",",$id);
          $sql='SELECT A.product_id,A.goods_id,A.pdt_desc,A.price,B.price as m_price,A.store,A.freez,A.marketable FROM sdb_products A';
          $sql.=' LEFT JOIN sdb_goods_lv_price B ON A.product_id=B.product_id '.$level;
          $sql.=' WHERE A.goods_id IN ('.$all.') order by A.goods_id';
          $price_gid=array();
          $store_gid=array();
          $freez_gid=array();
          $marketable_gid=array();
          $oMath = $object->system->loadModel('system/math');
          foreach($object->db->select($sql) as $q1=>$v1){
              $price_gid[$v1['product_id']]=$v1['m_price']?$v1['m_price']:($v1['price']*$aLevel['dis_count']);
              $price_gid[$v1['product_id']]= $oMath->getOperationNumber($price_gid[$v1['product_id']]);
              $price_goodsid[$v1['goods_id']]= $oMath->getOperationNumber($price_gid[$v1['product_id']]);
              $store_gid[$v1['product_id']]=$v1['store'];
              $freez_gid[$v1['product_id']]=$v1['freez'];
              $marketable_gid[$v1['product_id']]=$v1['marketable'];
          }
          foreach($list as $k => $aRow){
              $list[$k]['pdt_desc'] = unserialize($list[$k]['pdt_desc']);
              if(is_array($list[$k]['pdt_desc'])){
                  foreach($list[$k]['pdt_desc'] as $q=>$v){
                      if( $onMarketable && $marketable_gid[$q] == 'false'){
                          unset($list[$k]['pdt_desc'][$q]);
                          continue;
                      }
                      $list[$k]['pdt_desc'][$q]=stripslashes($list[$k]['pdt_desc'][$q]);
                      $list[$k]['pdt_desc']['marketable'][$q]=$marketable_gid[$q];
                      $list[$k]['pdt_desc']['price'][$q]=$price_gid[$q];
                      $list[$k]['pdt_desc']['store'][$q]=$store_gid[$q];
                      $list[$k]['pdt_desc']['freez'][$q]=$freez_gid[$q];
                  }
              }else{
                  $list[$k]['price'] = $price_goodsid[$aRow['goods_id']];
              }
          }
      }
      return $list;
  }
  //判断手机号码是否重复
  public function user_uname_phone_is_exist($uname_phone)
  {
    $uname_phone_is_exist_res = false;

    $params = array(
      'uname'     => $uname
    );

    $sql = " select * from #pr#members where uname = :uname  ";
    $sql = rep_db_prefix($sql);
    $res = DB()->fetchOne($sql,$params);
    // var_dump($res);die;
    if ($res) {
      $uname_phone_is_exist_res = true;
    }

    return $uname_phone_is_exist_res;
  }


  public function validate_uname_email($uname_email)
  {

    if (validEmail($uname_email)) {
      return true;
    }
    return false;
  }
  public function user_name_is_exist($uname)
  {
    
    $user_name_is_exist_res = false;

    $params = array(
      'uname'     => $uname
    );

    $sql = " select * from #pr#members where uname = :uname  ";
    $sql = rep_db_prefix($sql);
    $res  = DB()->fetchOne($sql,$params);
    // var_dump($res);die;
    if ($res) {
      $user_name_is_exist_res = true;
    }

    return $user_name_is_exist_res;
  }

  public function test_del_user_by_uname($uname)
  {
    $sql    = " delete from #pr#members where uname =:uname  ";
    $params = array('uname' => $uname);
    $sql    = rep_db_prefix($sql);
    $res    = DB()->query($sql,$params);
  }

  public function get_user_info($member_id)
  {
    
    $params = array(
      'member_id'     => $member_id
    );

    $sql = " select m.*,ml.name as ml_name from 
            #pr#members m,
            #pr#member_lv ml 
            where m.member_id =:member_id
            and ml.member_lv_id = m.member_lv_id  ";

    $sql = rep_db_prefix($sql);
    $res = DB()->fetchOne($sql,$params);
    //var_dump($sql,$res,$uname);die;

    $last_res = array();
    if ($res && $res['password']) {
      unset($res['password']);

      $last_res['member_id']  = $res['member_id'];
      $last_res['username']   = $res['uname'];
      $last_res['class']      = $res['ml_name'];
      $last_res['consume']    = $this->get_member_total_amount($res['member_id']);
      $last_res['couponNum']  = $this->get_member_coupon_count($res['member_id']);
      $last_res['newMessage'] = $this->get_new_message_num($res['member_id']);

    }

    return $last_res;
  }
  public function get_new_message_num($member_id)
  {
      $sql = " SELECT count(*) AS unreadmsg FROM #pr#message WHERE to_type = 0 AND del_status !='1' AND folder ='inbox' AND unread ='0' AND to_id =:member_id ";
      $sql = rep_db_prefix($sql);
      $params = array(
        'member_id'     => $member_id
      );
      $sql = rep_db_prefix($sql);
      $res = DB()->getOne($sql,$params);
      if (!$res) {
        $res = 0;
      }
      return $res;
  }
  public function get_new_messages($member_id)
  {
      $sql = " SELECT * FROM #pr#message WHERE to_type = 0 AND del_status !='1' AND folder ='inbox' AND unread ='0' AND to_id =:member_id ";
      $sql = rep_db_prefix($sql);
      $params = array(
        'member_id'     => $member_id
      );
      $sql = rep_db_prefix($sql);
      $res = DB()->fetchAll($sql,$params);

      $new_messages = array();
      if (!empty($res)) {
        foreach ($res as $message) {
          
          $new_messages[]         = $this->message_format($message);
        }
      }
      return $new_messages;
  }
  public function message_format($message_arr)
  {
      $last_res               = array();
      $last_res['message_id'] = $message_arr['msg_id'];
      $last_res['created_at'] = date( "Y-m-d H:i:s",$message_arr['date_line']);
      $last_res['title']      = $message_arr['subject'];
      $last_res['content']    = $message_arr['message'];
      $last_res['status']     = $message_arr['unread'];//0 是未读 1是已读
      return                  $last_res;
  }
  public function get_message_detail($msg_id)
  {
      $sql = " SELECT * FROM #pr#message WHERE msg_id =:msg_id ";
      $sql = rep_db_prefix($sql);
      $params = array(
        'msg_id'     => $msg_id
      );
      $res = DB()->fetchOne($sql,$params);

      $last_res               = array();
      if (!empty($res)) {
          $last_res  = $this->message_format($res);
      }

      // die(var_dump('$res,$sql,$msg_id,$last_res' , $res,$sql,$msg_id,$last_res ));
      return $last_res;
  }

  public function get_member_coupon_count($member_id)
  {
      $sql = " SELECT count(*) FROM #pr#member_coupon as mc
                left join #pr#coupons as c on c.cpns_id=mc.cpns_id
                left join #pr#promotion as p on c.pmt_id=p.pmt_id
                WHERE member_id=:member_id ORDER BY mc.memc_gen_time DESC ";

      $params = array(
        'member_id'     => $member_id
      );

      $sql = rep_db_prefix($sql);
      return $res = DB()->getOne($sql,$params);
  }
  public function get_member_total_amount($member_id)
  {
      $sql = " select sum(total_amount) from #pr#orders where member_id=:member_id  ";
      $sql = rep_db_prefix($sql);
      $params = array(
        'member_id'    => $member_id
      );
      $res = DB()->getOne($sql,$params);
      if (!$res) {
        $res = 0;
      }
      return $res;
  }
  public function get_member_lv_name($member_lv_id)
  {
    $sql = " select name from #pr#member_lv where member_lv_id=:member_lv_id  ";
    $params = array(
        'member_lv_id'    => $member_lv_id
    );
    $sql = rep_db_prefix($sql);
    $res = DB()->getOne($sql,$params);
    return $res;
  }

	public function reg($uname,$password)
	{
    
    $reg_res = false;

    $params = array(
      'uname'    => $uname,
      'password' => $password
    );

    #若用户名是邮件地址格式那么 插入数据库的时候补全到email字段里
    if ($this->validate_uname_email($uname)) {
      $params['email'] = $uname;
    }else{
      $params['email'] = "";
    }
    //默认member lv id = 1 普通会员等级
    $sql = " insert into #pr#members set uname =:uname , password =md5(:password) ,email=:email ,member_lv_id = 1";
    $sql = rep_db_prefix($sql);
    try {
      $res = DB()->query($sql,$params);

    } catch (Exception $e) {
      echo $e;
    }
    
    if ($res) {
      $reg_res = DB()->lastInsertId();
    }

    return $reg_res;
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


}






