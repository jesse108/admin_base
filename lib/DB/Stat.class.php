<?php
class DB_Stat {

    static public $records = array();

    static public function push($sql_record){
        array_push(self::$records, $sql_record);
    }

    static public function getRecords(){
        return self::$records;
    }

}