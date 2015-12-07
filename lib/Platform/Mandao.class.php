<?php
/**
 * 漫道科技 发送类
 * @author Jesse
 *
 */
class Platform_Mandao{
	public $account;
	public $passwd;
	public $perfix;
	
	public static $banlanceURL = 'http://sdk2.zucp.net/z_balance.aspx';
	//public static $banlanceURL = 'http://sdk2.entinfo.cn/webservice.asmx/GetBalance';
	
	//public static $smsSendURL = 'http://sdk2.zucp.net:8060/webservice.asmx/mt';
	//public static $smsSendURL = 'http://sdk2.entinfo.cn:8060/webservice.asmx/mt';
	//public static $smsSendURL = 'http://sdk.entinfo.cn:8061/webservice.asmx/mdsmssend';
	public static $smsSendURL = 'http://sdk2.entinfo.cn:8061/mdsmssend.ashx';
	//public static $smsSendURL = 'http://sdk2.entinfo.cn:8061/mdgxsend.ashx';
	
	
	public function __construct($config = null){
		$config = $config ? $config : Config::Get('mandao');

		$this->account = $config['account'];
		$this->passwd = $config['passwd'];
		$this->perfix = $config['perfix'];
	}
	
	
	/**
	 * 发短信接口 
	 */
	public function sendSMS($content,$tos){
		if(is_array($tos)){
			$tos = implode(',', $tos);
		}
		
		if($this->perfix){
			$content = $content . $this->perfix;
		}
		//$content = iconv('UTF-8',"GBK",$content);
		$param = array(
			'sn' => urlencode($this->account),
			'pwd' => urlencode($this->getIncodePwd()),
			'mobile' => $tos,
			'content' => urlencode($content),
			'ext' => '',
			'stime' => '',
			'rrid' => '',
			'msgfmt' => '',
		);
		
		
		$result = self::Request(self::$smsSendURL, $param,'POST');
		
		if($result > 0){
			return true;
		} else {
			return false;
		}
		
		return $result;
	}
	
	
	/**
	 * 查看余额
	 */
	public function getBalance(){
		$param = array(
			'sn' => $this->account,
			'pwd' => $this->passwd,
		);
		$result = self::Request(self::$banlanceURL,$param);
		return $result;
	}
	
	
	public function getIncodePwd(){
		$pwd = strtoupper(md5($this->account . $this->passwd));
		return $pwd;
	}
	
	
	/**
	 * 请求
	 */
	public static function Request($url,$param,$method = 'GET'){
		$result = Util_HttpRequest::Request($url, $method, $param);
		$result = str_replace('<?xml version="1.0" encoding="utf-8"?>', '', $result);
		$result = str_replace('<string xmlns="http://entinfo.cn/">', '', $result);
		$result = str_replace('</string>', '', $result);
		$result = trim($result);
		return $result;
	}
}