<?php
class Session{
	
	public static function Get($key,$once = false){
		$value = null;
		if(isset($_SESSION[$key])){
			$value = $_SESSION[$key];
			if($once){
				unset($_SESSION[$key]);
			}
		}
		return $value;
	}
	
	
	public static function Set($key,$value){
		$_SESSION[$key] = $value;
	}
	
	public static function Del($key){
		if(isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
	}
	
	public static function getAll(){
		$session = $_SESSION;
		return $session;
	}
	
	public static function Exists($key){
		return isset($_SESSION[$key]) && $_SESSION[$key] ? true : false;
	}
	
}