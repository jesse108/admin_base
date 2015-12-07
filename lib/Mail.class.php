<? 
/**
 * 使用 STMP 基于PHPMailer 开源程序
 * 
 * 使用前必须先配置 conf/email.conf.php 这个文件
 * 
 * @author zhaojian jesse_108@163.com
 *
 */
class Mail{
	public static $phpMailers;
	
	
	public static function GetMailer($address = ''){
		$emailConfig = Config::Get('email');	
		$currentConfig = $emailConfig[$address] ? $emailConfig[$address] : $emailConfig['default'];
		$address = $currentConfig['address'];
		
		if(!$currentConfig){
			return false;
		}
		if(!self::$phpMailers[$address]){
			$mailer = new PHPMailer();
			$mailer->isSMTP();
			$mailer->Host = $currentConfig['stmp_host'];
			$mailer->Username = $currentConfig['user'];
			$mailer->Password = $currentConfig['pwd'];
			$mailer->SMTPAuth = true;
			$mailer->Port = $currentConfig['port'];
			$mailer->CharSet = "utf-8";
			$mailer->setFrom($currentConfig['address']);
			$mailer->isHTML();
		} else {
			$mailer = self::$phpMailers[$address];
		}
		
		$mailer->clearAllRecipients();
		self::$phpMailers[$address] = $mailer;
		return $mailer;
	}
	
	
	public static function Send($tos,$subject,$body,$ccs = array(),$from = ''){
		$mailer = self::GetMailer($from);
		if(!$mailer){
			System::AddError("发送失败：邮件配置错误",System::MESSAGE_SYS);
			return false;
		}
		
		if(!$tos){
			return false;
		}
		
		$tos = is_array($tos) ? $tos : array($tos);
		$ccs = is_array($ccs) ? $ccs : array($ccs);
		
		foreach ($tos as $toAddress){
			$mailer->addAddress($toAddress);
		}
		
		foreach ($ccs as $ccAddress){
			$mailer->addCC($ccAddress);
		}
		
		$mailer->msgHTML($body);
		$mailer->Subject = $subject;
		
		$result = $mailer->send();
		if(!$result){
			System::AddError("邮件发送失败:" . $mailer->ErrorInfo,System::MESSAGE_SYS);
		}
		return $result;
	}
	
}

