<?php
header("Content-type: text/html; charset=utf8"); 
//http://appapi.loc/api.php?method=shopcats.brand.get
class BrandShopcatsActionApi extends NActionModule {
	
	public function get()
	{
	   	$brands_list_views_data   = M('Brand')->group_by_alphabetically();
	   	$brands_pic_views_data    = M('Brand')->group_by_custom();

		

	   	$res  = array(
				'brand'     => $brands_list_views_data,
				'brand_icon' => $brands_pic_views_data
	   	);
	   	
        return jr("API_RESPONSE_WRAP",$res);
    }

	
}
