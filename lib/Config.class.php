<?php
class Config{
	public static $config;
	
	public static function Load($name = null){
		$name = $name ? $name : 'system';
		$name = str_replace('_', '/', $name);
		$configPath = CONF_PATH . '/'.$name.'.conf.php';
		$defaulConfigPath = CONF_PATH . '/'.$name.'.conf.default.php';
		$configPath = file_exists($configPath) ? $configPath : $defaulConfigPath;
		if(file_exists($configPath)){
			require $configPath; 
			$config = $config ? $config : array();
			foreach ($config as $index => $one){
				self::$config[$index] = $one;
			}
		}
	}
	
	public static function Get($name = null,$key = null){
		if(!$name){
			return self::$config;
		}
		if(!self::$config[$name]){
			self::Load($name);
		}
		
		$config = self::$config[$name];
		
		if($key){
			$config = $config ? $config[$key] : null;
		}
		return $config;
	}
	
}