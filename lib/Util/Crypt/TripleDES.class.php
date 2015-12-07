<?php
/**
 * php 3des加密解密
3DES又称Triple DES，是DES加密算法的一种模式，它使用3条56位的密钥对数据进行三次加密。数据加密标准（DES）是美国的一种由来已久的加密标准，它使用对称密钥加密法，并于1981年被ANSI组织规范为ANSI X.3.92。DES使用56位密钥和密码块的方法，而在密码块的方法中，文本被分成64位大小的文本块然后再进行加密。比起最初的DES，3DES更为安全。 　　
3DES（即Triple DES）是DES向AES过渡的加密算法（1999年，NIST将3-DES指定为过渡的加密标准），是DES的一个更安全的变形。它以DES为基本模块，通过组合分组方法设计出分组加密算法，其具体实现如下：设Ek()和Dk()代表DES算法的加密和解密过程，K代表DES算法使用的密钥，P代表明文，C代表密表，这样， 　　3DES加密过程为：C=Ek3(Dk2(Ek1(P))) 　　
3DES解密过程为：P=Dk1((EK2(Dk3(C)))
3DES加密算法：密钥长度不足24字节时，右补ASCII字符“0”；内容采用PKCS5Padding方式填充，即长度以8字节切分，不能被8整除的末尾部分，根据长度不足8字节的部分，填充“0x01”—“0x08”，如不足1字节，则填充1个“0x01”，如不足2字节，则填充2个“0x02”，以此类推，如整除，则填充8个“0x08”
PHP内置的mcrypt库支持多种块状加密算法，包括DES，3DES和Blowfish（默认算法）等。由于是块状加密（Block Ciper），mcrypt支持以下模式对输入字符串进行操作：CBC, OFB, CFB和ECB。
php
3des key的长度为24字节,iv为8字节.
如果提供的key为48位,可以用pack("H48", $key) , 同理iv也可以用pack("H16", $iv);
如果提供的key为32位,可以用base64_decode解码,正好也是24位
 *
 * @link https://zh.wikipedia.org/wiki/3DES
 */
class Util_Crypt_TripleDES {

    private $key = 'B985D1B0D0CF57B674C7F4754CB96C042D79B0E7EB57D537';
    //只有CBC模式下需要iv，其他模式下iv会被忽略
    private $iv = '0102030405060708';

    public function __construct(){
        if(!function_exists('mcrypt_module_open')){
            die("mcrypt模块不存在");
        }
    }

    public function setKey($key){

        if($key){
            $this->key = $key;
        }
    }

    function pad($text){
        $text_add = strlen($text) % 8;
        for($i = $text_add; $i < 8; $i++){
            $text .= chr(8 - $text_add);
        }
        return $text;
    }

    function unpad($text){
        $pad = ord($text{strlen($text) - 1});
        if($pad > strlen($text)){return false;}
        if(strspn($text, chr($pad), strlen($text) - $pad) != $pad){return false;}
        return substr($text, 0, -1 * $pad);
    }

    function encrypt($key, $iv, $text){
        $key_add = 24 - strlen($key);
        $key .= substr($key, 0, $key_add);
        $text = $this->pad($text);
        $td = mcrypt_module_open(MCRYPT_3DES, '', MCRYPT_MODE_CBC, '');
        mcrypt_generic_init($td, $key, $iv);
        $encrypt_text = mcrypt_generic($td, $text);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        return $encrypt_text;
    }

    function decrypt($key, $iv, $text){
        $key_add = 24 - strlen($key);
        $key .= substr($key, 0, $key_add);
        $td = mcrypt_module_open(MCRYPT_3DES, '', MCRYPT_MODE_CBC, '');
        mcrypt_generic_init($td, $key, $iv);
        $text = mdecrypt_generic($td, $text);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        return $this->unpad($text);
    }

    function encode($text){
        $key = pack('H*', $this->key);
        $iv = pack('H*', $this->iv);
        return $this->encrypt($key, $iv, $text);
    }

    function decode($text){
        $key = pack('H*', $this->key);
        $iv = pack('H*', $this->iv);
        return $this->decrypt($key, $iv, $text);
    }
}
?>