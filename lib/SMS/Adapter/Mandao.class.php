<?php
class SMS_Adapter_Mandao implements SMS_Adapter_Model{
	public $mandao;
	
	public function __construct($config = null){
		$this->mandao = new Platform_Mandao();
	}

	public function sendSMS($message,$tos) {
		return $this->mandao->sendSMS($message, $tos);
		
	}

	

}