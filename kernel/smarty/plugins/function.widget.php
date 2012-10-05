<?php
/*function smarty_function_widget($params, &$smarty){
    include ROOTPATH.'modules/shop/template/widgets/__config.php';
    if(isset($setting[$params['id']])){
        $file = "package:widgets/".$setting[$params['id']]."/default.html";
        echo $smarty->fetch($file);
    }
    
}*/
include_once ROOTPATH.'kernel/functions.php';
function smarty_function_widget($params, &$smarty){
	
    include ROOTPATH.'modules/shop/template/widgets/__config.php';
    if(isset($setting[$params['id']]))
    {
    	//echo "<pre>";print_r($params);
    	if(isset($params['cid'])){
    		$fileter['gc_id'] = $params['cid'];
    		$category = N("ShopsGoodsCategory")->searchGoodsList($fileter);
    		//echo "<pre>";
    		//print_r($category);
    		$smarty->assign("category",$category);
    	}
    	
    	if(isset($params['nums'])){
    		//显示数据的数量
    		$smarty->assign('nums',$params['nums']);
    	}
    	if(isset($params['children']) && $params['children'] == 1){
    		//显示子集
    		$smarty->assign('children',$params['children']);
    	}
        $file = "package:widgets/".$setting[$params['id']]."/default.html";
        echo $smarty->fetch($file);
    }
}

?>
