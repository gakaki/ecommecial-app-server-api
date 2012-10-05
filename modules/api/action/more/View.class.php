<?php


class ViewMoreActionApi extends NActionModule {
	
	
	# 系统帮助列表 这个参考麦包包版本进行复制吧
    # http://appapi.loc/api.php?method=more.view.help_list
	public function help_list()
	{
		//建议redis 缓存吧 5分钟缓存一次吧
	   	$res = $this->gen_help_content();
		
	   	return jr("API_RESPONSE_WRAP",$res);
    }

	# 收藏夹列表
    # http://appapi.loc/api.php?method=more.view.fav_list
	public function fav_list()
	{
		$member_id = $_SESSION['uid'];
		$member_id = 111;
		$page      = intval($_REQUEST['page']);
		$page_size = intval($_REQUEST['page_size']);
		$res       = M('user')->get_fav_list($page,$page_size,$member_id);
		
	   	return jr("API_RESPONSE_PAGE",$res);
    }
    # 系统帮助列表 对应文章管理公告以及活动信息
    # http://appapi.loc/api.php?method=more.view.news_list 
	public function news_list()
	{
		//建议redis 缓存吧 5分钟缓存一次吧
	   	$res =  M('more')->article_list();
	   	return jr("API_RESPONSE_WRAP",$res);
    }

    # 用户站内信息
    # http://appapi.loc/api.php?method=more.view.new_message_list 
	public function new_message_list()
	{
		$member_id  =  $_SESSION['uid'];
	   	
	   	if ($member_id) {
			$res =  M('user')->get_new_messages($member_id);
	   		return jr("API_RESPONSE_WRAP",$res);
		}
		else{
			$err_arr = array(
				"error_code" =>  '010',
				"error_info" =>  "获取用户session uid错误 可能是session失效"
			);
			return jr("API_ERROR_DETAIL",$err_arr);
		}
    }

    # 用户站内信息明细
    # http://appapi.loc/api.php?method=more.view.message_detail&message_id=2
    public function message_detail()
    {
    	$message_id  = $_REQUEST['message_id'];
	   	if ($message_id) {
			$res =  M('user')->get_message_detail($message_id);
	   		return jr("API_RESPONSE_WRAP",$res);
		}
		else{
			$err_arr = array(
				"error_code" =>  '011',
				"error_info" =>  "没有提供站内短信息id"
			);
			return jr("API_ERROR_DETAIL",$err_arr);
		}
    }

    //用户意见反馈 需要用户注册登入
    # http://appapi.loc/api.php?method=more.view.leave_message
    public function leave_message()
    {
    	
    }
    

    //帮助信息假数据
    public function gen_help_content()
    {
    	$res =  array();

	   	$res[0] = array(
				"id"      =>  1,
				"name"    =>  "名鞋库如何确保正品鞋？",
				"content" =>  "1.完善的供应链&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;<br/>2.正规的采购渠道&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;<br/>3.入仓全检正品保障&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;"
	   	);
	   	$res[1] = array(
				"id"      =>  2,
				"name"    =>  "收到货后不满意怎么办？",
				"content" =>  '<strong style=""><span style="font-weight: normal;"><strong>&#xb7;免费拒收&nbsp;&nbsp;&nbsp; <br/>&#xb7;自由退换货<br/></strong></span></strong>'
	   	);
	   	$res[2] = array(
				"id"      =>  3,
				"name"    =>  "对商品有疑惑，如何联系售后客服？",
				"content" =>  "拨打名鞋库全国客服热线400-800-2222转2进行联系。"
	   	);
	   	$res[3] = array(
				"id"      =>  4,
				"name"    =>  "什么是“假一赔十”服务承诺？",
				"content" =>  "基于当前中国知识产权现状，顾客朋友对网上销售商品存在的种种疑虑，我们深表理解，为此，名鞋库自信承诺：确保正品，假一赔十（赔付金额=商品销售金额的十倍）！"
	   	);
	   	$res[4] = array(
				"id"      =>  5,
				"name"    =>  "如何成为麦包包会员",
				"content" =>  "点击页面顶部“注册”，进入注册页面；填写邮箱、密码等个人信息完成注册。"

	   	);
	   	$res[5] = array(
				"id"      =>  6,
				"name"    =>  "如何购买并提交订单 ",
				"content" =>  '选择您所要购买的商品，点击“加入购物车”，可将多件商品“加入购物车”后统一结算。点击"去结算",准确填写收货人的姓名、地址、邮编、电话等有效信息。未登入状态，结算时
				将提醒您进行登入'
	   	);
	   	$res[6] = array(
				"id"      =>  7,
				"name"    =>  "如何付款？",
				"content" =>  "麦包包iphone版手机客户端支持货到付款，支付宝和百付天下三种支付方式。若您选择 ‘货到付款’请在收货时将货款交予快递人员;若选择‘支付宝’或‘银联手机在线支付’，请按流程完成支付操作，之后快递人员会送货上门"
	   	);
	   	$res[7] = array(
				"id"      =>  8,
				"name"    =>  "如何办理退换货",
				"content" =>  "如果您的订单需要办理退货，请您先通过电话或邮件的方式与麦包包客服中心联系，确认退换货流程 。客服电话：4006-528-528.客服邮箱：mbb@mbaobao.com"
	   	);
	   	$res[8] = array(
				"id"      =>  9,
				"name"    =>  "如何联系麦包包？",
				"content" =>  "1.客服电话 4006-528-528 （周一～周五 09:00－23:00　|　周六、周日以及公共假期 10:00－22:00）2.客服邮箱：mbb@mbaobao.com"
	   	);
	   	return $res;
    }
}