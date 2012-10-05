<?php
class ApiViewApi extends NViewModule {
    public function api_response($mixed_data) {
        $array_result = array();
        $array_result["status"] = "SUCCESS";
        $array_result["msg"] = "";
        $array_result["data"] = $mixed_data;
        $array_result["ts"] = date("c");
        echo json_encode($array_result);
    }

    public function api_response_count($int_count, $mixed_data) {

        $array_result = array();
        $array_result["status"] = "SUCCESS";
        $array_result["msg"] = "";
        $array_result["data"] = $mixed_data;
        $array_result["count"] = $int_count;
        $array_result["ts"] = date("c");
        
        
        $array_result["page"] = date("c");
        $array_result["page_size"] = date("c");
        
        echo json_encode($array_result);
    }
    public function api_response_page( $mixed_data) {

        $array_result               = array();
        $array_result["status"]     = "SUCCESS";
        $array_result["msg"]        = "";
        
        $data                       = $array_result["data"]     = $mixed_data;
        
        $array_result["count"]      = $data['page_size'];
        $array_result["total"]      = $data['total'];
        $array_result["total_page"] = $data['total_page'];
        $array_result["page"]       = $data['page'];
        $array_result["page_size"]  = $data['page_size'];
        
        $array_result['data']       = $data['items'];
        
        $array_result["ts"]         = date("c");
        
        $array_result["error_code"] = $data['error_code'];
        $array_result["error_info"] = $data['error_info'];
        
   
        if (!$_REQUEST['weblogid']) {
            $array_result["weblogid"]   = session_id();
        }else{
            $array_result["weblogid"]   = $_REQUEST['weblogid'];
        }
        echo json_encode($array_result);
    }


    public function api_response_productlist_wrap($mixed_data) {

        $array_result               = array();
        $array_result["status"]     = "SUCCESS";
        $array_result["msg"]        = "";

        ChromePHP::log( '$mixed_data' , $mixed_data );
        

        if ($mixed_data['msg']) {
            $array_result["msg"] = $mixed_data['msg'];
        }

        $data                       = $mixed_data;

        $product_list               = $data['product_list'];
        ChromePHP::log( '$product_list' , $product_list );
        $data_length                = count($product_list['items']);
        $array_result["count"]      = $data_length;
        $array_result["total"]      = $product_list['total'];
        $array_result["total_page"] = $product_list['total_page'];
        $array_result["page"]       = $product_list['page'];
        $array_result["page_size"]  = $product_list['page_size'];

        $data['product_list']       = $product_list['items'];

        $array_result['data']       = $data;
      
        $array_result["ts"]         = date("c");
        
        $array_result["error_code"] = $data['error_code'];
        $array_result["error_info"] = $data['error_info'];

        if (!$_REQUEST['weblogid']) {
            $array_result["weblogid"]   = session_id();
        }else{
            $array_result["weblogid"]   = $_REQUEST['weblogid'];
        }

        echo json_encode($array_result);
    }


    
    public function api_response_simple($mixed_data)
    {
        if (!$mixed_data) {
            $array_result               = wrap_data_api_like($mixed_data);
        }else{
            $array_result               = $mixed_data;
        }
        
        echo json_encode($array_result);
    }

    public function api_response_wrap($mixed_data) {
        $array_result               = wrap_data_api_like($mixed_data);
        echo json_encode($array_result);
    }

    public function api_error($str_err_msg) {
        $array_result = array();
        $array_result["status"] = "ERROR";
        $array_result["msg"] = $str_err_msg;
        echo json_encode($array_result);
    }

    public function api_error_detail($error_arr) {
        $array_result               = array();
        $array_result["status"]     = "ERROR";
        $array_result["msg"]        = "";
        $array_result["error_code"] = $error_arr['error_code'];
        $array_result["error_info"] = $error_arr['error_info'];
        $array_result["weblogid"]   = session_id();
        echo json_encode($array_result);
    }
}