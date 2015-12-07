<?php
/**
 * Created by PhpStorm.
 * User: jianyong
 * Date: 14/12/26
 * Time: 14:22
 */

use ElephantIO\Client,
    ElephantIO\Engine\SocketIO\Version1X;

require dirname(dirname(dirname(__FILE__))) . '/plugins/elephant.io/vendor/autoload.php';

class Notify_ElephantIO extends Client {

    static private $client = null;

    static public function Instance(){
        if(self::$client === null){
            $domain = Config_System::$notifyDomain;
            self::$client = new Client(new Version1X($domain));
            self::$client->initialize();
        }

        return self::$client;
    }

    static public function EmitNotify($notifyInfo){
        if(!$notifyInfo) return false;
        self::Instance();
        return self::$client->emit('message', $notifyInfo);
    }

    static public function ClientClose(){
        self::Instance();
        self::$client->close();
    }

}
