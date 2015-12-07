<?php
require_once  PLUGIN_PATH . '/aliyun/oss/sdk.class.php';

class Aliyun_OSS{
    const HOST_BEIJING_IN = 'oss-cn-beijing-internal.aliyuncs.com'; //北京内网
    
    public $host;
    public $ossObj;
    
    public function __construct($host = null){
        $host = $host ? $host : self::HOST_BEIJING_IN;
        $this->host = $host;
        $oss = new ALIOSS(null,null,$host);
        $this->ossObj = $oss;
    }
    
    public function uploadByFile($bucket,$object,$file){
       $result = $this->ossObj->upload_file_by_file($bucket, $object, $file);
       return $result;
    }
    
    public function uploadByContent($bucket,$object,$content){
        $option = array(
          ALIOSS::OSS_CONTENT=> $content,
        );
        $result = $this->ossObj->upload_file_by_content($bucket, $object,$option);
        return $result;
    }
    
    public function getFile($bucket,$object){
        $result = $this->ossObj->get_object($bucket, $object);
        return $result->body;
    }
    
    public function downLoad($bucket,$object,$fileName){
        $content = $this->getFile($bucket, $object);
        if(!$content){
            return false;
        }
        Utility::Download($content, $fileName,false);
        exit;
    }
    
    public function getObjectList($bucket,$folder = null){
        $option = array();
        if($folder){
            $folder = trim($folder,'/ ');
            $folder = $folder . '/';
            $option['prefix'] = $folder;
        }
        $requestResult = $this->ossObj->list_object($bucket,$option);
        $result = Util_XML::XMLToArray($requestResult->body);
        return $result;
    }
    
    
    
}