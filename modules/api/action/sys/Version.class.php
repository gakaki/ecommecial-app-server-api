<?php
define(CURRENT_VERSION, 2.3);
define(APP_STORE_URL, "http://itunes.com/1231312");
define(FORCE_UPDATE, 0);

//http://appapi.loc/api.php?method=sys.version.update
class VersionSysActionApi extends NActionModule {

	public function is_need_update($client_version)
	{	
		
		$version_compare = floatval($client_version) < CURRENT_VERSION ? true:false;
		//die ( var_dump( $client_version, floatval($client_version),CURRENT_VERSION,$version_compare) );
		return $version_compare;
	}
    public function update() {

    	$client_version = $_GET['client_version'];
		$final_res  = array(
			status => 'success' ,
			msg => "目前已经有新的版本 ".CURRENT_VERSION." 请下载更新",
			force_update=> FORCE_UPDATE,
			url=>APP_STORE_URL ,
			version=>CURRENT_VERSION
		);
		
		if (empty($client_version)) {
			$final_res['error_msg']  = '请提供客户端版本号';
			$final_res['status']     = 0;
			$final_res['error_code'] = 2;
		}
		
		if ($this->is_need_update($client_version)){
			$final_res['need_update'] = 1;
		}else{
			$final_res['need_update'] = 0;
		}

        return jr("API_RESPONSE", $final_res);
    }
}