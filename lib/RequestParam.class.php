<?php
class RequestParam{
	
	public static function Get($key = null,$filter = true, $default = null){
		return self::GetParam($key,'get',$filter, $default);
	}
	
	public static function Post($key = null,$filter = true, $default = null){
		return self::GetParam($key,'post',$filter, $default);
	}
	
	public static function Request($key = null,$filter = true, $default = null){
		return self::GetParam($key,'request',$filter, $default);
	}
    public static function IsPost(){
        return $_SERVER['REQUEST_METHOD'] === 'POST' ? true : false;
    }
	
	
	public static function GetParam($key = null,$method='request',$filter = true, $default = null){
		switch ($method){
			case 'request':
				$param = $_REQUEST;
				break;
			case 'post':
				$param = $_POST;
				break;
			case 'get':
			default:
				$param = $_GET;
				break;
		}
		
		if(!$param){
			return $default;
		}
		
		if($key){
			if(!isset($param[$key])){
				return $default;
			}
			$value = $param[$key];
		} else {
			$value = $param;
		}
		
		
		if($filter){
			$value = self::Filter($value);
		}

		return $value ? $value : $default;
	}
	
	
	
	public static function Filter($value){
		if(is_array($value)){
			foreach ($value as $index => $one){
				$value[$index] = self::Filter($one);
			}
		} else {
			$value = $value;//过滤
		}
		
		return $value;
	}
}