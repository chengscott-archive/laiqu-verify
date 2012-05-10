<?php
// 针对聚划算平台，进行修改

define('WORD_WIDTH',10);
define('WORD_HIGHT',13);
define('OFFSET_X',7);
define('OFFSET_Y',3);
define('WORD_SPACING',3);

class valite
{
	public function setImage($Image)
	{
		$this->ImagePath = $Image;
	}
	public function getData()
	{
		return $data;
	}
	public function getResult()
	{
		return $DataArray;
	}
	public function getHec()
	{
		$res = imagecreatefromjpeg($this->ImagePath);
		$size = getimagesize($this->ImagePath);
		$data = array();
		for($i=0; $i < $size[1]; ++$i)
		{
			for($j=0; $j < $size[0]; ++$j)
			{
				$rgb = imagecolorat($res,$j,$i);
				$rgbarray = imagecolorsforindex($res, $rgb);
                if($rgbarray['red'] < 94 || $rgbarray['green']<94
                || $rgbarray['blue'] < 94)
                {
                    $data[$i][$j]=1;
                }else{
                    $data[$i][$j]=0;
                }

			}
		}

        // 如果1的周围数字不为1，修改为了0
		for($i=0; $i < $size[1]; ++$i)
		{
			for($j=0; $j < $size[0]; ++$j)
			{
				$num = 0;
				if($data[$i][$j] == 1)
				{
					// 上
					if(isset($data[$i-1][$j])){
						$num = $num + $data[$i-1][$j];
					}
					// 下
					if(isset($data[$i+1][$j])){
						$num = $num + $data[$i+1][$j];
					}
					// 左
					if(isset($data[$i][$j-1])){
						$num = $num + $data[$i][$j-1];
					}
					// 右
					if(isset($data[$i][$j+1])){
						$num = $num + $data[$i][$j+1];
					}
					// 上左
					if(isset($data[$i-1][$j-1])){
						$num = $num + $data[$i-1][$j-1];
					}
					// 上右
					if(isset($data[$i-1][$j+1])){
						$num = $num + $data[$i-1][$j+1];
					}
					// 下左
					if(isset($data[$i+1][$j-1])){
						$num = $num + $data[$i+1][$j-1];
					}
					// 下右
					if(isset($data[$i+1][$j+1])){
						$num = $num + $data[$i+1][$j+1];
					}
				}
				if($num == 0){
					$data[$i][$j] = 0;
				}
			}
		}


		$this->DataArray = $data;
		$this->ImageSize = $size;

	}
	public function run()
	{
		$result="";
		// 查找6个数字
		$data = array("","","","","","");
		for($i=0;$i<6;++$i)
		{
			$x = ($i*(WORD_WIDTH+WORD_SPACING))+OFFSET_X;
			$y = OFFSET_Y;
			for($h = $y; $h < (OFFSET_Y+WORD_HIGHT); ++ $h)
			{
				for($w = $x; $w < ($x+WORD_WIDTH); ++$w)
				{
					$data[$i].=$this->DataArray[$h][$w];
				}
			}
		}

		// 进行关键字匹配
		foreach($data as $numKey => $numString)
		{
			$max=0.0;
			$num = 0;
			foreach($this->Keys as $key => $value)
			{
				$percent=0.0;
				similar_text($value, $numString,$percent);
				if(intval($percent) > $max)
				{
					$max = $percent;
					$num = $key;
					if(intval($percent) > 95)
						break;
				}
			}
			$result.=$num;
		}
		$this->data = $result;
		// 查找最佳匹配数字
		return $result;
	}

	public function Draw()
	{
		for($i=0; $i<$this->ImageSize[1]; ++$i)
		{
	        for($j=0; $j<$this->ImageSize[0]; ++$j)
		    {
			    echo $this->DataArray[$i][$j];
	        }
		    echo "\n";
		}
	}
    
    public function getEachKey()
    {
        $keys = array();
		for($i=0;$i<6;++$i)
		{
            $key = "";
			$x = ($i*(WORD_WIDTH+WORD_SPACING))+OFFSET_X;
			$y = OFFSET_Y;
			for($h = $y; $h < (OFFSET_Y+WORD_HIGHT); ++ $h)
			{
				for($w = $x; $w < ($x+WORD_WIDTH); ++$w)
				{
					$key.=$this->DataArray[$h][$w];
				}
			}
            $keys[$i] = $key;
		}        
        return $keys;
    }
	public function __construct()
	{
		$this->Keys = array(
		'1'=>'0001110000011111000001111100000000110000000011000000001100000000110000000011000000001100000000110000000011000001111111100111111110',
		'2'=>'0111110000111111100010000011000000001100010000110000000110000000110000010110000001110000000110000001110000000011111111001111111100',
		'3'=>'0111110000111111110010000011000000001100000001100001111100000111111000000001110000000011000000001100100001110011111110000111110000',
		'4'=>'0000011000000011100000001110000001111000001101100000110110000110011000011001100011111111101111111110000001100000000110000000011000',
		'5'=>'1111111100111111110011000000001100000000110000000011111000001111111000000001110000000011000000001100100001110011111110000111110000',
		'6'=>'0001111000001111110001100001000110000000110000000011011110001111111100111000111011000001101100000110011000111001111111000001111000',
		'7'=>'0111111110011111111000000001100000000100000000110000000110000000010000000011000000001000000001100000000110000000110000000011000000',
		'8'=>'0011111000011111110001100011000110001100011100100000111110000011111000011001110011000001101100000110111000111001111111000011111000',
        '9'=>'0011110000011111110011100011001100000110110000011011100011100111111110001111011000000001100000001100010000110001111110000011110000',
        'A'=>'0001110000000111000000110110000011011000001101100001100011000110001100011000110011111111101111111110110000011010000000111000000011',
        'B'=>'0110110000101110100011000110001100011000110011000011011000001111110000110001100011000011001100001100010001110011111110001110110000',
        'C'=>'0001111100001111011001110000101110000000110000000011000000001100000000110000000011000000001110000000011100001000111111100001111100',
        'D'=>'1111111000111111110011000011101100000111110000001111000000111100000011110000001111000000111100000110110000111011111111001111111000',
        'E'=>'1111111100111111110011000000001100000000110000000011111110001111111000110000000011000000001100000000110000000011111111001111111100',
        'F'=>'1111111000111111100011000000001100000000110000000011000000001111110000111111000011000000001100000000110000000011000000001100000000'
	);
	}
	protected $ImagePath;
	protected $DataArray;
	protected $ImageSize;
	protected $data;
	protected $Keys;
	protected $NumStringArray;

}
?>
