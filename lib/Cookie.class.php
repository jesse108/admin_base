<?php
class Cookie{
	public static function Set($key,$value = null,$expire = null){
		if($expire){
			$expire = $expire + time();
		}

		return setcookie($key,$value,$expire,'/');
	}
	
	public static function Get($key){
		return $_COOKIE[$key];
	}
	
	public static function Del($key){
		return setcookie($key,null,null,'/');
	}
}