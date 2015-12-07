<?php
/**
 * Created by PhpStorm.
 * User: jianyong
 * Date: 15/1/28
 * Time: 17:56
 */
class Cache_Global {

    static public $gCache = array();

    static function Get($key)
    {
        settype($key, 'array');
        $data = array();
        foreach($key as $k){
            isset(self::$gCache[$k]) && ($data[$k] = self::$gCache[$k]);
        }
        return $data;
    }

    static function Set($key, $var, $flag=0, $expire=0) {
        self::$gCache[$key] = $var;
        return true;
    }
    static function Del($key, $timeout=0) {
        if (is_array($key)) {
            foreach ($key as $k) {
                if (isset(self::$gCache[$k])) unset(self::$gCache[$k]);
            }
        } else {
            if (isset(self::$gCache[$key])) unset(self::$gCache[$key]);
        }
        return true;
    }

    static function GetStringKey($str=null) {
        settype($str, 'array'); $str = var_export($str,true);
        $key = "[STR]:{$str}";
        return self::GenKey( $key );
    }

    static function GetObjectKey($tablename, $id, $pkname='id')
    {
        if (is_array($id)) {
            $id =implode('|',$id);
        }
        if ($pkname=='id') {
            $key = "[OBJ]:$tablename($id)";
        } else {
            $key = "[OBJ]:$tablename($pkname)($id)";
        }
        return self::GenKey( $key );
    }

    static function GenKey($key) {
        $hash = dirname(__FILE__);
        return md5( $hash . $key );
    }

    static function SetObject($tablename, $one, $pkname="id") {
        foreach($one AS $oone) {
            $k = self::GetObjectKey($tablename, $oone[$pkname], $pkname);
            self::Set($k, $oone);
        }
        return true;
    }

    static function GetObject($tablename, $id, $pkname="id") {
        $single = ! is_array($id);
        settype($id, 'array');
        $k = array();
        foreach($id AS $oid) {
            $k[] = self::GetObjectKey($tablename, $oid, $pkname);
        }

        $r = Util_Array::AssColumn(self::Get($k), $pkname);
        return $single ? array_pop($r) : $r;
    }

}