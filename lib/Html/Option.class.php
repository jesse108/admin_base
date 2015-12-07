<?php
class Html_Option{
    
    public static function CreateOption($datas,$valueKey,$showKey,$selectValue = null,$emptyStr = null){
        $optionStr = '';
        
        if($emptyStr){
            $optionStr = "<option value=''>{$emptyStr}</option>";
        }
        
        foreach ($datas as $key => $value){
            $curValue = $valueKey ? $value[$valueKey] : $key;
            $curName = $showKey? $value[$showKey] : $value;
            $curSelect = '';
            if($selectValue !== null && $selectValue == $curValue){
                $curSelect = 'selected';
            }
            
            $curOption = "<option value='{$curValue}' {$curSelect}>{$curName}</option>";
            $optionStr .= $curOption;
        }
        
        return $optionStr;
    }
}