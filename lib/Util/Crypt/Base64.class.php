<?php
class Util_Crypt_Base64 {

    private $_base_secret = 'hi_QjRST-UwxkVYZ0123aopqr89LMmGH45uvstbcdef+IJK6/WX7n=lDEFABCNOPyzg';

    private $only_seed = null;

    private $_secret = '';

    public function __construct($only_seed = null, $base_secret = null){
        if($base_secret){
            $this->setBaseSecret($base_secret);
        }
        $this->setOnlySeed($only_seed);
    }

    public function setBaseSecret($base_secret){
        if($base_secret){
            $this->_base_secret = $base_secret;
        }
    }

    public function setOnlySeed($only_seed = null){
        $this->only_seed = $only_seed;
    }

    public function getSecret(){
        if($this->_secret){
            return $this->_secret;
        }
        return $this->_getSecret();
    }

    private function _getSecret(){
        $base_secret_length = strlen($this->_base_secret);

        $week = intval(date("w")) + 1;
        $methon = intval(date("n"));

        $sub = ($week * $methon) % $base_secret_length;

        $only_seed = $this->only_seed;
        if($only_seed){
            $only_seed = 0 + $only_seed;
            $sub = ($sub * $only_seed) % $base_secret_length;
        }

        $new_base_secret = substr($this->_base_secret, $sub). substr($this->_base_secret, 0, $sub);
        $new_base_secret_array = str_split($new_base_secret);

        $str1 = $str2 = '';
        foreach ($new_base_secret_array AS $index => $value){
            if($index % 2){
                $str1 .= $value;
            }else{
                $str2 .= $value;
            }
        }

        $this->_secret = $str2 . $str1;
        return $this->_secret;
    }

    /**
    +----------------------------------------------------------
     * 加密字符串
    +----------------------------------------------------------
     * @param string $string 字符串
     * @param string $encode_secret 动态加密密码
    +----------------------------------------------------------
     * @return string
    +----------------------------------------------------------
     */
    public function encode($string, $encode_secret){
        $secret = $this->getSecret();
        $base64_string = base64_encode($string); //编码
        $secret_length = strlen($secret); //字符表长度 一般64

        $maxRandNum = $secret_length - 1;
        $randNum = rand(0, $maxRandNum);//生成一个随机数值 加密用

        $startRandChar = $secret{$randNum};//获取随机字符
        $endtRandChar  = $secret{$maxRandNum - $randNum};//获取随机字符

        $md5_encode_secret = md5($encode_secret. $startRandChar. $endtRandChar);
        $md5_encode_secret_length = strlen($md5_encode_secret);

        $strTemp = '';//结果
        $pwdIndex = $mapIndex = 0;

        $base64_string_length = strlen($base64_string);
        for ($i = 0; $i < $base64_string_length; $i++){
            $pwdIndex = $pwdIndex % $md5_encode_secret_length;
            $mapIndex = strpos($secret, $base64_string{$i}) + $randNum + ord($md5_encode_secret{$pwdIndex});
            $mapIndex = $mapIndex%$secret_length;
            $strTemp .= $secret{$mapIndex};
            $pwdIndex++;
        }

        $strTemp = $startRandChar.$strTemp.$endtRandChar;
        //var_dump($randNum.'|'.$startRandChar.'|'.$endtRandChar.'|'.$strTemp);
        return $strTemp;
    }

    /**
    +----------------------------------------------------------
     * 解密字符串
    +----------------------------------------------------------
     * @param string $encode_string 要解密的字符串
     * @param string $encode_secret 动态加密密码
    +----------------------------------------------------------
     * @return string
    +----------------------------------------------------------
     */
    public function decode($encode_string, $encode_secret){
        $secret = $this->getSecret();
        $secret_length = strlen($secret); //字符表长度 一般64

        $startRandChar = substr($encode_string,  0, 1);
        $endtRandChar  = substr($encode_string, -1, 1);
        $randNum = strpos($secret, $startRandChar);

        $md5_encode_secret = md5($encode_secret. $startRandChar. $endtRandChar);
        $md5_encode_secret_length = strlen($md5_encode_secret);

        $origin = '';
        $encode_temp = substr($encode_string, 1, -1);
        $encode_temp_length = strlen($encode_temp) - 1;
        for($i = $encode_temp_length; $i >= 0; $i--){
            $c_ord = ord($md5_encode_secret{$i}) + $randNum;
            $index = $this->_decodeModel(strpos($secret, $encode_temp{$i}), $c_ord);
            $origin = $secret{$index}. $origin;
        }

        return base64_decode($origin);
    }

    private function _decodeModel($con, $c_ord, $index = 0){
        $secret_length = strlen($this->_secret);

        if($index >= 50){
            return 0;
        }

        $origin = $secret_length * $index + $con - $c_ord;
        if($origin >= 0){
            return $origin;
        }

        $index++;
        $origin = $this->_decodeModel($con, $c_ord, $index);
        if($origin > 0){
            return $origin;
        }
    }

}