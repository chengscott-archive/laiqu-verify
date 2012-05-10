<?php
// author email: ugg.xchj@gmail.com
// 本代码仅供学习参考，不提供任何技术保证。
// 切勿使用本代码用于非法用处，违者后果自负。

include("Valite.php");

$valite = new Valite();
$data = array("4dnq.bmp"=>array('4','d','v','n','q'),
"r7dyq.bmp"=>array('r','7','d','y','q'),
"anfdd.bmp"=>array('a','n','f','d','d'),
"ec6uu.bmp"=>array('e','c','6','u','u'),
"hw6kg.bmp"=>array('h','w','6','k','g'),
"mwq7a.bmp"=>array('m','w','q','7','a'),
"n6wnw.bmp"=>array('n','6','w','n','w'),
"nmndu.bmp"=>array('n','m','n','d','u'),
"pwrkk.bmp"=>array('p','w','r','k','k'),
"quzpd.bmp"=>array('q','u','z','p','d'),
"wncdx.bmp"=>array('w','n','c','d','x'),
"ydndw.bmp"=>array('y','d','n','d','w'),
"queaa.bmp"=>array('q','u','e','a','a'),
"zacfd.bmp"=>array('z','a','c','f','d'));

foreach($data as $key => $value)
{
//	print_r($key);
//	echo "\n";
	$valite->bmp2jpeg($key);
	$valite->setImage($key.".jpeg");
	$valite->getHec();
	$valite->filterInfo();
	$valite->study($value);
    $valite->Draw();
}


$valite->bmp2jpeg("queaa.bmp");
$valite->setImage("queaa.bmp".".jpeg");
$valite->getHec();
$valite->filterInfo();
//$valite->Draw();
echo "\n 结果是：";
echo $valite->run();


/*
$data = $valite->getData();

foreach($data as $key=>$value)
{
	echo "=============$data===============\n";
	foreach($value as $lkey => $lvalue)
	{
		$str = implode("",$lvalue);
		echo $str."\n";
	}
	echo "===============================\n";
}
*/
?>
