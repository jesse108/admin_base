<?php
//核心文件
session_start();
error_reporting(E_ERROR);//报告错有错误 上线的时候需要关闭
ini_set('precision', 16);  //这只浮点型精度

///定义文件夹
define('ROOT_PATH', dirname(__FILE__));
define('LIB_PATH', ROOT_PATH.'/lib');
define('CLASS_PATH', ROOT_PATH.'/class');
define('CONF_PATH', ROOT_PATH.'/conf');
define('LANG_PATH', ROOT_PATH.'/lang');
define('COM_PATH', ROOT_PATH.'/common');
define('PLUGIN_PATH', ROOT_PATH.'/plugins');
define('TEMPLATE_PATH', ROOT_PATH.'/template');
define('TEMP_PATH', ROOT_PATH.'/temp');
define('CACHE_PATH',TEMP_PATH . '/cache');
define('COMPILE_PATH',TEMP_PATH . '/compile');
define('UPLOAD_PATH',TEMP_PATH . '/upload');
define('WEBROOT_PATH',ROOT_PATH . '/webroot');
define('STATIC_PATH',WEBROOT_PATH . '/static');
define('IMG_PATH',STATIC_PATH . '/images');
define('IMG_UPLOAD_PATH',IMG_PATH . '/upload');
define('TEMPLATE_PLUGINS_PATH',COM_PATH . '/template_plugins');
define('DOC_PATH', ROOT_PATH.'/doc');

//自动类加载
function classAutoload($strClassName)
{
	$strClassName = str_replace('_', '/', $strClassName);
	$libClassPath = LIB_PATH.'/'.$strClassName.'.class.php';
	$localClassPath = CLASS_PATH.'/'.$strClassName.'.class.php';
	if(file_exists($libClassPath)){
		require_once $libClassPath;
	} else if(file_exists($localClassPath)){
		require_once $localClassPath;
	}
}

spl_autoload_register('classAutoload');

//////////////固定文件引入
require_once COM_PATH.'/function.php'; //常用函数
require_once CONF_PATH . '/constant.php';


///插件引入
//require_once PLUGIN_PATH . '/Smarty3/libs/Smarty.class.php'; //加载模板文件
require_once PLUGIN_PATH . '/Smarty3/libs/SmartyBC.class.php'; //加载模板文件
require_once PLUGIN_PATH . '/PHPMailer/PHPMailerAutoload.php'; //加载PHP Mailer
require_once PLUGIN_PATH . '/PHPExcel/Classes/PHPExcel.php';

Config::Load();
Router::$routeType = Router::ROUTE_TYPE_METHOD;