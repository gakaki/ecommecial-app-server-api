<?php

#http://cloud.github.com/downloads/rfletcher/safari-json-formatter/JSON_Formatter-1.1.safariextz
//http://appapi.loc/api.php?method=home.view.index

class ViewHomeActionApi extends NActionModule {
	
	public function index()
	{
		//建议redis 缓存吧 5分钟缓存一次吧
	   	$res =  M('home')->get_cached_home();
	   	return jr("API_RESPONSE_WRAP",$res);

   }
	
	# 关键词
	# http://appapi.loc/api.php?method=home.view.keywords
	public function keywords()
	{   
		//建议redis 缓存吧 5分钟缓存一次吧
	   	$res =  M('home')->get_custom_hot_keywords();
	   	return jr("API_RESPONSE_WRAP",$res);
    }

    # 关键词 autocomplete 查redis auto complete
	# http://appapi.loc/api.php?method=home.view.keywords_autocomplete&keyword=%E9%9F%A9
	public function keywords_autocomplete()
	{
		$keyword = $_REQUEST['keyword'];
		//建议redis 缓存吧 5分钟缓存一次吧
	   	$res =  M('home')->post_keyword_autocomplete($keyword);
	   	return jr("API_RESPONSE_WRAP",$res);
    }
    
   

}