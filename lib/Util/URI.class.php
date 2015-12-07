<?php
/**
 * Created by PhpStorm.
 * User: jianyong
 * Date: 14/12/30
 * Time: 16:23
 */
class Util_URI {

//传送协议。
//服务器。
//端口号。（以数字方式表示，若为HTTP的默认值“:80”可省略）
    static public function ServerName(){

        $http = 'http://';
        $server = htmlspecialchars($_SERVER['SERVER_NAME']);

        $serverPort = 0 + $_SERVER['SERVER_PORT'];

        if($serverPort == 465){
            $http = 'https://';
            $port = '';
        }elseif($serverPort == 80){
            $port = '';
        }else{
            $port = ":{$serverPort}";
        }

        return $http . $server . $port;
    }


}