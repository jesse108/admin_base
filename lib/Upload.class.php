<?php
/**
 * 处理文件上传
 * 
 * @author Jesse
 *
 */
class Upload{
	
	public static $imageTypes  = array(
		'image/jpeg','image/gif','image/pjpeg','image/png',
	);
	public static $max_upload_size = 20;//最大上传size 单位M
	/**
	 * 文件上传
	 * 
	 */
	public static function Save($uploadFileKey,$prefix = '',$saveFolder = '',$savePath = ''){
		$file = $_FILES[$uploadFileKey];
		
		if(!$file){
			System::AddError("文件上传失败,没有文件",System::MESSAGE_SYS);
			return false;
		}
		
		$error = $file['error'];
		$tmpName = $file['tmp_name'];
		
		if($error != UPLOAD_ERR_OK){
			System::AddError("文件上传失败,错误码:" . $error,System::MESSAGE_SYS);
			return false;
		}
		
		$maxSize = self::$max_upload_size;
		if($file['size'] > self::$max_upload_size * 1024 *1024){
			System::AddError("文件过大,最大不得超过{$maxSize}M:",System::MESSAGE_SYS);
			return false;
		}
		
		if(!$savePath){
			$fileNames = explode('.', $file['name']);
			$fileName = md5($fileNames[0]);
			$fileName = $prefix . $fileName . Util_String::GenRandomStr(6);
			$countNamesCount = count($fileNames);
			if($countNamesCount > 1){
				$fileName .= ".{$fileNames[$countNamesCount-1]}";
			}
			$fileName = strtolower($fileName);
			$folder = self::GetDefaultFolder($file['type'],$saveFolder);
			
			if(!$folder){
				return false;
			}
			$savePath = $folder . '/' . $fileName;
		}
		
		$result = move_uploaded_file($tmpName, $savePath);
		if(!$result){
			System::AddError("文件保存失败~");
			return false;
		}
		
		$count = strlen(ROOT_PATH);
		$savePath = substr($savePath, $count);
		return $savePath;
	}
	
	
	public static function GetDefaultFolder($type,$folder = ''){
		$date = date('Ymd');
		if($folder){
			$path = $folder;
		} else if(self::IsImage($type)){
			$path = IMG_UPLOAD_PATH;
		} else {
			$path = UPLOAD_PATH;
		}
		
		if(!file_exists($path)){
			$result = @mkdir($path,0777);
			if(!$result){
				System::AddError($path . " 创建失败");
				return false;
			}
		}
		
		$path = $path . '/' .$date;
		
		if(!file_exists($path)){
			$result = @mkdir($path,0777);
			if(!$result){
				System::AddError($path . " 创建失败");
				return false;
			}
		}
		
		if(!is_writable($path)){
			chmod($path, 0777);
		}
		
		return $path;
	}
	
	/**
	 * 判断一个类型是否是图片
	 */
	public static function IsImage($type){
		if(in_array($type, self::$imageTypes)){
			return true;
		}
		return false;
	}
}