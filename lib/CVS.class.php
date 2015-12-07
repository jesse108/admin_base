<?php
class CVS{
    public $dataArray;
    public $titleInfo;
    
    public function __construct(){
        
    }
    
    
    public function setArrayData($data,$setTitle = true){
        $this->data = $data;
        if($setTitle && !$this->titleInfo){
            $titleInfo = array();
            foreach (current($data) as $key => $value){
                $titleInfo[$key] = $key;
            }
            $this->titleInfo = $titleInfo;
        }
    }
    
    public function setTitle($titleInfo){
        $this->titleInfo = $titleInfo;
    }
}