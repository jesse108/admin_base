<?php
/**
 * Created by PhpStorm.
 * User: jianyong
 * Date: 15/1/28
 * Time: 11:56
 */
class WeiXin_Enteprise_SuiteClient {

    private $suiteId          = '';
    private $suiteAccessToken = '';
    private $corpId           = '';
    private $permanentCode    = ''; // 永久授权码，通过get_permanent_code获取

    public $suiteTokenInfo = array();

    static public $baseURL = 'https://qyapi.weixin.qq.com/cgi-bin';

    public $httpClient;

    public function __construct($corpId = '', $suiteId = '', $suiteAccessToken = '') {

        $this->httpClient  = new WeiXin_Enteprise_HttpRequest(array(
            'base_url' => self::$baseURL
        ));

        $corpId           and $this->setCorpId($corpId);
        $suiteId          and $this->setSuiteId($suiteId);
        $suiteAccessToken and $this->setSuiteAccessToken($suiteAccessToken);
    }

    public function setCorpId($corpId){
        $this->corpId = $corpId;
    }

    public function setSuiteAccessToken($suiteAccessToken){
        $this->suiteAccessToken = $suiteAccessToken;
    }

    public function setSuiteTokenInfo($suiteTokenInfo){
        $this->suiteTokenInfo = $suiteTokenInfo;
    }

    public function setSuiteId($suiteId){
        $this->suiteId = $suiteId;
    }

    public function setPermanentCode($permanentCode){
        $this->permanentCode = $permanentCode;
    }


    public function getSuiteAccessToken($suite_secret, $suite_ticket){
        if(!$suite_secret || !$suite_ticket) return false;
        $api = '/service/get_suite_token';

        $data = array();
        $data['suite_id']     = $this->suiteId;
        $data['suite_secret'] = $suite_secret;
        $data['suite_ticket'] = $suite_ticket;

        $data = Util_Array::JsonEncode($data);
        return $this->output($this->httpClient->post($api, $data));
    }


    public function getPreAuthCode($appid){
        if(empty($appid)) return false;

        $api = sprintf(
            '/service/get_pre_auth_code?suite_access_token=%s',
            $this->suiteAccessToken
        );

        $data = array();
        $data['suite_id'] = $this->suiteId;
        $data['appid']    = $appid;

        $data = Util_Array::JsonEncode($data);
        return $this->output($this->httpClient->post($api, $data));
    }

    public function getPermanentCode($authCode){

        if(!$authCode) return false;

        $api = sprintf(
            '/service/get_permanent_code?suite_access_token=%s',
            $this->suiteAccessToken
        );

        $data = array();
        $data['suite_id']  = $this->suiteId;
        $data['auth_code'] = $authCode;

        $data = Util_Array::JsonEncode($data);
        return $this->output($this->httpClient->post($api, $data));
    }

    public function getAuthInfo($corpId = ''){
        $api = sprintf(
            '/service/get_auth_info?suite_access_token=%s',
            $this->suiteAccessToken
        );

        $corpId = $corpId ? $corpId : $this->corpId;

        $data = array();
        $data['suite_id']       = $this->suiteId;
        $data['auth_corpid']    = $this->corpId;
        $data['permanent_code'] = $this->permanentCode;

        $data = Util_Array::JsonEncode($data);
        return $this->output($this->httpClient->post($api, $data));
    }

    public function getAgent($agentid){
        if(empty($agentid)) return false;
        $api = sprintf(
            '/service/get_agent?suite_access_token=%s',
            $this->suiteAccessToken
        );

        $data = array();
        $data['suite_id']       = $this->suiteId;
        $data['auth_corpid']    = $this->corpId;
        $data['permanent_code'] = $this->permanentCode;
        $data['agentid']        = $agentid;

        $data = Util_Array::JsonEncode($data);
        return $this->output($this->httpClient->post($api, $data));
    }

    public function setAgent($agent){
        if(empty($agent)) return false;
        $api = sprintf(
            '/service/set_agent?suite_access_token=%s',
            $this->suiteAccessToken
        );

        $data = array();
        $data['suite_id']       = $this->suiteId;
        $data['auth_corpid']    = $this->corpId;
        $data['permanent_code'] = $this->permanentCode;
        $data['agent']          = $agent;

        $data = Util_Array::JsonEncode($data);
        return $this->output($this->httpClient->post($api, $data));
    }

    public function getCorpToken($corpId){
        $api = sprintf(
            '/service/get_corp_token?suite_access_token=%s',
            $this->suiteAccessToken
        );

        $corpId = $corpId ? $corpId : $this->corpId;

        $data = array();
        $data['suite_id']       = $this->suiteId;
        $data['auth_corpid']    = $corpId;
        $data['permanent_code'] = $this->permanentCode;

        $data = Util_Array::JsonEncode($data);
        return $this->output($this->httpClient->post($api, $data));
    }

    public function loginpage($pre_auth_code, $redirect_uri, $state){
        $api = sprintf(
            '/loginpage?suite_id=%s&pre_auth_code=%s&redirect_uri=%s&state=%s',
            $this->suiteId, $pre_auth_code, $redirect_uri, $state
        );
        return $api;
    }

    protected function output($response, $body = '') {
        if(!$body){
            return $response;
        }
        return isset($response[$body]) ? $response[$body] : $response;
    }

}