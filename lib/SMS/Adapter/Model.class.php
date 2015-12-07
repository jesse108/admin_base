<?php
interface SMS_Adapter_Model{
	
	/**
	 * 发送短信
	 * 
	 * @param unknown $tos
	 * @param unknown $message
	 */
	public function sendSMS($message,$tos);
	
}