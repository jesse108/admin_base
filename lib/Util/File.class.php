<?php
/**
 * Created by PhpStorm.
 * User: jianyong
 * Date: 14/12/24
 * Time: 12:17
 */
class Util_File {

    static public function GetExt($file){
        if(!$file){
            return '';
        }

        if(file_exists($file)){
            $fileInfo = pathinfo($file);
            $extname  = $fileInfo["extension"];
        }else{
            $extname = end(explode(".", $file));
        }

        return $extname;
    }

    static public function CopyFile($old_root, $new_root){
        if(!file_exists($old_root)){
            return false;
        }

        if(!file_exists($new_root)){
            @copy($old_root, $new_root);
        }

        return file_exists($new_root) ? true : false;
    }

    static public function UploadFileToOSS($oss_object, $dir){
        if(!$oss_object) return false;
        try{
            $oss = new Lib_Aliyun_OSS(Config_Aliyun_OSS::SSC_SHOP_BUCKET);
            if(!$oss->objectExists($oss_object)){
                $oss->uploadFile($oss_object, $dir);
            }
        }catch (Exception $e){
            return false;
        }
        return true;
    }

    static public function MkDir($dir){
        if(!$dir) return false;

        if(!file_exists($dir)){
            mkdir($dir, 0777, true);
        }

        return true;
    }

    static public function Touch($file){
        if(!$file) return '';

        if(!file_exists($file)){
            $dir = dirname($file);
            self::MkDir($dir);

            @touch($file);
            @chmod($file, 0777);
        }

        return file_exists($file);
    }

    static public function CreatePdf($contents, $filename){
        if(!$contents) return '';

        $snappy = Lib_Plugins::CreateSnappyPdf();
        $snappy->setOption('lowquality',  false);
        $snappy->setOption('margin-top',  '31');

        $dir = dirname($filename);
        Util_File::MkDir($dir);

        $snappy->generateFromHtml($contents, $filename);
        return file_exists($filename) ? true : false;
    }

    static public function Write($file, $contents = ''){

        if(!$file) return false;

        self::MkDir(dirname($file));

        $f = @fopen($file, 'ba+');
        if($f){
            fwrite($f,$contents,strlen($contents));
            fclose($f);
            return true;
        }

        return false;
    }
}