<?php
class Util_DB{
	static $idArray = array();
	
	public static function GetID($length,$table){
		$id = Util_String::GenRandomStr($length,Util_String::CHAR_NUM);
		
		while (self::$idArray[$id]){
			if($length <= 5){ //保证程序健壮性 防止死循环
				return false;
			}
			
			$id = Util_String::GenRandomStr($length,Util_String::CHAR_NUM);
		}
		
		if(DB::Exists($table, array('id' => $id))){
			return false;
		}
		
		self::$idArray[$id] = 1;
		return $id;
	}
}