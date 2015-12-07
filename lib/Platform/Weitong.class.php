<?php
/**
 *
 * 微网互通  发送类
 * @author sijia
 *
 */

class Platform_Weitong{

    public static $account;
    public static $password;
    public static $company;
    public static $pn;
    public static $prefix;

    public static $smsSendURL = 'http://cf.51welink.com/submitdata/Service.asmx/g_Submit';

    public static $balanceURL = 'http://cf.51welink.com/submitdata/Service.asmx/Sm_GetRemain';

    public function __construct($config = null){
        $config = $config ? $config : Config::Get('weitong');

        self::$account = $config['account'];
        self::$password = $config['password'];
        self::$company = $config['company'];
        self::$pn = $config['pn'];
        self::$prefix = $config['prefix'];
    }

    /*
     * 发送短信
     *
     */

    public function sendSMS($message,$tos){
        if(self::$prefix){
            $message = $message.self::$prefix;
        }

        if(is_array($tos)){
            $tos = implode(',',$tos);
        }

        $param = self::GetPath($message,$tos);
        $url = self::$smsSendURL;

        $result = self::Request($url, $param, 'POST');

        $result = (string) $result->State;

        if($result == '0'){
            return true;
        }else{
            return false;
        }

        return $result;

    }

    /*
     * 查询余额
     *
     */

    public function getBlance(){

        $url = self::$balanceURL;

        $param = self::GetPath();

        $result = self::Request($url,$param);

        $result = (string) $result->Remain;

        return $result;
    }

    public static function GetPath($message = null,$tos = null){
        $query_param = array(
            'sname'    => self::$account,
            'spwd'     => self::$password,
            'scorpid'  => self::$company,
            'sprdid'   => self::$pn

        );
        if($tos){
            $query_param['sdst'] = $tos;
        }
        if($message){
            $query_param['smsg'] = $message;
        }

        return $query_param;
    }


    /*
     * 发送请求
     *
     */

    public static function Request($url,$param,$method = 'GET'){
        $result = Util_HttpRequest::Request($url, $method, $param);
        $result = simplexml_load_string($result);
        return $result;
    }

}