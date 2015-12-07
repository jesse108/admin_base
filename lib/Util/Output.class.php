<?php 

class Util_Output {
	
	static public function AjaxResult($result, $defaultMessage = '', $returnData = array()){
		
		if($result){
			$output = array(
				'code' => ERROR_CODE_SUCESS,
				'message' => '操作成功 ' . $defaultMessage,
				'result' => $result,
				'errors' => array(),
			);
		}else{
			$errors = System::GetSysError();
			$errors = $errors ? unserialize($errors) : array();
				
			$message = System::GetError();
			if($message && !isset($errors['other_error'])){
				$errors['other_error'] = $message;
			}
            $defaultMessage = $defaultMessage ? $defaultMessage : '操作失败';
			$output = array(
				'code' => ERROR_CODE_FAIL,
				'message' => $message ? $message : $defaultMessage,
				'result' => $result,
				'errors' => $errors,
			);
		}
		$returnData = empty($returnData) ? array() : $returnData;
		$output     = array_merge($output, $returnData);
		
		return $output;
	}
	
	
	static public function AjaxUnloginResult($returnData = array()){
		return array(
			'code' => ERROR_CODE_UNLOGIN,
			'message' => '请登录之后在进行此操作',
			'datas' => $returnData,
			'result' => '',
		);
	}
	

	
	
	
	
	
	
}

?>