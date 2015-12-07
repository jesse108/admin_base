<?php
/**
 * 模板类  这里直接使用smarty模板
 * @author zhaojian
 *
 */
class Template{
	const DEFAULT_TEMPLATE_SUFFIX = '.html';
	public $smarty;

	public static function GetContent($template ='',$parameters = array(),$assignGlobal = true){
		ob_start();
		self::Show($template,$parameters,$assignGlobal);
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}

	public static function Show($template ='',$parameters = array(),$assignGlobal = true){
		$smarty = self::GetTemplate();
		$smarty->php_handling = Smarty::PHP_ALLOW;
		if($assignGlobal){
			$smarty = self::AssignGlobalVar($smarty);
		}
		if($parameters){
			foreach ($parameters as $key => $val){
				$smarty->assign($key,$val);
			}
		}

		if(!$template){
			$webroot = Config::Get('webroot');
			$path = $_SERVER['PHP_SELF'];

			if(trim($webroot,'/')){
				$webroot = strtr($webroot, array('/' => "\\/","\\" => "\\/"));
				$path = preg_replace("/$webroot/", '', $path,1);
			}

			$path = trim($path,'/');
			$dotPos = strpos($path,'.');
			if($dotPos !== false){
				$path  = substr($path, 0,$dotPos);
			}
			$template = $path . self::DEFAULT_TEMPLATE_SUFFIX;
		}

		///验证文件是否存在
		//404
		if(strpos($template, '/') !== 0){
			$file = TEMPLATE_PATH . '/' . $template;
		} else {
			$file = $template;
		}
		if(!file_exists($file)){
			//找不到文件   报404
			$systemConfig = Config::Get('system');
			
			if($systemConfig['404_redirect']){
				Utility::Redirect('/' . $systemConfig['error'] );
			} else {
				$template = $systemConfig['404'];
			}
		}

		///
		$smarty->display($template);
	}

	public static function GetTemplate(){
		$smarty = new Smarty();
		$smarty->setTemplateDir(TEMPLATE_PATH);
		$smarty->setCompileDir(COMPILE_PATH);
		$smarty->setConfigDir(CONF_PATH);
		$smarty->setCacheDir(CACHE_PATH);
		$smarty->addPluginsDir(TEMPLATE_PLUGINS_PATH);
		return $smarty;
	}

	public static function AssignGlobalVar(&$smarty){
		foreach ($GLOBALS as $key => $val){
			$smarty->assign($key,$val);
		}
		return $smarty;
	}
}
