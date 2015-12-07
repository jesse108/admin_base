<?php
class System{
	const MESSAGE_ALL 	= 0;
	const MESSAGE_INDEX = 1;
	const MESSAGE_ADMIN = 2;	
	const MESSAGE_SYS 	= 3; //系统消息 一般不给前端显示
	const MESSAGE_HR_PASSWORD = 4;
	const MESSAGE_COMPANY_CONTACTS = 5;
	const MESSAGE_COMPANY_INFO = 6;
	const MESSAGE_PAYBACK_INFO = 7;

    const MESSAGE_MULTI_IMPORT = 8;
    const MESSAGE_WEIXIN_SYNC = 9;
	
	public static $messageKey = "System_message";
	
	public static function SetError($message,$type = self::MESSAGE_INDEX){
		$key = self::$messageKey . "_error_{$type}";
		return Session::Set($key, $message);
	}
	
	public static function AddError($message,$type = self::MESSAGE_INDEX){
		$error = strval(self::GetError($type,true));
		$error = sprintf('%s,%s', $error, $message);
		return self::SetError($error,$type);
	}
	
	public static function AddSysError($message){
		return self::AddError($message, self::MESSAGE_SYS);
	}

    public static function AddSysNotice($message){
        return self::AddNotice($message, self::MESSAGE_SYS);
    }
	
	public static function GetSysError($once = true){
		return self::GetError(self::MESSAGE_SYS, $once);
	}
	
	public static function GetError($types = self::MESSAGE_INDEX,$once = true){
		if($types == self::MESSAGE_ALL){ //获取所有
			$types = array(self::MESSAGE_INDEX,self::MESSAGE_ADMIN,self::MESSAGE_SYS);
		} else if(!is_array($types)){
			$types = array($types);
		} 
		
		$value = '';
		foreach ($types as $type){
			$key = self::$messageKey . "_error_{$type}";
			$currentValue = Session::Get($key,$once);
			if($currentValue){
				$value = "{$value},{$currentValue}";
			}
		}
		$value = trim($value,', ');
		return $value;
	}
	
	
	public static function SetNotice($message,$type = self::MESSAGE_INDEX){
		$key = self::$messageKey . "_notice_{$type}";
		return Session::Set($key, $message);
	}
	
	public static function AddNotice($message,$type = self::MESSAGE_INDEX){
		$notice = strval(self::GetNotice($type,false));
		$notice = "{$notice},{$message}";
		return self::SetNotice($notice,$type);
	}
	
	public static function GetNotice($type = self::MESSAGE_INDEX,$once = true){
		$key = self::$messageKey . "_notice_{$type}";
		$value = Session::Get($key,$once);
		$value = trim($value,', ');
		return $value;
	}
}