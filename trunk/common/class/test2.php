<?php
// author email: ugg.xchj@gmail.com
// 本代码仅供学习参考，不提供任何技术保证。
// 切勿使用本代码用于非法用处，违者后果自负。

include("./decaptcha/Valite.php");

$valite = new Valite();

//http://localhost:8888/laiqu/verifyCoupon/doVerifyCoupon.php?action=validateCode&platform=juhuasuan
//$valite->bmp2jpeg("queaa.bmp");
$valite->setImage("http://localhost:8888/laiqu/verifyCoupon/doVerifyCoupon.php?action=validateCode&platform=juhuasuan");
$valite->getHec();
//$valite->filterInfo();
//$valite->Draw();
echo "\n 结果是：";
echo $valite->run();


?>

