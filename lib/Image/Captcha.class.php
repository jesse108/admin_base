<?php
class Image_Captcha{
	const FONT_HEIGHT = 15; //文字高度
	
	public $level = 3; //混淆程度 1:简单   5:复杂
	
	public $width = 100;
	public $height = 30;
	public $showStr;
	
	public $maxChar = 4;
	
	public $xpadding = '5%';
	public $ypadding = '5%';
	
	public function __construct(){
		
	}
	
	
	public function createImage(){
		$image = imagecreatetruecolor($this->width, $this->height);
		$bgColor = imagecolorallocate($image, 255, 255, 255);//给个白色背景
		imagefill($image, 0, 0, $bgColor);
		
		///////////背景
		for($x = 1 ; $x<=$this->width;$x++){
			$color = imagecolorallocate($image, rand(210,255), rand(210,255), rand(210,255));//给个白色背景
			imageline($image,$x,0,$x,$this->height,$color);
		}
		
// 		for($y = 1 ; $y<=$this->height;$y++){
// 			$color = imagecolorallocate($image, rand(210,255), rand(210,255), rand(210,255));//给个白色背景
// 			imageline($image,0,$y,$this->width,$y,$color);
// 		}
		/////////////画文字
		$xpadding = intval($this->xpadding) * 0.01;
		$xpadding = $this->width * $xpadding;
		
		$ypadding = intval($this->ypadding) * 0.01;
		$ypadding = $this->height * $ypadding;
		
		$startX = $xpadding;
		$endX = $this->width - $xpadding;
		
		$xLength = $endX-$startX;
		
		$startY = $ypadding;
		$endY = $this->height - self::FONT_HEIGHT - $ypadding;
		
		
		$fontsize = 6;
		$startFontColor = 0;
		$endFontColor = 125 + ($this->level - 3) *25;
		
		for($i=0; $i<$this->maxChar; $i++){
			$char = $this->showStr{$i};
			$fontColor = imagecolorallocate($image, rand($startFontColor, $endFontColor), rand($startFontColor, $endFontColor), rand($startFontColor, $endFontColor));
			
			$x =$i * $xLength/($this->maxChar) + $startX + rand(-$xpadding,$xpadding);
			$y = rand($startY,$endY);
			imagestring($image,$fontsize,$x,$y,$char,$fontColor);
		}
		
		/////////////画干扰点
		
		
		$pointNum = ($this->width * $this->height) / 15;
		$pointNum = $pointNum / 3 * $this->level;
		$startPointColor = 100 + (3-$this->level) * 25;
		$endPointColor = 250;
		
		for ($i = 0;$i<$pointNum;$i++){
			$color = imagecolorallocate($image, rand($startPointColor, $endPointColor), rand($startPointColor, $endPointColor), rand($startPointColor, $endPointColor));
			$x = rand(0,$this->width);
			$y = rand(0,$this->height);
			imagesetpixel($image,$x,$y,$color);
		}
		
		//////////画干扰线
		$lineNum = 3 + ($this->level - 3);
		
		for ($i = 0;$i<$lineNum;$i++){
			$color = imagecolorallocate($image, rand(0, 150), rand(1, 100), rand(0, 130));
			$x1 = rand(0,$this->width);
			$y1 = rand(0,$this->height);
			
			$x2 = rand(0,$this->width);
			$y2 = rand(0,$this->height);
			imageline($image,$x,$y1,$x2,$y2,$color);
		}
		
		return $image;
	}
	
	
	public function showImage(){
		$image = $this->createImage();
		header("Content-type: image/png");
		imagepng($image);
		exit;
		imagedestroy($image);
	}
	
	
}