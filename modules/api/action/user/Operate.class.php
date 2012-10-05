<?php
header("Content-type: text/html; charset=utf8"); 

class OperateUserActionApi extends NActionModule {
	
	//http://appapi.loc/api.php?method=user.operate.test_insert
	public function test_insert()
	{
		$sql = " insert into #pr#members set uname =:uname ";
	    $sql = rep_db_prefix($sql);
	    $params = array(
      		'uname'    => '12121221'
    	);
	    try {
	      $res = DB()->query($sql,$params);

	      var_dump($res,DB()->lastInsertId());die;

	    } catch (Exception $e) {
	      echo $e;
	    }

	}

	//http://appapi.loc/api.php?method=user.operate.logout
	public function logout()
	{
		$_SESSION['uid'] = null;
		unset($_SESSION['uid']);

		$res        = array();
		$res['msg'] = '退出成功';
		return jr("API_RESPONSE_WRAP",$res);
	}

	//http://appapi.loc/api.php?method=user.operate.ucenter
	public function ucenter()
	{
		$uid = $_SESSION['uid'];
		
		if ($uid) {
			//login成功
			$res_arr         = M('User')->get_user_info($uid);
			$res_arr['msg']  = '使用session获取用户信息成功';
			$_SESSION['uid'] = $res_arr['member_id'];
			return jr("API_RESPONSE_WRAP",$res_arr);
		}
		else{
			$err_arr = array(
				"error_code" =>  '009',
				"error_info" =>  "SESSION验证失效 用户认证信息可能过期"
			);
			return jr("API_RESPONSE_WRAP",$err_arr);
		}
		return jr("API_RESPONSE_WRAP",$res_arr);
	}

	//http://appapi.loc/api.php?method=user.operate.register&username=gakaki@gmail.com&password=z5896321	
	public function register()
	{
		$name  = $_REQUEST['username'];
		$pwd   = $_REQUEST['password'];

		//根据用户名 删除用户数据 纯测试使用
		M('User')->test_del_user_by_uname($name);
		
		if (empty($name) || empty($pwd) ) {

			$err_arr = array(
				"error_code" =>  '003',
				"error_info" =>  "用户名和密码不能为空"
			);
			return jr("API_ERROR_DETAIL",$err_arr);
		}

		if ( M('User')->user_name_is_exist($name) ) {

			$err_arr = array(
				"error_code" =>  '004',
				"error_info" =>  "用户名已经被注册"
			);
			return jr("API_ERROR_DETAIL",$err_arr);
		}
		
		//若用户名是邮件地址那么判断邮件地址正确性
		//邮件格式是否正确
		if (strpos($name, '@')) { 
			if ( !M('User')->validate_uname_email($name) ) {

				$err_arr = array(
					"error_code" =>  '005',
					"error_info" =>  "邮件格式不正确"
				);
				return jr("API_ERROR_DETAIL",$err_arr);
			}
		}

		//若用户名是手机号码那么根据手机号码进行验证
		if (preg_match('/^\d+$/', $name )) { 
			//手机号码是否重复
			if ( M('User')->user_uname_phone_is_exist($name) ) {

				$err_arr = array(
					"error_code" =>  '006',
					"error_info" =>  "重复的手机号码"
				);
				return jr("API_ERROR_DETAIL",$err_arr);
			}
		}
		

		$new_user_id   = M('User')->reg( $name , $pwd );

		if ($new_user_id) {
			//注册成功之后要返回用户信息来着
			$res_arr        = M('User')->get_user_info($new_user_id);
			
			$res_arr['msg'] = '注册成功';

			$_SESSION['uid'] = $res_arr['member_id'];

			return jr("API_RESPONSE_WRAP",$res_arr);
		}
		else{
			$err_arr = array(
				"error_code" =>  '007',
				"error_info" =>  "注册失败，具体错误原因待验证！数据写入错误"
			);
			return jr("API_ERROR_DETAIL",$err_arr);
		}

    }


	//http://appapi.loc/api.php?method=user.operate.login&username=gakaki@gmail.com&password=z5896321
	public function login()
	{

		$name = $_REQUEST['username'];
		$pwd  = $_REQUEST['password'];

	    $res  = M('User')->validate_login( $name , $pwd );
	    
	    if ($res) {
			//login成功之后要返回用户信息来着
			$res_arr         = M('User')->get_user_info($res['member_id']);
			$res_arr['msg']  = '登入成功';
			$_SESSION['uid'] = $res_arr['member_id'];
			return jr("API_RESPONSE_WRAP",$res_arr);
		}
		else{
			$err_arr = array(
				"error_code" =>  '008',
				"error_info" =>  "登入失败，用户名或密码填写错误"
			);
			return jr("API_ERROR_DETAIL",$err_arr);
		}
    }
	
}
