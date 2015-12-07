<?php
/**
 * Created by PhpStorm.
 * User: jianyong
 * Date: 14/12/29
 * Time: 12:07
 */
class Weibo_API {

//iphone新浪微博客户端 App Key：5786724301
//iPad新浪客户端App Key：2849184197
//Google.Nexus浪客户端App Key：1206405345
//周博通微博管家App Key：202088835
//Weico App Key：211160679
    static public function GetShorTen($url, $one = true, $source = '5786724301'){

        $uTpl = 'https://api.weibo.com/2/short_url/shorten.json?source=%s&url_long=%s';
        $link = sprintf($uTpl, $source, urlencode($url));
        $ret  = Util_HttpRequest::Get($link);
        if($ret){
            $ret = json_decode($ret, true);
        }
        $urls = array_get($ret, 'urls');
        return $one ? Util_Array::GetFristItem($urls) : $urls;
    }

    static public function WeiboSDK(){
        if(!defined(WB_AKEY)){
            include_once( PLUGIN_PATH . '/config.php' );
            include_once( PLUGIN_PATH . '/saetv2.ex.class.php' );
        }
    }


    static public $TClient = null;

    static public $TOAuth  = null;

    static public function GetTClient($access_token = NULL, $refresh_token = NULL){
        if(self::$TClient === null){
            self::WeiboSDK();
            self::$TClient = new SaeTClientV2( WB_AKEY , WB_SKEY , $access_token, $refresh_token );
        }

        return self::$TClient;
    }

    static public function GetTOAuth($access_token = null, $refresh_token = null){
        if(self::$TOAuth === null){
            self::WeiboSDK();
            self::$TOAuth = new SaeTOAuthV2( WB_AKEY , WB_SKEY, $access_token, $refresh_token );
        }
        return self::$TOAuth;
    }

}