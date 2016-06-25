<?php

	/**
	* thinkphp 风格C函数 可以进行动态读取，和动态配置
	* @param $name 要获取的名称
	* @param $value 要配置的名字
	* @param $default 默认值
	*
	*/
	function C($name = null,$value = null, $default = null){
		static $_config = array();
		if(empty($name)){
			return false;
		}
		if(is_string($name)){
			if(!strpos($name,'.')){
				$name = strtolower($name);
				if(is_null($value))
					return isset($_config[$name])?$_config[$name]:$default;
				$_config[$name] = $value;
				return;
			}
			//支持二维数组
			$name 		= 	explode('.',$name);
			$name[0]   	=  	strtolower($name[0]);
			if(is_null($value))
				return isset($_config[$name[0]][$name[1]]) ? $_config[$name[0]][$name[1]] : $default;
			$_config[$name[0]][$name[1]] 	=	$value;
			return;
		}

		//进行数组导入
		if(is_array($name)){
			$_config = array_merge($_config,$name);
			return;
		}

		return null;
	}

	/**
	 * 字符串命名风格转换
	 * type 0 将Java风格转换为C的风格 1 将C风格转换为Java的风格
	 * @param string $name 字符串
	 * @param integer $type 转换类型
	 * @return string
	 */
	function parse_name($name, $type=0) {
	    if ($type) {
	        return ucfirst(preg_replace_callback('/_([a-zA-Z])/', function($match){return strtoupper($match[1]);}, $name));
	    } else {
	        return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $name), "_"));
	    }
	}

	/**
	 * 以空格拼接数组
	 */
	function arrtoString($option){
		$arr = [];

		foreach($option as $k => $v){
			$arr[] = $k.'="'.$v.'"';
		}

		return implode(' ',$arr);
	}

	/**
	 * 模板配置文件获取值
	 */
	function getVal($item,$field){
		if(is_object($item)){
			return isset($item->{$field}) ? $item->{$field} :'';
		}else{
			return isset($item[$field]) ? $item[$field]:'';
		}
	}

	/**
     * 获取客户端IP地址
     * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
	 * @param boolean $adv 是否进行高级模式获取（有可能被伪装） 
	 * @return mixed
	 */
	function get_client_ip($type = 0){

		$type =	$type ? 1 : 0;
		static $ip 	=	null;
		if($ip !== null) return $ip;

		if(isset($_SERVER['REMOTE_ADDR'])){
			$ip = $_SERVER['REMOTE_ADDR'];
			return $ip;
		}

		if(isset($_SERVER['HTTP_X_FORWARDED'])){
			$arr = explode(',',$_SERVER['HTTP_X_FORWARDED']);
			$pos = array_search('unknown',$arr);
			if(false !== $pos) unset($arr[$pos]);
            $ip     =   trim($arr[0]);
		}elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip     =   $_SERVER['HTTP_CLIENT_IP'];
        }elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip     =   $_SERVER['REMOTE_ADDR'];
        }

        // IP地址合法验证
	    $long = sprintf("%u",ip2long($ip));die;
	    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
	    return $ip[$type];
	}

	/**
	 * 生成随机字符串
	 */
	function randomString($length = 8){

		$string = 'abcdefghigklmnopqrstuvwxyzABCDEFGHIGKLMNOPQRSTUVWXYZ0123456789';
		$string = str_shuffle($string);
		return substr($string,0,$length);
	}

	/**
	 * json|jsonp 数据返回函数
	 */
	function json_response($json,$type='json',$callback='jsoncallback'){
		if($type=='json'){
			exit(json_encode($json,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
		}elseif($type=='jsonp'){
			$callback_msg = isset($_GET[$callback])?$_GET[$callback]:'jsoncallback';
			exit($callback_msg.'('.json_encode($json,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE).')');
		}
	}

	/**
	* thinkphp风格 URL组装
	* @param string $url 要解析的url
	* @param mixed(string/array) 传入的参数，支持字符串和数组
	* @param 模式使用的模块
	* @return string 解析后的url
	*/
	function U($url='',$vars='',$module = 'main'){
		//解析url
		$info 	=	parse_url($url);
		$uri	=	!empty($info['path'])?$info['path']:'';

		//解析参数
		if(is_string($vars)){
			parse_str($vars,$vars);
		}elseif(!is_array($vars)){
			$vars = [];
		}

		//解析地址里的参数合并到 vars里
		if(isset($info['query'])){
			 parse_str($info['query'],$params);
			$vars = array_merge($vars,$params);
		}

		$des = !empty($vars) ? '?' :'';

		$host = C('app.'.$module.'_host');
		if($host){
			$url = $host.'/'.$info['path'].$des.http_build_query($vars);
		}else{
			$url = $info['path'].$des.http_build_query($vars);
		}
		return $url;
	}

	//获取lev样式
	function levToStyle($lev,$num = 10){
		$n = $lev * $num;
		$style = ['style' => 'margin-left:'.$n.'px'];
		return arrtoString($style);
	}

	//获得前一条网址
	function getPreUrl($encode = true){

		$url 	=	$_SERVER['HTTP_REFERER'];
		return $encode ? urlencode($url) : $url;
	}

	//获取当前网址
	function getCurrentUrl($encode = false){

		$url 	=	'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		return $encode ? urlencode($url) : $url;
	}

	//增加软件删除条件
	function addSoftDelParams($params = null){
		//关闭软删除
		if(isset($params['softDelete']) && $params['softDelete'] === false){
			return $params;
		}

		if($params != null){

			if(is_string($params)){
				$params 	=	$params." AND is_delete = 0";
			}else{
				if(isset($params['conditions'])){
					$params["conditions"] = $params["conditions"]." AND is_delete=0";
				}else{
					$params["conditions"] = "is_delete = 0";
				}
			}
		}else{
			$params["conditions"] = "is_delete=0";
		}
		return $params;
	}

	//过滤值为空的变量
	function filterEmptyVal($params){

		$arr = [];
		foreach($params as $k =>$v){
			if(empty($v)){
				unset($k);
			}else{
				$arr[$k] = $v;
			}
		}
		return $arr;
	}
?>