<?php
class Queue_Model_MNS{
    
    public static $beijingURL =  "mns.cn-beijing.aliyuncs.com";//"mns.cn-beijing-internal.aliyuncs.com";
    
    public $accessID;
    public $accessKey;
    public $accountID;
    public $version = '2015-06-06';
    public $contentType =  'text/xml;utf-8';
    public $queueName = '';
    
    public function __construct($accessID = null,$accessKey = null,$accountID = null){
        $this->accessID = $accessID ? $accessID : Config::Get('aliyun','access_id');
        $this->accessKey = $accessKey ? $accessKey : Config::Get('aliyun','access_key');
        $this->accountID = $accountID ? $accountID : Config::Get('aliyun','account_id');
    }
    
    public function setQueue($queueName){
        $this->queueName = $queueName;
    }
    
    
    /**
     * 获取队列信息
     */
    public function getQueueInfo(){
        $path = "/queues/{$this->queueName}";
        $result = $this->request( 'GET','',$path);
        return $result;
    }
    
    public function sendMessage($message,$delay = 0,$priority = 1){
        $param = array(
            'MessageBody' => $message,
            'DelaySeconds' => intval($delay),
            'Priority' => intval($priority),
        );
        
        $path = "/queues/{$this->queueName}/messages";
        $result = $this->request('POST', $param,$path);
        return $result;
    }
    
    public function receiveMessage($waitseconds = 0){
        $path = "/queues/{$this->queueName}/messages?waitseconds={$waitseconds}";
        $result = $result = $this->request('GET', '', $path);
        if(!$result || $result['Code']){
            return false;
        }
        return $result;
    }
    
    public function delMessage($receiptHandle){
        $receiptHandle = urlencode($receiptHandle);
        $path = "/queues/{$this->queueName}/messages/?ReceiptHandle={$receiptHandle}";
        $result = $this->request('DELETE', '', $path);
        return $result;
    }
    
    
    public function request($method,$param,$path){
        $content = self::buildXML($param);
        $queueName = $this->queueName;
        $date = Util_Time::GetGMTDate();
        
        $canonicalizedMQSHeaders = array(
            'x-mns-version' => $this->version,
        );
        
        $sign = $this->createSign($method, $canonicalizedMQSHeaders,$date,$path);
        $host = $this->accountID . '.' . self::$beijingURL;
        
        $headers = array(
            'Host' => $host,
            'Date' => $date,
            'Content-Type' => $this->contentType,
            'Authorization' => $sign,
        );
        
        foreach ($canonicalizedMQSHeaders as $key => $value){
            $headers[$key] = $value;
        }
        
        $uri = $host . $path;
        $result = self::Http($uri, $method, $headers, $content,$path);
        if($result){
            $result = Util_String::XMLToArray($result);
        }
        return $result;
    }
    
    
    
    public function createSign($method,$mqsHeaders,$date,$path){
        $contentType = $this->contentType;
        $xMqsHeadersString = '';
        $mqsHeaders = Util_Array::Sort($mqsHeaders);
        foreach ($mqsHeaders as $key => $value){
            $xMqsHeadersString .= "{$key}:{$value}\n";
        }
        $str2Sign = "{$method}\n\n{$contentType}\n{$date}\n{$xMqsHeadersString}{$path}";
        $sig = base64_encode(hash_hmac('sha1',$str2Sign,$this->accessKey,true));
        return "MNS " . $this->accessID . ":" . $sig;
    }
    
    public static function Http($uri,$method,$header,$body,$path){
        $header = $header ? $header : array();
        if($body){
            $header['Content-Length'] = strlen($body);
        }
        
        $_header = array();
        foreach ($header as $key => $value){
            $_header[] = $key . ':' . $value;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $_header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }
    
    public static function buildXML($param){
        if(!$param){
            return '';
        }
        
        $xml = "";
        foreach ($param as $key => $value){
            $xml .= "<{$key}>{$value}</{$key}> \n";
        }
        $xml = trim($xml,"\n");
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>
<Message xmlns=\"http://mns.aliyuncs.com/doc/v1/\">
{$xml}
</Message>";
        return $xml;
    }
}

