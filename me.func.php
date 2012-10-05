<?php  

include_once('debuglib/debug.php');
include_once('QueryBuilder.php');

/**
* Access the HTTP Request
*/
class http_request {

    /** additional HTTP headers not prefixed with HTTP_ in $_SERVER superglobal */
    var $add_headers = array('CONTENT_TYPE', 'CONTENT_LENGTH');

    /**
    * Construtor
    * Retrieve HTTP Body
    * @param Array Additional Headers to retrieve
    */
    function http_request($add_headers = false) {
    
        $this->retrieve_headers($add_headers);
        $this->body = @file_get_contents('php://input');
    }
    
    /**
    * Retrieve the HTTP request headers from the $_SERVER superglobal
    * @param Array Additional Headers to retrieve
    */
    function retrieve_headers($add_headers = false) {
        
        if ($add_headers) {
            $this->add_headers = array_merge($this->add_headers, $add_headers);
        }
    
        if (isset($_SERVER['HTTP_METHOD'])) {
            $this->method = $_SERVER['HTTP_METHOD'];
            unset($_SERVER['HTTP_METHOD']);
        } else {
            $this->method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : false;
        }
        $this->protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : false;
        $this->request_method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : false;
        
        $this->headers = array();
        foreach($_SERVER as $i=>$val) {
            if (strpos($i, 'HTTP_') === 0 || in_array($i, $this->add_headers)) {
                $name = str_replace(array('HTTP_', '_'), array('', '-'), $i);
                $this->headers[$name] = $val;
            }
        }
    }
    
    /** 
    * Retrieve HTTP Method
    */
    function method() {
        return $this->method;
    }
    
    /** 
    * Retrieve HTTP Body
    */
    function body() {
        return $this->body;
    }
    
    /** 
    * Retrieve an HTTP Header
    * @param string Case-Insensitive HTTP Header Name (eg: "User-Agent")
    */
    function header($name) {
        $name = strtoupper($name);
        return isset($this->headers[$name]) ? $this->headers[$name] : false;
    }
    
    /**
    * Retrieve all HTTP Headers 
    * @return array HTTP Headers
    */
    function headers() {
        return $this->headers;
    }
    
    /**
    * Return Raw HTTP Request (note: This is incomplete)
    * @param bool ReBuild the Raw HTTP Request
    */
    function raw($refresh = false) {
    
        if (isset($this->raw) && !$refresh) {
            return $this->raw; // return cached
        }
    
        $headers = $this->headers();
        $this->raw = "{$this->method}\r\n";
        
        foreach($headers as $i=>$header) {
                $this->raw .= "$i: $header\r\n";
        }
        
        $this->raw .= "\r\n{$http_request->body}";
        
        return $this->raw;
    }
}



/**
* PageHelper
*/
class ModelPageHelper
{
    public $field_str_sql;
    public $table_name;
    public $field_for_assoc;
    public $page;
    public $page_size = 20; //默认每页显示20条数据/
    public $sort_arr;
    public $where;

    public $res;       
    public $total;     
    public $total_page;
    

    function __construct($table_path,$field_for_assoc,$page,$page_size,$where,$sort_arr,$where_params)
    {
        $this->table_path      = $table_path;
        $this->field_for_assoc = $field_for_assoc;

        $this->page            = !$page?1:intval($page);;
        $this->page_size       = !$page_size?$this->page_size:intval($page_size);
        $this->sort_arr        =  $sort_arr;

        $this->field_str_sql   = $this->gen_field_assoc_sql();
        $this->where           = $where;
        $this->where_params    = $where_params;
    }
    
    public function gen_field_assoc_sql()
    {
        $field_for_assoc = $this->field_for_assoc;
        $gen_sql         = "";

        if (!is_array($field_for_assoc)) {
          // throw new Exception("  $field_for_assoc is not array type error ", 1);
          // die;
          $gen_sql         = $field_for_assoc; //若没有提供对应关系 array 那么直接使用sql 
        }else{
            $result = array();
            foreach ($field_for_assoc as $k => $v) {
              if (empty($v)) {
                  //continue;
                  throw new Exception("对应的value为空了 请补全THE SQL FIELD ASSOC VALUE CAN NOT BE NULL gen_field_name method", 1);
                  die;
              }
              $tmp      = " $v as $k ";
              $result[] = $tmp;
            }
            $gen_sql = implode(',',$result);
        }
        
        return $gen_sql;        
    }

    public function gen_sort_str_sql()
    {
        $sort_arr = $this->sort_arr;
        $result   = array();
        foreach ($sort_arr as $sort_kv) {
            if (empty($sort_kv)) {
                continue;
                throw new Exception("对应的value为空了 请补全THE SQL FIELD ASSOC VALUE CAN NOT BE NULL gen_field_name method", 1);
                die;
            }

            foreach ($sort_kv as $key => $value) {
             $tmp         = " $key $value ";
            }

            $result[] = $tmp;
        }
        $gen_sql = implode(',',$result);
        if ($gen_sql) {
          $gen_sql = ' order by '.$gen_sql;
        }
        return $gen_sql;        
    }
    /*
            SELECT SQL_CALC_FOUND_ROWS * FROM `sdb_brand` limit 0, 10;
            SELECT FOUND_ROWS();
            
            获取limit的第一个参数的值 offset，假如第一页则为(1-1)*10 =0,第二页为(2-1)*10=10。
            (传入的页数-1) * 每页的数据 得到limit第一个参数的值
    */
    public function page()
    {
        $field_str_sql   = $this->field_str_sql;
        $sort_str_sql    = $this->gen_sort_str_sql();
        
        $table_path      = $this->table_path;
        
        $page            = $this->page;
        $page_size       = $this->page_size;
        
        $where           = $this->where;
        

        if ( !empty($where) ) {
           $where = ' where '.$where;
        }
        
        $db_prefix       = DB_PREFIX;
        $offset          = ($page-1)*$page_size;
        // $sql             = "SELECT SQL_CALC_FOUND_ROWS $field_str_sql from {$db_prefix}{$table_name} $where limit $offset,$page_size";

        $sql             = "SELECT SQL_CALC_FOUND_ROWS {$field_str_sql} from {$table_path} $where $sort_str_sql limit $offset,$page_size";
        $sql_total_row   = "SELECT FOUND_ROWS()";

        $sql             = rep_db_prefix($sql);
        $sql             = rep_sdb($sql);
        
        $res             = db_fetch_all( $sql,$this->where_params );
        
        $total           = DB()->getOne($sql_total_row);


        ChromePHP::log( '$sql' , $sql );
        ChromePHP::log( '$where_params' , $this->where_params );
        ChromePHP::log( '$sort_arr' , $this->sort_arr );
        ChromePHP::log( '$res' , $res);

        //获得总页数 total_page
        $total_page      =  ceil($total/$page_size);
        
        $this->res        = $res;
        $this->total      = $total;
        $this->total_page = $total_page;
        
        return $this;
    }

    public function wrap()
    {
        $data        =  array(
        'items'      => $this->res,  
        'page'       => $this->page,
        'page_size'  => $this->page_size,
        'total'      => $this->total,
        'total_page' => $this->total_page
        );
        $this->res   = $data;
        return $this;
    }
    public function res()
    {   
        return $this->res;
    }
    public function paged_res()
    {
        return $this->wrap()->res();
    }
}
function jr($str_status,$data) {
    $array_params = array();
    for ($int_i = 2; $int_i < func_num_args(); $int_i++) {
        $array_params[] = func_get_arg($int_i);
    }
    
    //$count         = count($data['items']);
    //$data['count'] = $count;
    $data          = array( 'items' => $data);
    
    return array("status"=>$str_status, 'data' => $data,"other"=>$array_params);
}

function wrap_data($res,$page=1,$page_size=0,$total=0,$total_page=1)
{
    if (!$total) {
        $total = count($res);
    }
    if (!$page_size) {
        $page_size = count($res);
    }
    $data   =  array(
        'items'      => $res,  
        'page'       => $page,
        'page_size'  => $page_size,
        'total'      => $total,
        'total_page' => $total_page
    );

    return $data;
}

	

class PYInitials
{
    private $_pinyins = array(
        176161 => 'A',
        176197 => 'B',
        178193 => 'C',
        180238 => 'D',
        182234 => 'E',
        183162 => 'F',
        184193 => 'G',
        185254 => 'H',
        187247 => 'J',
        191166 => 'K',
        192172 => 'L',
        194232 => 'M',
        196195 => 'N',
        197182 => 'O',
        197190 => 'P',
        198218 => 'Q',
        200187 => 'R',
        200246 => 'S',
        203250 => 'T',
        205218 => 'W',
        206244 => 'X',
        209185 => 'Y',
        212209 => 'Z',
    );
    private $_charset = null;
    /**
     * 构造函数, 指定需要的编码 default: utf-8
     * 支持utf-8, gb2312
     *
     * @param unknown_type $charset
     */
    public function __construct( $charset = 'utf-8' )
    {
        $this->_charset    = $charset;
    }
    /**
     * 中文字符串 substr
     *
     * @param string $str
     * @param int    $start
     * @param int    $len
     * @return string
     */
    private function _msubstr ($str, $start, $len)
    {
        $start  = $start * 2;
        $len    = $len * 2;
        $strlen = strlen($str);
        $result = '';
        for ( $i = 0; $i < $strlen; $i++ ) {
            if ( $i >= $start && $i < ($start + $len) ) {
                if ( ord(substr($str, $i, 1)) > 129 ) $result .= substr($str, $i, 2);
                else $result .= substr($str, $i, 1);
            }
            if ( ord(substr($str, $i, 1)) > 129 ) $i++;
        }
        return $result;
    }
    /**
     * 字符串切分为数组 (汉字或者一个字符为单位)
     *
     * @param string $str
     * @return array
     */
    private function _cutWord( $str )
    {
        $words = array();
         while ( $str != "" )
         {
            if ( $this->_isAscii($str) ) {//非中文
                $words[] = $str[0];
                $str = substr( $str, strlen($str[0]) );
            }else{
                 $word = $this->_msubstr( $str, $i, 1 );
                $words[] = $word;
                 $str = substr( $str,  strlen($word) );
            }
         }
         return $words;
    }
    /**
     * 判断字符是否是ascii字符
     *
     * @param string $char
     * @return bool
     */
    private function _isAscii( $char )
    {
        return ( ord( substr($char,0,1) ) < 160 );
    }
    /**
     * 判断字符串前3个字符是否是ascii字符
     *
     * @param string $str
     * @return bool
     */
    private function _isAsciis( $str )
    {
        $len = strlen($str) >= 3 ? 3: 2;
        $chars = array();
        for( $i = 1; $i < $len -1; $i++ ){
            $chars[] = $this->_isAscii( $str[$i] ) ? 'yes':'no';
        }
        $result = array_count_values( $chars );
        if ( empty($result['no']) ){
            return true;
        }
        return false;
    }
    /**
     * 获取中文字串的拼音首字符
     *
     * @param string $str
     * @return string
     */
    public function getInitials( $str )
    {
        if ( empty($str) ) return '';
        if ( $this->_isAscii($str[0]) && $this->_isAsciis( $str )){
            return $str;
        }
        $result = array();
        if ( $this->_charset == 'utf-8' ){
            $str = iconv( 'utf-8', 'gb2312', $str );
        }
        $words = $this->_cutWord( $str );
        foreach ( $words as $word )
        {
            if ( $this->_isAscii($word) ) {//非中文
                $result[] = $word;
                continue;
            }
            $code = ord( substr($word,0,1) ) * 1000 + ord( substr($word,1,1) );
            //获取拼音首字母A--Z
            if ( ($i = $this->_search($code)) != -1 ){
                $result[] = $this->_pinyins[$i];
            }
        }
        return strtoupper(implode('',$result));
    }
    /*
        $test = new PYInitials();
        echo $test->get_first_letter('王小明');//WXM //w
    */
    public function get_first_letter($str)
    {    
        $result = $this->getInitials($str); 
        $first_letter = strtoupper($result[0]);
        return $first_letter;
    }


    private function _getChar( $ascii )
    {
        if ( $ascii >= 48 && $ascii <= 57){
            return chr($ascii);  //数字
        }elseif ( $ascii>=65 && $ascii<=90 ){
            return chr($ascii);   // A--Z
        }elseif ($ascii>=97 && $ascii<=122){
            return chr($ascii-32); // a--z
        }else{
            return '~'; //其他
        }
    }

    /**
     * 查找需要的汉字内码(gb2312) 对应的拼音字符( 二分法 )
     *
     * @param int $code
     * @return int
     */
    private function _search( $code )
    {
        $data = array_keys($this->_pinyins);
        $lower = 0;
        $upper = sizeof($data);
        if ( $code < $data[0] ) return -1;
        for (;;) {
            if ( $lower > $upper ){
                return $data[$lower-1];
            }
            $tmp = (int) round(($lower + $upper) / 2);
            if ( !isset($data[$tmp]) ) return $data[$middle];
            else $middle = $tmp;
            if ( $data[$middle] < $code ){
                $lower = (int)$middle + 1;
            }else if ( $data[$middle] == $code ) {
                return $data[$middle];
            }else{
                $upper = (int)$middle - 1;
            }
        }// end for
    }
}
function gen_fields($fields=array('*'))
{
  return implode(',', $fields);
}
function gen_in_sql($in_arr)
{   
    foreach ($in_arr as $k => &$v) {
        $v = "'{$v}'";
    }

    $in_sql =  implode(',',$in_arr);
    $tmp    = "  in ( $in_sql ) ";

    return $tmp;
}
function arr2sqlin($in_arr,$column_name)
{   
    foreach ($in_arr as $k => &$v) {
        $v = "'{$v}'";
    }
  
    $in_sql =  implode(',',$in_arr);
    $tmp    = " $column_name in ( $in_sql ) ";
    return $tmp;
}

function rep_db_prefix($str_sql)
{
    $str_sql     = str_replace("#pr#", DB_PREFIX, $str_sql);
    return $str_sql;
}
function rep_sdb($str_sql)
{
    $str_sql     = str_replace("sdb_", DB_PREFIX, $str_sql);
    return $str_sql;
}
/*  var_dump(get_http_request());die; */
function get_http_request()
{
    $http_request         = new http_request();
    return $http_request->headers();
    // $res['header_params'] = $http_request->headers();         
}

function db_fetch_pre(&$sql, &$array_params = array())
{
  $sql = rep_db_prefix($sql);
  $sql = rep_sdb($sql);
  if (!$array_params) {
    $array_params = array();
  }
}
function db_fetch_all($sql, $array_params = array())
{
  db_fetch_pre($sql,$array_params);
  $res = DB()->fetchAll($sql,$array_params);
  return $res;
}
function db_fetch_one($sql, $array_params = array())
{
  db_fetch_pre($sql,$array_params);
  $res = DB()->fetchOne($sql,$array_params);
  return $res;
}
function db_get_one($sql, $array_params = array())
{
  db_fetch_pre($sql,$array_params);
  $res = DB()->getOne($sql,$array_params);
  return $res;
}
function db_query($sql, $array_params = array())
{
  db_fetch_pre($sql,$array_params);
  $res = DB()->query($sql,$array_params);
  return $res;
}
function wrap_data_api_like($mixed_data)
{
    $array_result               = array();
    $array_result["status"]     = "SUCCESS";
    $array_result["msg"]        = "";

    if ($mixed_data['msg']) {
        $array_result["msg"]    = $mixed_data['msg'];
    }

    $data                       = $mixed_data;
    $data_length                = count($data);
    $array_result["count"]      = $data_length;
    $array_result["total"]      = $data_length;
    $array_result["total_page"] = 1;
    $array_result["page"]       = 1;
    $array_result["page_size"]  = $data_length;
    
    $array_result['data']       = $data;
  
    $array_result["ts"]         = date("c");
    
    $array_result["error_code"] = $data['error_code'];
    $array_result["error_info"] = $data['error_info'];

    if (!$_REQUEST['weblogid']) {
        $array_result["weblogid"]   = session_id();
    }else{
        $array_result["weblogid"]   = $_REQUEST['weblogid'];
    }
    return $array_result;
}

// todo 这里应该实现获取店名的函数
function get_shop_name()
{
  return "店名";
}
function format_ts($ts)
{
  return date("Y-m-d H:i:s",$ts);
}

/**
    Validate an email address.
    Provide email address (raw input)
    Returns true if the email address has the email 
    address format and the domain exists.
*/
function validEmail($email)
{
   $isValid = true;
   $atIndex = strrpos($email, "@");
   if (is_bool($atIndex) && !$atIndex)
   {
      $isValid = false;
   }
   else
   {
      $domain = substr($email, $atIndex+1);
      $local = substr($email, 0, $atIndex);
      $localLen = strlen($local);
      $domainLen = strlen($domain);
      if ($localLen < 1 || $localLen > 64)
      {
         // local part length exceeded
         $isValid = false;
      }
      else if ($domainLen < 1 || $domainLen > 255)
      {
         // domain part length exceeded
         $isValid = false;
      }
      else if ($local[0] == '.' || $local[$localLen-1] == '.')
      {
         // local part starts or ends with '.'
         $isValid = false;
      }
      else if (preg_match('/\\.\\./', $local))
      {
         // local part has two consecutive dots
         $isValid = false;
      }
      else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
      {
         // character not valid in domain part
         $isValid = false;
      }
      else if (preg_match('/\\.\\./', $domain))
      {
         // domain part has two consecutive dots
         $isValid = false;
      }
      else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',str_replace("\\\\","",$local)))
      {
         // character not valid in local part unless 
         // local part is quoted
         if (!preg_match('/^"(\\\\"|[^"])+"$/',
             str_replace("\\\\","",$local)))
         {
            $isValid = false;
         }
      }
      if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A")))
      {
         // domain not found in DNS
         $isValid = false;
      }
   }

   return $isValid;
}