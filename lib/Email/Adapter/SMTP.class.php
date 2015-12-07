<?php
class Email_Adapter_SMTP implements Email_Adapter_Model{
	public $phpMailer;
	
	
	/**
	 *  $emailConfig['smtp'] = array(
	 *     'stmp_host' => '',
	 *     'user' => '',
	 *     'pwd' => '',
	 *     'port' => '',
	 *     'address' => '',
	 *  );
	 */
	public function __construct(){
		$config = Config::Get('email','smtp');
		$mailer = new PHPMailer();
		$mailer->isSMTP();
		$mailer->Host = $config['stmp_host'];
		$mailer->Username = $config['user'];
		$mailer->Password = $config['pwd'];
		$mailer->SMTPAuth = true;
		$mailer->Port = $config['port'];
		$mailer->CharSet = "utf-8";
		$mailer->setFrom($config['address']);
		$mailer->isHTML();
		$mailer->clearAllRecipients();
		
		$this->phpMailer = $mailer;
	}
	
	public function send($subject, $content, $tos, $from = null) {
		$tos = is_array($tos) ? $tos : array($tos);
		
		$mailer = $this->phpMailer;
		
		foreach ($tos as $toAddress){
			$mailer->addAddress($toAddress);
		}
		
		$mailer->msgHTML($content);
		$mailer->Subject = $subject;
		
		$result = $mailer->send();
		return $result;
	}

}