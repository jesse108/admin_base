<?php
/**
 * Created by PhpStorm.
 * User: jianyong
 * Date: 15/1/27
 * Time: 11:58
 */
class WeiXin_Enteprise_Client {

    static public $baseURL = 'https://qyapi.weixin.qq.com/cgi-bin';

    public $accessToken;
    public $httpClient;
    public $corpId;

    public function __construct($accessToken, $agentId = 0, $corpId = 0) {

        $this->agentId     = $agentId;
        $this->accessToken = $accessToken;
        $this->httpClient  = new WeiXin_Enteprise_HttpRequest(array(
            'base_url' => self::$baseURL
        ));
        $this->setCorpId($corpId);
    }

    public function setHttpClient(WeiXin_Enteprise_HttpRequest $client) {
        $this->httpClient = $client;
        return $this;
    }

    public function setCorpId($corpId){
        $this->corpId = $corpId;
    }

    public function getDepartments() {
        $api = sprintf('/department/list?access_token=%s', $this->accessToken);
        return $this->output($this->httpClient->get($api), 'department');
    }

    public function getEmployees($departmentId, $fetchChild = 0, $status = 0) {

        $api = sprintf(
            '/user/simplelist?access_token=%s&department_id=%d&fetch_child=%d&status=%d',
            $this->accessToken, $departmentId, $fetchChild, $status
        );

        return $this->output($this->httpClient->get($api), 'userlist');
    }

    public function getEmployeeDetail($userId) {
        $api = sprintf(
            '/user/get?access_token=%s&userid=%s',
            $this->accessToken, $userId
        );

        return $this->output($this->httpClient->get($api));
    }

    public function getUserId($code) {
        $api = sprintf(
            '/user/getuserinfo?access_token=%s&code=%s&agentid=%d',
            $this->accessToken, $code, $this->agentId
        );

        return $this->output($this->httpClient->get($api));
    }

    public function menuCreate($menus){
        $api = sprintf(
            '/menu/create?access_token=%s&agentid=%d',
            $this->accessToken, $this->agentId
        );

        $menus = Util_Array::JsonEncode($menus);
        return $this->output($this->httpClient->post($api, $menus));
    }

    public function userCreate($data){
        $api = sprintf(
            '/user/create?access_token=%s',
            $this->accessToken, $this->agentId
        );

        $data = Util_Array::JsonEncode($data);
        return $this->output($this->httpClient->post($api, $data));
    }

    public function departmentCreate($data){
        $api = sprintf(
            '/department/create?access_token=%s',
            $this->accessToken, $this->agentId
        );

        $data = Util_Array::JsonEncode($data);
        return $this->output($this->httpClient->post($api, $data));
    }

    public function messageSend($message){
        $api = sprintf(
            '/message/send?access_token=%s&agentid=%d',
            $this->accessToken, $this->agentId
        );
        $message['agentid'] = $this->agentId;
        $message = Util_Array::JsonEncode($message);

        return $this->output($this->httpClient->post($api, $message));
    }

    public function inviteSend($invite){

        $api = sprintf(
            '/invite/send?access_token=%s',
            $this->accessToken
        );

        $invite = Util_Array::JsonEncode($invite);

        return $this->output($this->httpClient->post($api, $invite));
    }

    const MEDIA_TYPE_IMAGE = 'image';
    const MEDIA_TYPE_VOICE = 'voice';
    const MEDIA_TYPE_VIDEO = 'video';
    const MEDIA_TYPE_FILE  = 'file';

    // https://qyapi.weixin.qq.com/cgi-bin/media/upload?access_token=ACCESS_TOKEN&type=TYPE
    public function mediaUpload($media, $mediaType = self::MEDIA_TYPE_IMAGE){

        $api = sprintf(
            '/media/upload?access_token=%s&type=%s',
            $this->accessToken, $mediaType
        );

        return $this->output($this->httpClient->http($api, 'POST', $media));
    }


    protected function output($response, $body = '') {
        if(!$body){
            return $response;
        }
        return isset($response[$body]) ? $response[$body] : $response;
    }
}