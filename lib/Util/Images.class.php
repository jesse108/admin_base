<?php
/**
 * Created by PhpStorm.
 * User: jianyong
 * Date: 14/12/23
 * Time: 17:00
 */
class Util_Images {

    static public function ReadImages($path, $dir, $ali_oss = true, $options = array()){
        if(!$path){
            throw new Exception('图片文件不存在, 请核实');
        }
        ob_start();
        if($ali_oss){
            $bucket     = array_get($options, 'bucket', Config_Aliyun_OSS::SSC_SHOP_BUCKET);
            $imageInfo  = self::ReadOSSImages($path, $bucket);
        }else{
            $imageInfo  = self::ReadLocalImages($dir . $path);
        }
        ob_end_clean();
        return $imageInfo;
    }

    static public function ReadLocalImages($path){
        if(file_exists($path)){
            $imageInfo   = getimagesize($path);
            $contentType = $imageInfo['mime'];
            $contents    = file_get_contents($path);
        }

        return array(
            'contentType'   => $contentType,
            'contents'      => $contents,
        );
    }

    static public function ReadOSSImages($object, $bucket = Config_Aliyun_OSS::SSC_SHOP_BUCKET ){
        $oss = new Lib_Aliyun_OSS($bucket);
        $oss->readFile($object);

        if($oss->status()){
            $header      = $oss->header();
            $contentType = $header['content-type'];
            $contents    = $oss->result();
        }

        return array(
            'contentType'   => $contentType,
            'contents'      => $contents,
        );
    }

    static public function AvatarResize($contents){
        if(!$contents){
            throw new Exception('一寸证件照照片不存在');
        }

        $src = imagecreatefromstring($contents);
        if ($src !== false) {

            $width  = imagesx($src);
            $height = imagesy($src);
            $new_w  = 358;
            $new_h  = 441;

            $img = imagecreatetruecolor($new_w, $new_h);
            imagecopyresized($img,$src,0,0,0,0,$new_w,$new_h,$width,$height);

            imagejpeg($img);
            imagedestroy($img);
        }else{
            throw new Exception('一寸证件照照片不存在!');
        }
    }
}