<?php
include ('Valite.php');

$valite = new Valite();
//$valite->setImage('http://localhost:8888/laiqu/VerifyCoupon/doVerifyCoupon.php?action=validateCode&platform=juhuasuan');
$valite->setImage('http://59.151.29.121/validateCode.do');
$valite->getHec();
$valite->Draw();

?>
