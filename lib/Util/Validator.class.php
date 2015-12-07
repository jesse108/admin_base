<?php
class Util_Validator{
	const TYPE_MOBILE = 1;
	const TYPE_EMAIL = 2;
	
	public static $patternArray = array(
		self::TYPE_MOBILE => "/^1(3|5|7|8)[0-9]{9}$/",
		self::TYPE_EMAIL => "/^.+@.+\..+$/",
	);
	
	static public function CheckRequired($value){
		$value = trim($value);
		return $value ? true : false;
	}
	
	static function checkIdCard($value){
		return $value ? self::idCardChecksum18($value) : false;
	}
	
	public static function ValidateEmail($data){
		return self::Validate($data, self::TYPE_EMAIL);
	}
	
	public static function ValidateMobile($data){
		return self::Validate($data, self::TYPE_MOBILE);
	}
	
	public static function Validate($data,$type){
		$validateResult = false;
		
		switch($type){
			default:
				$validateResult = self::CommonValidate($data,$type);
				break;
		}
		return $validateResult;
	}
	
	/**
	 * 通用验证
	 * @param unknown $data
	 * @param unknown $type
	 * @return boolean
	 */
	public static function CommonValidate($data,$type){
		$data = trim($data);
		$pattern = self::$patternArray[$type];
		
		if(!$pattern){
			return false;
		}
		
		if(preg_match($pattern, $data)){
			return true;
		} else {
			return false;
		}
	}
	
	// 计算身份证校验码，根据国家标准GB 11643-1999
	static public function idCardVerifyNumber($idcard_base){
		if (strlen($idcard_base) != 17){ return false; }
		// 加权因子
		$factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
	
		// 校验码对应值
		$verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
	
		$checksum = 0;
		for ($i = 0; $i < strlen($idcard_base); $i++){
			$checksum += substr($idcard_base, $i, 1) * $factor[$i];
		}
	
		$mod = $checksum % 11;
		$verify_number = $verify_number_list[$mod];
	
		return $verify_number;
	}
	
	// 将15位身份证升级到18位
	static public function idcard_15to18($idcard){
		if (strlen($idcard) != 15){
			return false;
		}else{
			// 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码
			if (array_search(substr($idcard, 12, 3), array('996', '997', '998', '999')) !== false){
				$idcard = substr($idcard, 0, 6) . '18'. substr($idcard, 6, 9);
			}else{
				$idcard = substr($idcard, 0, 6) . '19'. substr($idcard, 6, 9);
			}
		}
	
		$idcard = $idcard . self::idCardVerifyNumber($idcard);
	
		return $idcard;
	}
	
	// 18位身份证校验码有效性检查
	static public function idCardChecksum18($idcard){
		if (strlen($idcard) != 18){ return false; }
		$idcard_base = substr($idcard, 0, 17);
		if (self::idCardVerifyNumber($idcard_base) != strtoupper(substr($idcard, 17, 1))){
			return false;
		}else{
			return true;
		}
	}
	
	public static function HasNumber($str){
	    $pattern = "/[0-9]+/";
	    if(preg_match($pattern, $str)){
	        return true;
	    }
	    return false;
	}
	
	public static function HasSmallChar($str){
	    $pattern = "/[a-z]+/";
	    if(preg_match($pattern, $str)){
	        return true;
	    }
	    return false;
	}
	
	public static function HasBigChar($str){
	    $pattern = "/[A-Z]+/";
	    if(preg_match($pattern, $str)){
	        return true;
	    }
	    return false;
	}
	
	public static function HasSpecialChar($str){
	    $pattern = "/[^0-9a-zA-Z]+/";
	    if(preg_match($pattern, $str)){
	        return true;
	    }
	    return false;
	}
	
}