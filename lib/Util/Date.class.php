<?php
class Util_Date {
	
	public static function ChinessDay($dayNum){
		$dayNum = intval($dayNum);
		$dayStr = '';
		
		switch ($dayNum){
			case 1:
				$dayStr = '一';
				break;
			case 2:
				$dayStr = '二';
				break;
			case 3:
				$dayStr = '三';
				break;
			case 4:
				$dayStr = '四';
				break;
			case 5:
				$dayStr = '五';
				break;
			case 6:
				$dayStr = '六';
				break;
			case 7:
				$dayStr = '日';
				break;
			default:
				$dayStr = '未知';
				break;
		}
		return  $dayStr;
	}
	
	public static function NextMonthTime(){
		return mktime(0,0,0,date('m') + 1,1,date('Y'));
	}

    public static function GetYears(){
        $year  = date("Y") + 1;
        $start = 2014;

        $years = array();
        for($start; $start <= $year; $start++){
            $years[$start] = $start;
        }
        return $years;
    }

    public static function GetMonths(){
        $months = array();

        for($i = 1; $i <= 12; $i++){
            $months[$i] = $i;
        }

        return $months;
    }

    public static function DateGenerator($days = 30){

        $startTime = time() - $days * 86400;

        $date = '';
        for($i = 1; $i <= $days; $i++){
            $date = date('Y-m-d', $startTime + $i * 86400);
            yield $date;
        }
    }

    public static function DateTimeGenerator($startTime, $endTime, $format = 'Y-m-d'){

        if(!$endTime || !$startTime || $endTime < $startTime) {
            return array();
        }

        $vars = array();
        for($i = $startTime; $i <= $endTime; $i+=86400){
            $date = date($format, $i);
            $vars[] = $date;
        }
        $vars = empty($vars) ? array() : array_unique($vars);
        return $vars;
    }
}