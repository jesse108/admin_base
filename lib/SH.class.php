<?php 
require_once PLUGIN_PATH . '/Bundle/shellwrap/src/MrRio/ShellWrap.php';
// require_once PLUGIN_PATH . '/Bundle/shellwrap/vendor/autoload.php';
class SH extends \MrRio\ShellWrap {

    const LEVEL_DEBUG   = 'debug';
    const LEVEL_INFO    = 'info';
    const LEVEL_SUCCESS = 'success';
    const LEVEL_FAILED  = 'failed';
	
	static public function Run($cmd){
		$params  = explode(" ", $cmd);
		$control = array_shift($params);
		if(empty($params)){
			$ret = self::{$control}();
		}else{
			$ret =self::{$control}( implode(" ", $params) );
		}
		return trim(strval($ret));
	}

    static public $consoleOptions = array(
        self::LEVEL_DEBUG => array(
            'color' => '\033[01;30;4m [%s] \033[0m %s',
        ),
        self::LEVEL_INFO => array(
            'color' => '\033[22;34;1m [%s] \033[0m %s', //
        ),
        self::LEVEL_SUCCESS => array(
            'color' => '\033[22;32m [%s] \033[0m %s', //\033[22;32m
        ),
        self::LEVEL_FAILED => array(
            'color' => '\033[35;1m [%s] \033[0m %s',
        ),
    );

    static public function Console($string, $level =  self::LEVEL_INFO){

        echo "[{$level}]{$string}\n";
        return true;

        $option = self::$consoleOptions[$level];
        $cmd    = sprintf($option['color'], str_pad($level, 7), $string);
        $cmd="echo \"{$cmd}\n\"";
        exec($cmd, $result);

        if(isset($result[0])){
            print $result[0]. "\n";
        }
    }
}



?>