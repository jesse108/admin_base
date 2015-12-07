<?php 
class Util_Zip {

    const TYPE_STRING = 'string';
    const TYPE_FILE   = 'file';
    const TYPE_OSS    = 'oss';

    static public function FileZip($filePaths, $zipFilePath){

        $zip = new ZipArchive();

        $openRet = $zip->open($zipFilePath, ZipArchive::OVERWRITE);
        if ($openRet === TRUE) {
            foreach ($filePaths as $oneFileKey => $oneFilePath){
                if(is_numeric($oneFileKey)){
                    if(!file_exists($oneFilePath)) continue;
                    $zip->addFile($oneFilePath, basename($oneFilePath));
                }else{
                    if(!file_exists($oneFileKey)) continue;
                    $zip->addFile($oneFileKey, $oneFilePath);
                }
            }
            $zip->close();
        }

        return $zipFilePath;
    }

    static public function StringZip($fileContents, $zipFilePath){
        if(!$zipFilePath){
            return false;
        }

        $dir = dirname($zipFilePath);
        if(!file_exists($dir)){
            mkdir($dir, 0777, true);
        }

        $zip = new ZipArchive();

        $openRet = $zip->open($zipFilePath, ZipArchive::OVERWRITE);

        if ($openRet === TRUE) {
            foreach ($fileContents as $oneFilePath => $oneFileContent){
                $zip->addFromString($oneFilePath, $oneFileContent);
            }
            $zip->close();
        }

        if(!file_exists($zipFilePath)){
            return false;
        }

        return $zipFilePath;
    }

    static public function ZipTools($fileContents, $zipFilePath){
        if(!$zipFilePath){
            return false;
        }

        $dir = dirname($zipFilePath);
        if(!file_exists($dir)){
            mkdir($dir, 0777, true);
        }

        $zip = new ZipArchive();

        $openRet = $zip->open($zipFilePath, ZipArchive::OVERWRITE);

        if ($openRet === TRUE) {
            foreach ($fileContents as $option){

                if(!$option['file'] || !$option['zipfile']) continue;

                $option['zipfile'] = iconv('UTF-8', 'GBK', $option['zipfile']);
                switch($option['type']){
                    case self::TYPE_FILE:
                        if(!file_exists($option['file'])) continue;
                        $zip->addFromString($option['zipfile'], file_get_contents($option['file']));
                        break;
                    case self::TYPE_STRING:
                        $zip->addFromString($option['zipfile'], $option['file']);
                        break;
                    case self::TYPE_OSS:
                        $zip->addFromString($option['zipfile'], read_oss_file($option['file']));
                        break;
                }
            }
            $zip->close();
        }

        if(!file_exists($zipFilePath)){
            return false;
        }

        return $zipFilePath;
    }

    static public function ZipFileInfo($filename, $zipFile, $type = self::TYPE_OSS){
        return array(
            'file'      => $filename,
            'zipfile'   => $zipFile,
            'type'      => $type,
        );
    }

}
?>