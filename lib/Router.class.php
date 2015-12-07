<?php
/**
 * 请求转发类   系统核心类
 * @author zhaojian
 *
 */
class Router{
    const ROUTE_TYPE_PATHINFO = 1; //使用pathInfo 进行处理
    const ROUTE_TYPE_METHOD = 2;  //使用参数传递的方式处理
    const ROUTE_TYPE_NO_REWEIRTE = 3;// 没有重写
    
    public static $pathMethodKey = 'm';
    
	public static $defaultPath = 'index';
	public static $pathSeparator = '/';
	public static $defualtAction = "Action_Model";
	public static $routeType = self::ROUTE_TYPE_PATHINFO;
	public static $path = '';
	
	
	public function __construct(){
		$pathStr = self::GetPathStr();
		$pathInfo = self::ParsePath($pathStr);
		
		self::$path = $pathInfo['path'];
		
		if($pathInfo['param']){
			self::SetParam($pathInfo['param']);
		}
	}
	
	
	public function dispath(){
		$this->direct();
		$actionClassName = $this->getActionClassName();
		
		if(class_exists($actionClassName,true)){
			$action = new $actionClassName();
		} else {
			$action = new self::$defualtAction();
		}
		$action->preExcute();
		$action->excute();
		$action->afterExcute();
	}
	
	
	
	
	
	public function getActionClassName(){
		$path = self::$path;
		$pathSeparator = self::$pathSeparator;
		$paths = explode($pathSeparator, $path);
		
		$className = "Action";
		foreach ($paths as $curPath){
			$className .= '_' . ucfirst($curPath);
		}
		return $className;
	}
	
	////工具方法
	public static function SetParam($param){
		if($param){
			foreach ($param as $key => $value){
				if(!isset($_GET[$key])){
					$_GET[$key] = $value;
				}
				
				if(!isset($_REQUEST[$key])){
					$_REQUEST[$key] = $value;
				}
			}
		}
	}

	public   static function  GetPathStr(){
	    switch (self::$routeType){
	        case self::ROUTE_TYPE_PATHINFO:
	            $path = $_SERVER['PATH_INFO'];
	            break;
	        case self::ROUTE_TYPE_METHOD:
	            $path = $_GET[self::$pathMethodKey];
	            break;
	        case self::ROUTE_TYPE_NO_REWEIRTE:
	            $path = $_SERVER['SCRIPT_NAME'];
	            $path = trim($path,'.php');
	            break;
	    }
	    $path = $path ? $path : self::$defaultPath;
	    
	    if($path[0] != '/'){
	        $path = '/' . $path;
	    }
	    
		$routeConfig = Config::Get('route');
		//静态路由
		if($routeConfig[$path]){
			$path = $routeConfig[$path];
		} else {
		//动态路由
			foreach ($routeConfig as $pattern => $replace){
				$pattern = "#^$pattern$#";
				if(preg_match($pattern, $path)){
					$path = preg_replace($pattern, $replace, $path);
					break; //第一次匹配到后退出
				}
			}
		}
		return $path;
	}
	
	public static function ParsePath($pathStr){
		$pos = strpos($pathStr, '?');
		if($pos !== false){
			$path = substr($pathStr, 0,$pos);
			$paramStr = substr($pathStr, $pos + 1);
		} else {
			$path = $pathStr;
		}
		
		if($paramStr){
			parse_str($paramStr,$param);
		}
		
		$path = trim($path,self::$pathSeparator . ' ');
		
		$result = array(
			'path' => $path,
			'param' => $param,
		);
		return $result;
	}
	
	public static function GetPath(){
		$pathStr = self::GetPathStr();
		$pathInfo = self::ParsePath($pathStr);
		return $pathInfo['path'];
	}
	
	
	public static function direct(){
		$url = Utility::GetCurrentFullURL();
		$redirectConfig = Config::Get('redirect');
		$redirectConfig = $redirectConfig ? $redirectConfig : array();
		foreach ($redirectConfig as $curUrl => $to){
			if($url == $curUrl){
				Utility::Redirect($to);
			}
		}
	}
}