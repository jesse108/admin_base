<?php
/**
 * Created by PhpStorm.
 * User: jianyong
 * Date: 15/1/27
 * Time: 10:49
 */

class WeiXin_Enteprise {

    static private $wxcptInstances = array();

    static private $instance = null;

    static private $options = array(
        'token'             => 'wgHhfakpwbJbSeoN9dK9yeCh6RM',
        'corpid'            => 'wx479e32a3e4e44805',
        'encodingAesKey'    => 'AUCK8DqKy4RL5xTW7hnHfm9vW4tm2RK2g2bE63ggOCV',
    );

    static private $suiteOptions = array(
        'corpid'        => 'wx479e32a3e4e44805',
        'suite_id'      => 'tjd954dc4a67334ed6',
        'suite_secret'  => 'nFn0bW28ecsrVybTs8bRmt28I8dUtejl8NMpGKcL-vUE_iWxe2wBuJfwpM060FD6',
        'suite_ticket'  => '',
        'token'         => 'BNMoIC8VBGmdLhnGJg',
        'encodingAesKey'=> '3TecotnXTQ9TLGJVz1yWYvaQzwQa2qPV8CJwaiAVpxM',
    );

    static public function InitInstance($options = array()){

        empty($options) and ($options = self::$options);

        $corpId = $options['corpid'];
        if(isset(self::$wxcptInstances[$corpId]) && self::$wxcptInstances[$corpId] !== null){
        }else{
            include_once PLUGIN_PATH . "/weixin/enteprise/WXBizMsgCrypt.php";

            $wxcpt = new WXBizMsgCrypt($options['token'], $options['encodingAesKey'], $corpId);
            self::$wxcptInstances[$corpId] = $wxcpt;
        }

        self::$instance = self::$wxcptInstances[$corpId];
        return self::$instance;
    }

    static public function VerifyURL($data){

        $sEchoStr = "";
        $errCode = self::$instance->VerifyURL($data['msg_signature'], $data['timestamp'], $data['nonce'], $data['echostr'], $sEchoStr);

        if ($errCode != 0) {
            self::Error("ERR: " . $errCode, $errCode);
        }

        return self::SetResponce($sEchoStr);
    }

    static public function DecryptMsg($data, $sReqData){
        $sMsg = "";  // 解析之后的明文
        $errCode = self::$instance->DecryptMsg($data['msg_signature'], $data['timestamp'], $data['nonce'], $sReqData, $sMsg);

        if ($errCode != 0) {
            self::Error("ERR: " . $errCode, $errCode);
        }

        return Util_String::XMLToArray($sMsg);
    }

    static public function EncryptMsg($sRespData, $reqData){

        $reqData['msg_encrypt']     = self::$options['encodingAesKey'];

        $sRespData['msgType']       = $sRespData['type'] ? $sRespData['type'] : 'text';
        $sRespData['createTime']    = time();
        $sRespData['FromUserName']  = self::$options['corpId'];

        $template = Template::GetContent('weixin/enteprise/resp_data.xml', array(
            'data'      => $sRespData,
            'reqData'   => $reqData,
        ));
        $template = strtr($template, array(
            "\n" => '',
            "\r" => '',
            "\t" => '',
            " "  => '',
        ));

        $sEncryptMsg = ""; //xml格式的密文
        $errCode = self::$instance->EncryptMsg($template, $reqData['timestamp'], $reqData['nonce'], $sEncryptMsg);

        if ($errCode != 0) {
            self::Error("ERR: " . $errCode, $errCode);
        }

        return self::SetResponce($sEncryptMsg);
    }

    static public function GetAccessToken($corpid, $corpsecret){
        $api = sprintf(
            '%s/gettoken?corpid=%s&corpsecret=%s',
            WeiXin_Enteprise_Client::$baseURL, $corpid, $corpsecret
        );
        $tokenInfo = Util_HttpRequest::Get($api);
        if($tokenInfo){
            return json_decode($tokenInfo, true);
        }
        return array();
    }

    static public function RenderOAuthURL($corpId, $url, $state){
        if(!$url) return '';
        $authorize = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=snsapi_base&state=%s#wechat_redirect';
        $authorize = sprintf(
            $authorize,
            $corpId, $url, $state
        );
        return $authorize;
    }


    static public function SetResponce($string){
        echo $string;
        return $string;
    }

    static public function Error($string, $code = 0){
        throw new Exception($string, $code);
    }

    static public function GetOptions($key = ''){
        return $key ? self::$options[$key] : self::$options;
    }
    static public function GetSuiteOptions($key = ''){
        return $key ? self::$suiteOptions[$key] : self::$suiteOptions;
    }

}