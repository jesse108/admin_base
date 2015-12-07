<?php
class Utility{
	public static $numberMap = array(
		0 => '零',1 => '一',2=>'二',3=>'三',4=>'四',5=> '五',6=>'六',7=>'七' ,8=> '八',9=>'九',
	);
	
	const RETURN_URL_KEY = "Utility_Return_Url";
	
	

	
	
	public static function getUserIP($defaultIP = null){ //获取用户IP todo
		
		if(isset($_SERVER['HTTP_CLIENTIP'])){
			$userIP = $_SERVER['HTTP_CLIENTIP'];
		} else if(isset($_SERVER['REMOTE_ADDR'])){
			$userIP = $_SERVER['REMOTE_ADDR'];
		} else if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
			$userIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
			$intPos = strrpos($userIP, ',');
			if($intPos > 0){
				$userIP = substr($userIP, $intPos+1);
			}
		} else if(isset($_SERVER['HTTP_CLIENT_IP'])){
			$userIP = $_SERVER['HTTP_CLIENT_IP'];
		}
		$userIP = strip_tags($userIP);
		$userIP = trim($userIP);
		
		if(!$userIP && $defaultIP){
			$userIP = $defaultIP;
		}
		
		return $userIP;
	}
	
	/**
	 * 页面跳转函数
	 *
	 * 这里使用修改头文件的方式现实页面跳转
	 * 这里要注意的是调用这个函数之前 页面不能有任何输出 否则跳转失败
	 * 跳转后会退出程序
	 *
	 * @param string $u 跳转页面
	 */
	public static function Redirect($u=null,$referer = false,$return = false) {
		if($referer){
			$u = $_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : $u;
		}
		
		if($return){
			$returnURL = Utility::GetReturnUrl();
			$u = $returnURL ? $returnURL : $u;
		}
		
		if (!$u) $u = '/';
		Header("Location: {$u}");
		exit;
	}
	
	public static function SetReturnUrl($url = ''){
		$url = $url ? $url : self::GetCurrentUri();
		Session::Set(self::RETURN_URL_KEY, $url);
	}
	
	public static function GetReturnUrl(){
		return Session::Get(self::RETURN_URL_KEY, true);
	}
	
	public static function GetCurrentUri(){
		$uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
		$uri = htmlspecialchars($uri);
		return $uri;
	}
	
	public static function GetCurrentUrL(){
		$uri = $_SERVER['REQUEST_URI'];
		return $uri;
	}
	
	public static function IsCurrentURI($key){
		$currentURI = self::GetCurrentUri();
		$key = "#{$key}#";
		if(preg_match($key, $currentURI)){
			return true;
		}
		return false;
	}
	public static function GetCurrentFullURL(){
		$host = $_SERVER['HTTP_HOST'];
		$uri = $_SERVER['REQUEST_URI'];
		
		$url = $host . $uri;
		return 'http://' . $url;
	}
	
	
	public static function GetRefer(){
		$url = $_SERVER['HTTP_REFERER'];
		return $url;		
	}
	
	public static function getPrevUrl($default = ''){
		$url = self::GetRefer();
		$url = $url ? $url : $default;
		return $url;
	}
	
	public static function GetPathInfo(){
		$pathInfo = $_SERVER['PATH_INFO'];
		return $pathInfo;
	}
	
	public static function CompleteURL($url){
		$url = trim($url);
		if(!$url){
			return $url;
		}
		if(strpos($url,'http') !== false ||
			strpos($url,'http') !== false){
			return $url;
		}
		
		if($url{0} != '/'){
			$url = '/'.$url;
		}
		
		$url = "http://{$_SERVER['HTTP_HOST']}{$url}";
		return $url;
	}
	
	public static function GetQueryString($parse = false){
		$queryString = $_SERVER['QUERY_STRING'];	
		if($parse){
			parse_str($queryString, $params);
			return $params;
		}
		return $queryString;
	}
	
	/**
	 * 阿拉伯数字转化成中文数字
	 */
	public static function TransNumberToCN($number){
		$number = strval($number);
		
		$temp = '';
		for($i = 0 ; $i< strlen($number); $i ++){
			$curNumStr = self::$numberMap[$number[$i]];
			if($curNumStr){
				$temp .= $curNumStr;
			}
		}
		
		return $temp;
	}
	
	public static function Download($filePath,$fileName,$isFile = true){
		if($isFile && !file_exists($filePath)){
			header("Content-type: text/html; charset=utf-8");
			echo "没有找到文件";
			exit;
		} else {
            header("Content-Type: application/octet-stream");
            header('Content-Type: application/x-download');
            header("Accept-Ranges: bytes");
            self::FileNameHeader($fileName);
            if($isFile){
                $file = fopen($filePath, 'r');
                $fileCount = 0;
                $fileSize = filesize($filePath);
                $buffer = 1024;

                header("Accept-Length: ".$fileSize);
                while(!feof($file) && $fileCount<$fileSize){
                    $curStr = fread($file,$buffer);
                    $fileCount+=$buffer;
                    echo $curStr;
                }
                fclose($file);
            }else{
                $fileSize = strlen($filePath);
                header("Accept-Length: ".$fileSize);
                echo $filePath;
            }
		}
	}
	
	// 第二个参数表示处理模式，
	//	ZipArchive::OVERWRITE表示如果zip文件存在，就覆盖掉原来的zip文件。
	//  ZIPARCHIVE::CREATE，系统就会往原来的zip文件里添加内容。
// 		$path 			= array(
// 			WEBROOT_PATH . '/static/images/404.jpg', 
// 			WEBROOT_PATH . '/static/images/sfzb.jpg'
// 		);
// 		$zipfilename 	= WEBROOT_PATH .'/static/downloads/abcd.zip';
		
// 		Utility::ZipDownload($path, $zipfilename);
	public static function ZipDownload($filePaths, $zipfilename, $processMode = ZipArchive::OVERWRITE){
		if(empty($filePaths)){
			header("Content-type: text/html; charset=utf-8");
			echo "没有找到文件";
			exit;
		}
		
		$zip = new ZipArchive();
		
		$openRet = $zip->open($zipfilename, $processMode);
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
		
		header("Cache-Control: public");
		header("Content-Description: File Transfer");
        self::FileNameHeader($zipfilename);
		header("Content-Type: application/zip"); 			//zip格式的
		header("Content-Transfer-Encoding: binary"); 		//告诉浏览器，这是二进制文件
		header('Content-Length: '. filesize($zipfilename)); //告诉浏览器，文件大小
		header('Pragma: no-cache');
		header('Expires: 0');
		@readfile($zipfilename);
	}
    static public function FileNameHeader($name){

//string(72) "Mozilla/5.0 (Windows NT 6.3; WOW64; rv:38.0) Gecko/20100101 Firefox/38.0"
//string(68) "Mozilla/5.0 (Windows NT 6.3; WOW64; Trident/7.0; rv:11.0) like Gecko"
//Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.104 Safari/537.36

        //处理中文文件名
        $ua = $_SERVER["HTTP_USER_AGENT"];
        if (preg_match("/Firefox/", $ua)) {
            header("Content-Disposition: attachment; filename*=\"utf8''" . $name . '"');
//            header("Content-Disposition","attachment;filename*=utf-8'zh_cn'{$name}");
        } else if (preg_match("/MSIE/", $ua) || preg_match("/Trident/", $ua)) {
            $encoded_filename = rawurlencode($name);
            header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
        } else {
            header('Content-Disposition: attachment; filename="' . $name . '"');
        }
    }

    public static function ExcelDown($objPHPExcel, $name = 'doc.xlsx'){
        if(!$objPHPExcel){
            return false;
        }
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

        self::FileNameHeader($name);

        header('Cache-Control: max-age=0');
        header ('Pragma: public'); // HTTP/1.0
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Type: application/octet-stream");
        header('Content-Type: application/x-download');
        $objWriter->save('php://output');
        exit;
    }
	
}