<?php
/**
 * 支付宝支付类
 * 
 * 签名md5, 编码utf8
 * 
 * @author Jesse
 *
 */
class Pay_Alipay{
	
	///接口地址
	public $alipayGateway = "https://mapi.alipay.com/gateway.do?";
	
	//返回验证地址
	public $httpsVerifyURL = 'https://mapi.alipay.com/gateway.do?service=notify_verify';
	public $httpVerifyURL  = 'http://notify.alipay.com/trade/notify_query.do';
	
	
	///合作者身份ID
	public $partner;
	
	//安全校验码
	public $key;
	
	//////////////传递参数
	public $service = 'create_direct_pay_by_user';
	
	//支付类型
	public $payment_type = '1'; // 收款类型 1:商品   4:捐赠    47:电子券
	
	public $sign_type = 'MD5';// 固定
	public $_input_charset = 'utf8';//编码
	public $param;
	
	public function __construct($alipayConfig = null){
		$alipayConfig = $alipayConfig ? $alipayConfig : Config::Get('pay','alipay');
		
		$this->partner = $alipayConfig['partner'];
		$this->key = $alipayConfig['key'];
	}
	
	
	/**
	 * $paramter = array(
	 * 		'notify_url'   //通知地址 
	 * 		'return_url'  //返回地址
	 * 		'out_trade_no' //对外订单号
	 * 		'subject'   //商品标题
	 * 		'total_fee'  //总价 
	 * 		'body'       //商品描述 
	 * 		'show_url'   //展示链接
	 * 		''
	 * );
	 * 
	 * 设置业务参数
	 * @param unknown $parameter
	 */
	public function setParamter($param){
		$this->param = $param;
	}
	

	
	public function getRequestParam(){
		$param = $this->param;
		$param['service'] = $this->service;
		$param['partner'] = $this->partner;
		$param['payment_type'] = $this->payment_type;
		$param['_input_charset'] = $this->_input_charset;
		$sign = self::CreateMySign($param, $this->key,$this->sign_type);
		
		$param['sign'] = $sign;
		$param['sign_type'] = $this->sign_type;
		return $param;
	}
	
	public function createPayURL(){
		$url = $this->alipayGateway;
		$requestData = $this->getRequestParam();
		$queryStr = http_build_query($requestData);
		$url = $url . $queryStr;
		return $url;
	}
	
	public function verify($method = 'get'){
		$param = $method == 'get' ? $_GET : $_POST;
		if(empty($param)){
			return false;
		}
		
		$mySign = self::CreateMySign($param, $this->key,$this->sign_type);
		if($param['sign'] != $mySign){
			return false;
		}
		
		$notifyID = $param['notify_id'];
		
		if($notifyID){
			return $this->verfyNotify($notifyID);
		}
		
		return true;
	}
	

	public function verfyNotify($notifyID){
		return true;
		
		$request = $this-> getNotifyResponse($notifyID);
		if (preg_match("/true$/i",$request)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 获取验证信息
	 * 
	 * @param unknown $notifyID
	 */
	public function getNotifyResponse($notifyID){
		$url = $this->httpVerifyURL;
		$partner = trim($this->partner);
		$param = array(
			'partner' => $partner,
			'notify_id' => $notifyID,
		);
		
		Util_HttpRequest::Request($url, 'get', $param);
	}
	
	public static function  CreateMySign($param,$key,$signType = 'MD5'){
		$param = self::FilterParam($param);
		$param = self::SortParam($param);
		$signStr = self::CreateSignStr($param);
		$signStr = $signStr . $key;
		
		$sign = '';
		switch ($signType){
			case 'MD5':
				$sign = md5($signStr);
				break;
			default:
				$sign = '';
				break;
		}
		
		return $sign;
	}
	
	public static function CreateSignStr($param){
		$signStr = '';
		foreach ($param as $key => $value){
			$signStr .= "{$key}={$value}&";
		}
		$signStr = trim($signStr,'&');
		return $signStr;
	}
	
	
	public static function FilterParam($param){
		$filterParam = array();
		foreach ($param as $key => $value){
			$value = trim($value);
			if($key == 'sign' || $key == 'sign_type' || !$value){
				continue;
			}
			$filterParam[$key] = $value;
		}
		return $filterParam;
	}
	
	public static function SortParam($param){
		ksort($param);
		reset($param);
		return $param;
	}
	


}