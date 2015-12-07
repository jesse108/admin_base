<?php
class Page{
	public static  $cssClass="page_nav";
	public static  $ulClass= "pagination margin_auto";
	public static  $showPreNum = 2;
	public static  $showNextNum = 2;
	
	public $pageSize = 0;
	public $total = 0;
	public $pageNum = 1;
	
	
	public function __construct($pageSize,$total,$pageNum = 1){
		$this->pageSize = $pageSize;
		$this->total = $total;
		$this->pageNum = $pageNum;
	}
	
	public function getTotalPage(){
		$totalPage =  intval(($this->total  -1)/ $this->pageSize) + 1;
		$totalPage = $totalPage < 1 ? 1 : $totalPage;
		return $totalPage;
	}
	
	public function getPageDetail(){
		$totalPage = $this->getTotalPage();
		$hasNext = $this->pageNum < $totalPage ? true : false;
		$hasPre = $this->pageNum > 1 ? true : false;
		
		$prePages = array();
		if($hasPre){
			for($i=self::$showPreNum; $i > 0 ; $i--){
				$curPage = $this->pageNum - $i;
				if($curPage > 0){
					$prePages[] = $curPage;
				}
			}
		}
		
		$nextPages = array();
		if($hasNext){
			for($i=1; $i <= self::$showNextNum ; $i++){
				$curPage = $this->pageNum + $i;
				if($curPage <= $totalPage){
					$nextPages[] = $curPage;
				}
			}			
		}
		
		$showFrist = $this->pageNum - self::$showPreNum - 1 > 0 ? true : false;
		$showEnd = $this->pageNum + self::$showNextNum < $totalPage ? true : false;
		
		$pageInfo = array(
			'page_num' => $this->pageNum,
			'has_next' => $hasNext,
			'has_pre' => $hasPre,
			'total_page' => $totalPage,
			'pre' => $prePages,
			'next' => $nextPages,
			'show_frist' => $showFrist,
			'show_end' => $showEnd,
		);
		return $pageInfo;
	}
	
	
	public static function GetPageInfo($pageSize,$total){
		$pageNum = isset($_REQUEST['page']) ? $_REQUEST['page'] : 0;
		$pageNum = $pageNum ? 0 + $pageNum : 1;
		$page = new Page($pageSize, $total,$pageNum);;
		
		$offset = ($pageNum - 1) * $pageSize;
		
		$pageStr = self::GetPageStr($page);
		$result = array(
			'offset' => $offset,
			'page_str' => $pageStr,
		);
		return $result;
	}
	
	public static function GetPageStr($page){
		$pageInfo = $page->getPageDetail();
		
		$fristStr ='';
		if($pageInfo['show_frist']){
			$uri = self::GetPageUri(1);
			$fristStr = "<li><a href='{$uri}'><</a></li>";
		}
		$endStr = '';
		if($pageInfo['show_end']){
			$uri = self::GetPageUri($pageInfo['total_page']);
			$endStr = "<li><a href='{$uri}'>></a></li>";
		}
		
		$preStr = '';
		if($pageInfo['pre']){
			foreach ($pageInfo['pre'] as $curPage){
				$uri = self::GetPageUri($curPage);
				$preStr .=  "<li><a href='{$uri}'>{$curPage}</a></li>";
			}
		}
		
		$nextStr = '';
		if($pageInfo['next']){
			foreach ($pageInfo['next'] as $curPage){
				$uri = self::GetPageUri($curPage);
				$nextStr .=  "<li><a href='{$uri}'>{$curPage}</a></li>";
			}
		}
		
		$uri = self::GetPageUri($page->pageNum);
		$curStr = "<li class='active'><a href='{$uri}'>{$page->pageNum}</a></li>";
		
		$totalStr = '';
		if($page->total){
			$totalStr = "<li class='disabled'><a href='javascript:void(0);'>共{$page->total}个</a></li>";
		}
		
		$class = self::$cssClass;
		$ulClass = self::$ulClass;
		$str = "
		<div class='{$class}'>
		<ul class='{$ulClass}'>
				{$fristStr}{$preStr}{$curStr}{$nextStr}{$endStr}{$totalStr}
		</ul>
		</div>
		";
		return $str;
	}
	
	public static function GetPageUri($page){
		$curUri = Utility::GetCurrentUri();
		
		$pathInfo = Utility::GetPathInfo();
		$params   = Utility::GetQueryString(true);
		
		$params['page'] = 0 + $page;
		
		$queryString    = empty($params) ? '' : ('?' . http_build_query($params));
		$uri = $pathInfo . $queryString;
		return $uri;
	}
	
}