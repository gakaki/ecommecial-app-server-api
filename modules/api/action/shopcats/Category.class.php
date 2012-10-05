<?php


//http://appapi.loc/api.php?method=shopcats.category.get
class CategoryShopcatsActionApi extends NActionModule {
	
	public function get()
	{

		$page            = $_REQUEST['page'];
		$page_size       = $_REQUEST['page_size'];
		$parent_id       = intVal($_REQUEST['fatherId']);
		
		$field_str       = ' cat_id,cat_name ';
		
		$res             =  M("category")->all($parent_id,$page,$page_size,$field_str);
		
		//这里没办法为了显示图片路径
		if ($res[0])
			$res[0]['image'] = "http://appapi.loc/statics/images/www.s.cn_images_mobi_cat_icon_17_40.png";
		if ($res[1])
			$res[1]['image'] = "http://appapi.loc/statics/images/www.s.cn_images_mobi_cat_icon_26_40.png";
		if ($res[2])
			$res[2]['image'] = "http://appapi.loc/statics/images/www.s.cn_images_mobi_cat_icon_51_40.png";
		
		return jr("API_RESPONSE_WRAP",$res);
   }
	//http://www.blogabc.net/i451_整理一下杂七杂八的postfix%2Bextmail的mail服务器架设维护笔记(1).htm
	//http://www.extmail.org/forum/thread-4387-1-1.html
   
}