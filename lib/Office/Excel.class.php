<?php
/**
 * 简单的 Excel 实现类
 * 依赖于 PHPExcel
 * 
 * @author Jesse
 *
 */
class Office_Excel {
	const TYPE_EXCEL5 = 'Excel5';
	const TYPE_EXCEL2007 = 'Excel2007';
	const TYPE_EXCEL2003XML = 'Excel2003XML';
	const TYPE_HTML = 'HTML';
	const TYPE_CSV = 'CSV';
	
	public static $extMap = array(
		self::TYPE_CSV => 'csv',
		self::TYPE_EXCEL5 => 'xls',
		self::TYPE_EXCEL2007=>'xlsx',
		self::TYPE_HTML=>'html',
		self::TYPE_EXCEL2003XML => 'xls',
		'default' => 'xls',
	);
	
	public $phpExcel;
	public $data;
	public $titleInfo;
	public $filePath;
	
	public function __construct(){
	    $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
	    $cacheSettings = array(
	         ' memoryCacheSize '  => '8MB'
	    );
	    PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
	    
		$this->phpExcel = new PHPExcel();
	}
	
	public function load($filePath){
		$this->filePath = $filePath;
		$this->phpExcel = PHPExcel_IOFactory::load($filePath);
		$data = $this->phpExcel->getActiveSheet()->toArray(null,true,true);
		$this->setArrayData($data,false);
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

    public function mergeCellsInfo($mergeInfo){
        if(empty($mergeInfo)) return false;

        $sheet = $this->phpExcel->getActiveSheet();
        foreach($mergeInfo as $one){
            $sheet->mergeCells($one);
        }
        return true;
    }

    public function titleInfoCenter(){

        $titleDetail = array();
        if(isset($this->titleInfo[0])){
            $rowTotal = count($this->titleInfo);
            $colTotal = count($this->titleInfo[0]);
        }else{
            $rowTotal = 1;
            $colTotal = count($this->titleInfo);
        }

        for($rowInx = 1; $rowInx <= $rowTotal; $rowInx++){
            for($i = 1; $i <= $colTotal; $i++){
                $col = chr(64 + $i) . '' . $rowInx;
                $objStyleA5 = $this->phpExcel->getActiveSheet()->getStyle($col);
                $objAlignA5 = $objStyleA5->getAlignment();
                $objAlignA5->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objAlignA5->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            }
        }
    }

    public function downloadMergeTable($name,$type = self::TYPE_EXCEL5,$return = false){
        $data = $this->data;
        $titleInfo = $this->titleInfo;
        if(!$data){
            return false;
        }

        $showArray = array();
        if($titleInfo){
            foreach($titleInfo as $rowTitleInfo){
                $row = array();
                foreach ($rowTitleInfo as $key => $title){
                    $row[] = $title;
                }
                $showArray[] = $row;
            }

            foreach ($data as $one){
                $row = array();
                foreach ($titleInfo[0] as $key => $title){
                    $row[] = $one[$key];
                }
                $showArray[] = $row;
            }
        } else {
            $showArray = $data;
        }

        return $this->_downloadExcel($name, $showArray, $type, $return);
    }

	public function download($name,$type = self::TYPE_EXCEL5,$return = false){
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

        return $this->_downloadExcel($name, $showArray, $type, $return);
	}

    private function _downloadExcel($name, $showArray, $type = self::TYPE_EXCEL5, $return = false ){
        $objPHPExcel = $this->phpExcel;
        $worksheet = $objPHPExcel->getActiveSheet();

        //全部设置成字符串

        $colNum = $rowNum = 0;
        foreach ($showArray as $array){
            $colNum = 0;
            $rowNum++;

            foreach ($array as $val){
                $worksheet->setCellValueExplicitByColumnAndRow($colNum,$rowNum,$val,PHPExcel_Cell_DataType::TYPE_STRING);
                $colNum++;
            }
        }

        //$worksheet->fromArray($showArray);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $type);

        $names = explode('.', $name);
        if(count($names) < 2){//无后缀
            $name = $name . '.' . self::GetExt($type);
        }

        if($return){
            ob_start();
            $objWriter->save('php://output');
            $content =ob_get_clean();
            return $content;
        } else {
            $ua = $_SERVER["HTTP_USER_AGENT"];
            if (preg_match("/Firefox/", $ua)) {
                header("Content-Disposition: attachment; filename*=\"utf8''" . $name . '"');
            } else if (preg_match("/MSIE/", $ua) || preg_match("/Trident/", $ua)) {
                $encoded_filename = rawurlencode($name);
                header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
            } else {
                header('Content-Disposition: attachment; filename="' . $name . '"');
            }
            header('Cache-Control: max-age=0');
            header ('Pragma: public'); // HTTP/1.0
            header('Content-Type: application/vnd.ms-excel');
            header("Content-Type: application/octet-stream");
            header('Content-Type: application/x-download');
            $objWriter->save('php://output');
            exit;
        }
    }

	public static function GetExt($type){
		$ext = self::$extMap[$type];
		$ext = $ext ? $ext : self::$extMap['default'];
		return $ext;
	}
	
	public function getData(){
		return $this->data;
	}
}