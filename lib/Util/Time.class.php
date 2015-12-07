<?php
class Util_Time{
	const TIMER_PRECISION_MSEC = 1; //毫秒
	const TIMER_PRECISION_USEC = 2; //微秒
	const TIMER_PRECISION_SEC = 3;  //秒
	
	private static $timerList = array(); //计时用数组
	
	//获取当前的毫秒数  单位毫秒
	public static function GetMilliTime(){
		return floatval(self::GetMicroTime()/1000);
	}
	
	//获取当前的微秒数 单位微秒
	public static function GetMicroTime(){
		list($usec,$sec) = explode(' ', microtime());
		$usec = floatval($usec) * 1000000;
		$sec = floatval($sec) * 1000000;

		$uTime = $usec + $sec;
		return floatval($uTime);
	}
	
	
	
	//////////////计时开始
	public static function TimerStart($key = 'default'){
		$key = md5($key);
		self::$timerList[$key] = self::GetMicroTime();
	}
	
	///////计时结束
	public static function TimerStop($key = 'default',$precision = self::TIMER_PRECISION_MSEC){
		$key = md5($key);
		$startTime = self::$timerList[$key];
		if(!isset($startTime)){
			return 0;
		}
		$endTime = self::GetMicroTime();
		$duration = $endTime - $startTime;
		switch ($precision){
			case self::TIMER_PRECISION_SEC://秒
				$duration = $duration / 1000000;
				break;
			case self::TIMER_PRECISION_MSEC://毫秒
				$duration = $duration / 1000;
				break;
			case self::TIMER_PRECISION_USEC:
				break;
		}
		return $duration;
	}
	
	
	///////
	public static function getManReadTime($time,$currentTime = 0){
		$currentTime = $currentTime ? $currentTime : time();
		if(abs($time-$currentTime) <= 60){
			return "现在";
		}
		
		$currentDate = date('Y-m-d',$currentTime);
		$currentDateTime = strtotime($currentDate);
		
		$date = date('Y-m-d',$time);
		$dateTime = strtotime($date);
		
		$dateDiff = $dateTime - $currentDateTime;
		if($dateDiff >=0  && $dateDiff < 86400){
			$showDay = "今天";
		} else if($dateDiff >= 86400  && $dateDiff < 2*86400){
			$showDay = "明天";
		} else if($dateDiff > -86400  && $dateDiff < 0){
			$showDay = "昨天";
		} else {
			$showDay = $date;
		}
		
		$hour = date("H",$time);
		$hour = intval($hour);
		if($hour < 7){
			$halfDay = "早上";
		} else if($hour >=7 && $hour < 11){
			$halfDay = "上午";
		} else if($hour >= 11 && $hour < 2){
			$halfDay = "中午";
		} else if($hour >=2 && $hour < 18){
			$halfDay = "下午";
		} else {
			$halfDay = "晚上";
		}
		
		$miniute = date("i",$time);
		
		if(intval($miniute) == 0){
			$showMiniute = "";
		} else if(intval($miniute) == 15){
			$showMiniute = "一刻";
		} else if(intval($miniute) == 30){
			$showMiniute = "半";
		} else {
			$showMiniute = $miniute .'分';
		}
		
		$showTime = "{$showDay}{$halfDay}{$hour}点{$showMiniute}";
		return $showTime;
	}
	
	static public function getNextMonths($step = 1, $time = 0, $format = 'Y-m-d'){
		
		
		if(is_numeric($time)){
			$time = $time ? $time : time();
		}elseif(is_string($time)){
			$nowDateTime = date("Y-m-d");
			$time = strtotime($nowDateTime . ' ' . $time); // "+{$i} months"
		}else{
			$time = time();
		}
		
		$months = array();
		
		$nowDate 	= date("Y-m-d", $time);
		$nowMonth 	= date('Y-m', $time);
		if($step > 0){
			$months[$nowMonth] = $nowMonth;
			for($i=1; $i<$step; $i++){
				$year_month 		 = date($format, strtotime($nowDate . "+{$i} months"));
				$months[$year_month] = $year_month;
			}
		}elseif($step < 0){
			for($i=$step; $i<0; $i++){
				$year_month 		 = date($format, strtotime($nowDate . "{$i} months"));
				$months[$year_month] = $year_month;
			}
			$months[$nowMonth] = $nowMonth;
		}else{
			$months[$nowMonth] = $nowMonth;
		}
		
		return $months;
	}

    static public function GetNextMonth($step = 1, $time = 0){
        if(is_numeric($time) || !$time){
            $time = $time ? $time : time();
        }elseif(is_string($time)){
            $nowDateTime = date('Y-m-d H:i:s');
            $time = strtotime($nowDateTime . ' ' . $time); // "+{$i} months"
        }
        $nowDate = date('Y-m-d H:i:s', $time);
        return strtotime($nowDate . "+{$step} months");
    }

    static public function YmAddNowDayToTime($date){
        if($date){
            return strtotime( date('Y-m', strtotime($date)) . '-' . date('d'));
        }else{
            return strtotime(date('Y-m-d'));
        }
    }
    
    public static function GetGMTDate($time = null){
        $time = $time ? $time : time();
        date_default_timezone_set("UTC");
        return date('D, d M Y H:i:s', $time) . ' GMT';
    }
    
    public static function GetReadTime($time){
        if($time <= 0){
            return '';
        }
        $day = intval($time / 86400);
        
        $time = $time % 86400;
        $hour = intval($time/3600);
        
        $time = $time %3600;
        $minute = intval($time/60);
        
        $time = $time % 60;
        $second = $time;
        
        $manReadTime = "";
        if($day){
            $manReadTime = $manReadTime . "{$day}天";
        }
        if($hour){
            $manReadTime = $manReadTime . "{$hour}小时";
        }
        if($minute){
            $manReadTime = $manReadTime . "{$minute}分钟";
        }
        if($second){
            $manReadTime = $manReadTime . "{$second}秒";
        }
        return $manReadTime;
    }
}