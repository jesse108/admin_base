<?php
class Util_Binary{
    public static function GetCode($bit){
        $bit = intval($bit);
        $bit = $bit - 1;
        return 1 << $bit;
    }
    
    public static function GetBinaryValue($code,$bit){
        $value = $code & $bit;
        return $value ? 1 : 0;
    }
    
    public static function SetBinaryValue($code,$bit,$value){
        $bit  = 1 << ($bit - 1);
        if($value){
            $code = $code | $bit;
        } else {
            $code = $code ^ $bit;
        }
        return $code;
    }
}