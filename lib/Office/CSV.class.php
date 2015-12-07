<?php
class Office_CSV{
    public $data;
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
	
	public function download($fileName){
	    $data = $this->data;
	    $titleInfo = $this->titleInfo;
	    if(!$data){
	        return false;
	    }
	
	    $showArray = array();
	    if($titleInfo){
	        $row = array();
	        foreach ($titleInfo as $key => $title){
	            $row[] = $title;
	        }
	        $showArray[] = $row;
	
	        foreach ($data as $one){
	            $row = array();
	            foreach ($titleInfo as $key => $title){
	                $row[] = $one[$key];
	            }
	            $showArray[] = $row;
	        }
	    } else {
	        $showArray = $data;
	    }
	    
	    $fileNames = explode('.', $fileName);
	    if($fileName[count($fileName) - 1] != 'csv'){
	        $fileName .= ".csv";
	    }
	    
	    $this->_download($showArray, $fileName);
	}
	
	private function _download($data,$fileName){
        header("Content-Disposition: attachment;filename={$fileName}");
        header('Cache-Control: max-age=0');
        header ('Pragma: public'); // HTTP/1.0
        header("Content-type: text/csv");
	    $handle = fopen("php://output", "w+");
	    foreach ($data as $row){
	        $rowStr = implode(',', $row);
	        $rowStr =iconv('utf-8','gb2312',$rowStr);//转换编码
	        $rowStr .= "\n";
	        fwrite($handle, $rowStr);
	    }
	    
	    exit;
	}
}