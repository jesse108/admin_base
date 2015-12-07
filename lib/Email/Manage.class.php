<?php
class Email_Manage{
	const TYPE_SOHU = 1; //搜狐send cloud 发送邮件
	const TYPE_SMTP = 2; //smtp 形式发送邮件 使用phpMailer
    const TYPE_SENDMAIL = 3;
	
	public static $adapters;
	public static $defaultType = 1;
	
	
	
	
	public static function Send($subject,$content,$tos,$from = null){
		$adapter = self::GetAdapter(self::$defaultType);
		
		return $adapter->send($subject, $content, $tos,$from);
	}
	
	
	public static function SetType($type){
		self::$defaultType = $type;
	}
	
	public static function GetAdapter($type = null){
		$type = $type ? $type : self::TYPE_SOHU;
		
		if(self::$adapters[$type]){
			return self::$adapters[$type];
		}
		
		switch ($type){
			case self::TYPE_SMTP:
				$adapter = new Email_Adapter_SMTP();
				break;
            case self::TYPE_SENDMAIL:
                $adapter = new Email_Adapter_SendMail();
                break;
			case self::TYPE_SOHU:
			default:
				$adapter = new Email_Adapter_Sohu();
				break;
		}
		
		self::$adapters[$type] = $adapter;
		return $adapter;
	}
}