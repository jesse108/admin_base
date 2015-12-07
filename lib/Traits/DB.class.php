<?php
trait Traits_DB {

    static public function dbFetch($ids,$key = null){

        if(!$ids) return null;

        $db   = self::dbObject();
        $data = $db->fetch($ids, $key);

        return $data;
    }

    static public function dbFetchCache($ids,$key = null, $is_unique = false){

        if(!$ids) return null;

        $db   = self::dbObject();
        $data = $db->fetchCache($ids, $key, $is_unique);

        return $data;
    }

    static public function dbCreate($data, $duplicateCondition = null,$actorType = 0,$actorID = 0){

        if(!$data) return null;

        $db = self::dbObject();
        $id = $db->create($data, $duplicateCondition,$actorType,$actorID);

        if(!$id){
            self::dbError($db->error);
        }

        return $id;
    }

    static public function dbUpdate($condition, $data, $oldCondition = null,$actorType = 0,$actorID = 0){

        if(empty($condition) || empty($data)) return null;

        $db  = self::dbObject();
        $ret = $db->update($condition, $data, $oldCondition, $actorType, $actorID);

        return $ret;
    }

    static public function dbDel($condition){

        if(empty($condition)) return null;

        $db  = self::dbObject();
        $ret = $db->del($condition);

        if(!$ret){
            self::dbError($db->error);
        }

        return $ret;
    }

    static public function dbGet($condition, $option = array()){

        if(empty($condition)) return null;

        $db   = self::dbObject();
        $data = $db->get($condition, $option);

        return $data;
    }

    static public function dbCount($condition, $sum = null){

        if(empty($condition)) return 0;

        $db    = self::dbObject();
        $total = $db->count($condition, $sum);

        return $total;
    }


    static private function dbObject(){

        $db = DB_Manage::createDBObj(self::$table_name);

        return $db;
    }

    static public function dbError($error = ''){

        if(!$error) return null;

        if(property_exists((new self), 'error')) {
            self::$error = $error;

            return false;
        }

        throw new Exception($error);
    }


    static public function dbRow($condition, $option = array()){

        $option['one'] = true;

        return self::dbGet($condition, $option);
    }

    static public function dbLastRow($condition, $option = array()){

        $option['one'] = true;
        $option['order'] = 'order by id desc';

        return self::dbGet($condition, $option);
    }

    static public function dbRandomId($pkName = 'id'){

        $pkName     = $pkName ? $pkName : 'id';
        $randomId   = Util_String::GenRandomStr(8, Util_String::CHAR_NUM);

        $db = self::dbObject();
        if($db->exsits(array( $pkName => $randomId ), $pkName)){
            return null;
        }else{
            return $randomId;
        }
    }

    static public function dbExists($condition, $column = 'id'){
        $db = self::dbObject();
        return $db->exsits($condition, $column);
    }
}
?>