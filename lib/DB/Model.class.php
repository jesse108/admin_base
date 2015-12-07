<?php
class DB_Model{
	public $tableName = '';
	public $error = '';
	public $readDB ='ro';
	public $writeDB = 'rw';
	public $primaryKey = 'id';
	public $logTable = '';
	public $useLog = true;
	public $hasCreateTime = false;
	public $hasUpdateTime = false;
	
	
	public function create($condition,$duplicateCondition = null,$actorType = 0,$actorID = 0){
		$table = $this->tableName;
		if(!$table){
			$this->error = 'Table name is null';
			return false;
		}
		
		if($this->hasCreateTime){
			$condition['create_time'] = $condition['create_time'] ? $condition['create_time'] : time();
		}
		
		if($this->hasUpdateTime){
			$condition['update_time'] = $condition['update_time'] ? $condition['update_time'] : time();
		}
		
		$insertID = DB::Insert($table, $condition,$duplicateCondition,$this->writeDB);
		if(!$insertID){
			$this->error = DB::$error;
		}
		
		$this->log($insertID, '', $condition, DB_Log::TYPE_CREATE,$actorType,$actorID);
		return $insertID;
	}
	
	public function get($condition,$option = array(),$dbType = null){
		$table = $this->tableName;
		$dbType = $dbType ? $dbType : $this-> readDB;
		if(!$table){
			$this->error = 'Table name is null';
			return false;
		}
		$result = DB::LimitQuery($table,$condition,$option,$dbType);
		if(!$result){
			$this->error = DB::$error;
		}
		return $result;
	}
	
	public function update($condition,$updateRow,$oldCondition = null,$actorType = 0,$actorID = 0){
		if(!is_array($condition)){
		    $tableID  = $condition;
			$condition = array($this->primaryKey => $condition);
		}
		$tableID = $tableID ? $tableID : $condition[$this->primaryKey];
		$tableID = $tableID ? $tableID : $oldCondition[$this->primaryKey];
		
		if($this->hasUpdateTime){
			$updateRow['update_time'] = $updateRow['update_time'] ? $updateRow['update_time'] : time();
		}
		
		if($tableID && !$oldCondition && $this->useLog && $this->logTable){
		    $oldCondition = $this->fetch($tableID);
		}
		
		$result = DB::Update($this->tableName, $condition, $updateRow,$this->writeDB);
		if($result){
			$this->log($tableID, $oldCondition, $updateRow, DB_Log::TYPE_UPDATE,$actorType,$actorID);
		} else {
			$this->error = DB::$error;
		}
		return $result;
	}
	
	public function del($condition){
		if(!is_array($condition)){
			$condition = array($this->primaryKey => $condition);
		}

		$result = DB::Delete($this->tableName, $condition);
		
		if(!$result){
			$this->error = DB::$error;
		}
		return $result;
	}
	
	public function count($condition,$sum =null){
		$count  = DB::Count($this->tableName, $condition);
		$count = intval($count);
		return $count;
	}
	
	
	public function fetch($id,$key = null){
		if(!$id){
			return false;
		}
		
		$key = $key ? $key : $this->primaryKey;
		

		
		if(Util_Array::IsArrayValue($id)){
			$id = array_values($id);
			$one = false;
		} else {
			$one = true;
		}
		
		$condition = array(
				$key => $id,
		);
		
		$option =array('one' => $one);
		
		$result = DB::LimitQuery($this->tableName,$condition,$option,$this->readDB);
		if(!$result){
			$this->error = DB::$error;
		}
		
		if(is_array($id) && $this->primaryKey){
			$result = Util_Array::AssColumn($result, $this->primaryKey);
		}
		return $result;
	}
	
	public function exsits($condition,$column = ''){
		$column = $column ? $column : $this->primaryKey;
		return DB::Exists($this->tableName, $condition,$column,$this->readDB);
	}
	
	
	public function log($tableID,$oldData,$updateData,$type,$actorType = 0,$actorID = 0){
		if(!$this->logTable || !$this->useLog){
			return false;
		}
		
		$log = new DB_Log($this->logTable);
		
		if(!$actorType && !$actorID){
			$currentUser = $this->getCurrentUser();
			if($currentUser){
				$actorType = $currentUser['actor_type'];
				$actorID = $currentUser['actor_id'];
			}
		}
		return $log->log($this->tableName, $tableID, $oldData, $updateData, $type, $actorType, $actorID);
	}
	
	/**
	 * 获取当前用户， 需要覆盖使用
	 */
	public function getCurrentUser(){
		return false;
	}
	
	
	////////////Search

    public function fetchCache($ids=array(),$k='id', $is_unique=false)
    {
        if ( empty($ids) || !$ids ) return array();

        $k = $k ? $k : 'id';
        $single = is_array($ids) ? false : true;

        settype($ids, 'array'); $ids = array_values($ids);
        $ids = array_diff($ids, array(NULL));

        if ($k=='id') {
            $r = $this->_fetch($ids);
            return $single ? array_pop($r) : $r;
        }

        if ($is_unique) {
            // $k is primary key
            $r = $this->_fetch($ids, $k);
            return $single ? array_pop($r) : $r;
        }

        $result = DB::LimitQuery($this->tableName,array(
            $k => $ids,
        ), array(
            'one' => $single,
        ), $this->readDB);

        if ( $single ) { return $result; }
        return $result;
    }

    private function _fetch($ids=array(),$pkname="id") {

        $r = Cache_Global::GetObject($this->tableName, $ids, $pkname);
        $r = $r ? $r : array();

        $diff = array_diff($ids, array_keys($r));
        if(!$diff) return $r;

        $rr = DB::LimitQuery($this->tableName,array(
            $pkname => array_values($diff),
        ), array(
            'one' => false,
        ), $this->readDB);

        Cache_Global::SetObject($this->tableName, $rr, $pkname);
        $r = array_merge($r, $rr);

        return Util_Array::SortArray($r, $ids, $pkname);
    }
	
}