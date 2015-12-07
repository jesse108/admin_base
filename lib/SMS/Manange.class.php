<?php
class SMS_Manange{
	const TYPE_MANDAO = 1;
	const TYPE_WEITONG = 2;
	public static $adapters = array();
	
	public static function SendSMS($message,$tos,$type = self::TYPE_MANDAO){
		$adapter = self::GetAdapter($type);
		$result = false;
		if($adapter){
			$result  = $adapter->sendSMS($message, $tos);
		}
		return $result;
	}
	
	
	public static function GetAdapter($type = self::TYPE_MANDAO){
		if(self::$adapters[$type]){
			return self::$adapters[$type];
		}
		
		$adapter = null;
		switch ($type){
			case self::TYPE_MANDAO:
				$adapter = new SMS_Adapter_Mandao();
				break;
			case self::TYPE_WEITONG:
			    $adapter = new SMS_Adapter_Weitong();
			    break;
		}
		
		if($adapter){
			self::$adapters[$type] = $adapter;
			return $adapter;
		}
		return false;
	}
}