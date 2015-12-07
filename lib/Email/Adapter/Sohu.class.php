<?php
class Email_Adapter_Sohu implements Email_Adapter_Model{
	public $api_user;
	public $multi_api_user;
	public $api_key;
	public $from;
	public $fromName;
	
	public $useMultiSend = false;
	
	public static $url = "http://sendcloud.sohu.com/webapi/mail.send.json";
	
	public function __construct(){
		$config = Config::Get('email','sohu');
		
		$this->api_user = $config['api_user'];
		$this->multi_api_user = $config['multi_api_user'];
		$this->api_key = $config['api_key'];
		$this->from = $config['from'];
		$this->fromName = $config['fromname'];
	}

	
	public function send($subject, $content, $tos,$from = null) {
		$tos = is_array($tos) ? implode(';', $tos) : $tos;
		
		$param = array(
			'from' => $from = $from ? $from : $this->from,
			'fromname' => $this->fromName,
			'to' => $tos,
			'subject' => $subject,
			'html' => $content,
		);
		
		$result = $this->request($param);
		return $result;
	}

	
	
	public function request($param){
		$param = $param ? $param : array();
		if($this->useMultiSend){
			$param['api_user'] = $this->multi_api_user;
		} else {
			$param['api_user'] = $this->api_user;
		}
		
		$param['api_key'] = $this->api_key;
		
		$result = Util_HttpRequest::Http(self::$url, 'POST',$param);
		if($result){
			$result = json_decode($result,true);
		}
		return $result;
	}
}