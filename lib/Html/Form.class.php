<?php
class Html_Form {

	static public function select($name, $list = array(), $selected = null, $title="", $show_key_name = 'name', $show_pk_name = 'id') {
		$default[] = "<option value='0' selected>-请选择{$title}-</option>";
		
		if($selected === null){
			if(isset($_REQUEST[$name]) && $_REQUEST[$name]){
				$selected = RequestParam::Request($name);
			}elseif( Session::Exists(SESSION_LAST_POST) ){
				$last_post = Session::Get(SESSION_LAST_POST);
				$selected = array_get($last_post, $name);
			}
		}
		
		if(!empty($list)){
			foreach ($list as $one){
				$selectedStyle = ($one[$show_pk_name] == $selected) ? 'selected' : '';
				$default[] = sprintf('<option value="%s" %s>%s</option>', $one[$show_pk_name], $selectedStyle, $one[$show_key_name]);
			}
		}
		
		return join('', $default);
	}
	
	static public function options($name, $list = array(), $selected = null, $title="", $options = array()){
        $title = $title ? $title : '请选择';

        if($options['select']){
            $default[] = sprintf('<select name="%s" %s>', $name, ($options ? join(' ', $options) : '') );
        }else{
            $default = array();
        }

		$default[] = "<option value='0' selected>{$title}</option>";
		if(!empty($list)){
			foreach ($list as $oneKey => $oneValue){
				$selectedStyle = ($oneKey == $selected) ? 'selected' : '';
				$default[] = sprintf('<option value="%s" %s>%s</option>', $oneKey, $selectedStyle, $oneValue);
			}
		}
        if($options['select']) {
            $default[] = '</select>';
        }
		return empty($default) ? '' : join('', $default);
	}
	
}