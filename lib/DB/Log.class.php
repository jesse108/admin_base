<?php
/**
 * 详细数据库日志类
 * 使用此工具的日志表结构必须如下
 * 
 * CREATE TABLE IF NOT EXISTS `log` (
  `id` BIGINT(20) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `create_time` INT(10) NOT NULL DEFAULT 0,
  `table_name` VARCHAR(200) NOT NULL,
  `table_id` BIGINT(20) NOT NULL,
  `old_data` VARCHAR(500) NULL,
  `update_data` VARCHAR(500) NULL,
  `type` TINYINT(2) NOT NULL DEFAULT 1 COMMENT '类型 1:更新, 2:创建',
  `actor_id` BIGINT(20) NOT NULL COMMENT '操作人ID',
  `actor_type` TINYINT(2) NOT NULL DEFAULT 1 COMMENT '操作人类型 1:user'
)ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT = '通用重要数据日志表';
 * 
 * @author zhaojian jesse_108@163.com
 *
 */
class DB_Log extends DB_Model{
	const TYPE_CREATE = 1; //创建
	const TYPE_UPDATE = 2; //更新
	const TYPE_DELLETE = 3; //删除
	public $tableName = 'log';
	public $useLog = false;
	
	public $logTable = 'log';
	public $defaultActorType = 0;
	public $defaultActorID = 0;
	
	public function __construct($logTable){
		if($logTable){
			$this->logTable = $logTable;
		}
	}
	
	public function log($tableName,$tableID,$oldData,$updateData,$type,$actorType,$actorID){
		if(!$tableName || !$tableID){
			return false;
		}
		
		$log = array(
			'create_time' => time(),
			'table_name' => $tableName,
			'table_id' => $tableID,
			'type' => $type,
			'actor_id' => $actorID,
			'actor_type' => $actorType,
		);
		$oldData = $oldData ? $oldData : array();
		$updateData = $updateData ? $updateData : array();
		
		foreach ($updateData as $index => $one){
			if(isset($oldData[$index]) && $one == $oldData[$index]){
				unset($updateData[$index]);
				unset($oldData[$index]);
			}
		}
		
		foreach ($oldData as $index => $one){
		    if(!isset($updateData[$index])){
		        unset($oldData[$index]);
		    }
		}
		
		if(!$oldData && !$updateData){
		    return false;
		}
		
		$oldData = $oldData ? http_build_query($oldData) : '';
		$updateData = $updateData ? http_build_query($updateData) : '';
		
		switch($type){
			case self::TYPE_CREATE:
			    $log['update_data'] = $updateData;
			    break;
			case self::TYPE_DELLETE:
				$log['old_data'] = '';
				$log['update_data'] = '';
				break;
			case self::TYPE_UPDATE:
				$log['old_data'] = $oldData;
				$log['update_data'] = $updateData;
				break;
		}
		
		$id = DB::Insert($this->logTable, $log);
		return $id;
	}
}