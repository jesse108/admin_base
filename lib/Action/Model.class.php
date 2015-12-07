<?php
class Action_Model{
	public $params = array();
	
	public $mDebug  = false;
	
	
	public function preExcute(){
		
	}
	
	public function excute(){
		
		$this->show();
	}
	
	public function afterExcute(){
		if($this->mDebug){
			dump($this->params);
		}
	}
	
	public function debug(){
		$this->mDebug = true;
        DB::SQLDebug();
	}

	public  function show($param = null,$template = null){
		if(!$template){
			$path = Router::GetPath();
			$template = trim($path,'/');
			$template .= Template::DEFAULT_TEMPLATE_SUFFIX;
		}
		$param = $param ? $param : array();
		$this->params = $this->params ? $this->params : array();
		
		$param = array_merge($this->params, $param);
		
		$this->params = $param;
		Template::Show($template,$param);
	}
}