<?php
/**
 * Created by PhpStorm.
 * User: jianyong
 * Date: 15/1/27
 * Time: 12:04
 */
class WeiXin_Enteprise_HttpRequest {

    private $base_url = '';

    public function __construct($options = array()){
        $options['base_url'] && ($this->base_url = $options['base_url']);
    }

    public function get($url, $params = array()){
        $url = $this->base_url . $url;
        $j = Util_HttpRequest::Get($url, $params);

        return $this->render($j);
    }

    public function post($url, $params = array(), $header = array()){

        $url = $this->base_url . $url;
        $j = Util_HttpRequest::Post($url, $params, $header);

        return $this->render($j);
    }

    public function http($url, $method, $params = array(), $header = array()){

        $url = $this->base_url . $url;
        $j = Util_HttpRequest::Http($url, $method, $params, $header);

        return $this->render($j);
    }

    public function render($j){
        if($j){
            $ret = json_decode($j, true);
            if($ret['errcode'] != 0){
                $errmsg = WeiXin_Enteprise_ErrorCode::GetMsg($ret['errcode']);
                WeiXin_Enteprise::Error($errmsg, $ret['errcode']);
            }

            return $ret;
        }
        return false;
    }

}