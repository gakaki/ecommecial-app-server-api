<?php
$array_params['timestamp']=date("Y-m-d H:i:s");
$array_params['format']='json';
$array_params['fields']='cid,parent_cid,name,is_parent,status,sort_order';
$array_params['app_key']='12541234';
$array_params['session']='6201229274fa2e031c11dc2b23a15cb9bd13ceg03a394a6664671110';
$array_params['sign_method']='md5';
$array_params['v']='2.0';
$array_params['datetime']=date("Y-m-d H:i:s");
$array_params['method']='taobao.itemcats.get';

$array_data = array();
ksort($array_params);
$str_verfy_string = "";
foreach ($array_params as $str_key=>$str_value) {
    $str_verfy_string .= $str_key.$str_value;
    $array_data[] = $str_key."=".rawurlencode($str_value);
}
$array_data[] = "sign=".strtoupper(md5("15bbea18ee7ea6838235a573c954883b".$str_verfy_string."15bbea18ee7ea6838235a573c954883b"));
//echo implode("&", $array_data);exit;
$obj_ch = curl_init();
curl_setopt($obj_ch, CURLOPT_URL, "http://gw.api.taobao.com/router/rest");
curl_setopt($obj_ch, CURLOPT_POST, 1);
curl_setopt($obj_ch, CURLOPT_POSTFIELDS, implode("&", $array_data));
curl_setopt($obj_ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($obj_ch, CURLOPT_HEADER, 0);
$str_result = curl_exec($obj_ch);
curl_close($obj_ch);
file_put_contents("c:/dump.txt", $str_result);